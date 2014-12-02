<?php

/**
 * PyroCMS Forums Library
 *
 * @author Dan Horrigan
 */
class Forums_lib
{
    private $CI;

    private $categoriesTable;
    private $postsTable;
    private $forumsTable;
    private $subscriptionsTable;

    public function  __construct()
    {
        $this->CI =& get_instance();
    }

    public function delete_category($id)
    {
        $this->_setupModels();

        $rv = true;

        // delete all the subscriptions on posts of a forum belonging to this category
        $rv = $rv && $this->delete_subscriptions_by_category($id);
        // delete all posts by category
        $rv = $rv && $this->delete_posts_by_category($id);
        // delete the forums
        $rv = $rv && $this->CI->forums_m->delete_by('category_id', $id);
        // delete the category
        $rv = $rv && $this->CI->categories_m->delete($id);

        // return true or false, it will be false if any of the above calls fails
        // TODO: this might suck
        return $rv;
    }

    public function delete_subscriptions_by_category($id)
    {
        $this->_setupModels();

        // get the post ids of the subscriptions we want to delete
        $data = $this->CI->posts_m
                    ->select( $this->postsTable . '.id')
                    ->join( $this->forumsTable, $this->forumsTable . '.id = ' . $this->postsTable . '.forum_id')
                    ->join( $this->categoriesTable, $this->categoriesTable . '.id = ' . $this->forumsTable . '.category_id')
                    ->where( $this->forumsTable . '.category_id', $id)
                    ->get_all();

        if(count($data) <= 0)
        {
            // nothing here, so yay
            return true;
        }

        $posts = array();

        foreach($data as $row) { $posts[] = $row->id; }

        // delete subscriptions by posts ids
        return $this->CI->subscriptions_m->delete_subscriptions_by_posts($posts);
    }

    public function delete_subscriptions_by_forum($id)
    {
        $this->_setupModels();

        // get the posts ids of the subscriptions we want to delete
        $data = $this->CI->posts_m
                    ->select( $this->postsTable . '.id')
                    ->join( $this->forumsTable, $this->forumsTable . '.id = ' . $this->postsTable . '.forum_id')
                    ->where( $this->forumsTable . '.id', $id)
                    ->get_all();

        if(count($data) <= 0)
        {
            // nothing here, so yay
            return true;
        }

        $posts = array();

        foreach($data as $row) { $posts[] = $row->id; }

        // delete subscriptions by post ids
        return $this->CI->subscriptions_m->delete_subscriptions_by_posts($posts);
    }

    public function delete_posts_by_category($id)
    {
        $this->_setupModels();

        // get the posts ids of the posts we want to delete
        $data = $this->CI->posts_m
                    ->select( $this->postsTable . '.id')
                    ->join( $this->forumsTable, $this->forumsTable . '.id = ' . $this->postsTable . '.forum_id')
                    ->join( $this->categoriesTable, $this->categoriesTable . '.id = ' . $this->forumsTable . '.category_id')
                    ->where( $this->forumsTable . '.category_id', $id)
                    ->get_all();

        if(count($data) <= 0)
        {
            // nothing here, so yay
            return true;
        }

        $posts = array();

        foreach($data as $row) { $posts[] = $row->id; }

        // delete the posts
        return $this->CI->posts_m->delete_many($posts);
    }

    // load all models and "cache" the table name for easier access
    private function _setupModels()
    {
        $this->CI->load->model('categories_m');
        $this->CI->load->model('forums_m');
        $this->CI->load->model('subscriptions_m');
        $this->CI->load->model('posts_m');

        $this->categoriesTable = $this->CI->categories_m->table_name();
        $this->postsTable = $this->CI->posts_m->table_name();
        $this->forumsTable = $this->CI->forums_m->table_name();
        $this->subscriptionsTable = $this->CI->subscriptions_m->table_name();
    }

    public function notify_report($reply)
    {
        //return false;

        $this->CI->load->library('email');
        $this->CI->load->helper('url');

        if ($reply->parent_id == 0)
        {
          $text_body = '<b>Reported Post:</b> ' . anchor('forums/topics/view/' . $reply->id) . '<br /><br />';
        }
        else
        {
          $text_body = '<b>Reported Post:</b> ' . anchor('forums/topics/view/' . $reply->parent_id . '#' . $reply->id) . '<br /><br />';
        }

        $text_body .= "Reported by: " . $this->CI->current_user->display_name . " (" . $this->CI->current_user->id . ")<br /><br />";

        $text_body .= '<strong>Post contents:</strong><br />';
        $text_body .= parse($reply->content);

        $this->CI->email->clear();
        $this->CI->email->from(Settings::get('server_email'), Settings::get('site_name') . " - " . $this->CI->config->item('forums_title'));
        $this->CI->email->to(Settings::get('contact_email'));

        $this->CI->email->subject('Forum Post Reported');
        $text_body = 'Post reported on Forums. <b>Action Required</b><br /><br />' . $text_body;

        $this->CI->email->message($text_body);
        $this->CI->email->send();

        return true;
    }

    public function notify_reply($recipients, $reply)
    {
        $this->CI->load->library('email');
        $this->CI->load->helper('url');
        $person = new stdClass();
        
        foreach($recipients as $person)
        {
            // No need to email the user that entered the reply
            if($person->email == $this->CI->session->userdata('email'))
            {
                continue;
            }
            $text_body = 'View the reply here: ' . anchor('forums/posts/view_reply/' . $reply->id) . '<br /><br />';
            $text_body .= '<strong>Message:</strong><br />';
            $text_body .= parse($reply->content);

            $this->CI->email->clear();
            $this->CI->email->from(Settings::get('server_email'), $this->CI->config->item('forums_title'));
            $this->CI->email->to($person->email);

            $this->CI->email->subject('Subscription Notification: ' . $reply->title);
            $text_body = 'Reply to <strong>"' . $reply->title . '"</strong>.<br /><br />' . $text_body;
            $text_body .= "<br /><br />Click here to unsubscribe from this topic: " . anchor('forums/unsubscribe/' . $person->id . '/' . $reply->topic_id);

            $this->CI->email->message($text_body);
            $this->CI->email->send();
        }
    }

    public function get_recipients($topic_id)
    {
        $this->CI->load->model('subscriptions_m');

        $recipient_count = 0;
        $recipients = array();
        $subscriptions = $this->CI->subscriptions_m->get_many_by(array('post_id' => $topic_id));
        foreach($subscriptions as& $sub)
        {
            $this->CI->db->or_where('users.id', $sub->user_id);
            $recipient_count++;
        }

        // If there are recipients
        if($recipient_count > 0)
        {
            $this->CI->db->select('email,id');
            return $this->CI->db->get($this->CI->ion_auth_model->tables['users'])->result();
        }

        // If no recipients then return an empty array
        return array();
    }

}