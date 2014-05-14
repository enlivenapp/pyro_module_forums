<?php

/**
 * PyroCMS Forums Library
 *
 * @author Dan Horrigan
 */
class Forums_lib
{
  private $CI;

  public function  __construct()
  {
    $this->CI =& get_instance();
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
    $recipient_count = 0;
    $recipients = array();
    $subscriptions = $this->CI->forum_subscriptions_m->get_many_by(array('topic_id' => $topic_id));
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
?>
