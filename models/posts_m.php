<?php
class Posts_m extends ForumsBase_m {

    protected $_table = 'posts';
    protected $_stream = 'posts';

    /**
    * Count Topics in Forum
    *
    * How many topics (posts which have no parent / are not a reply to anything) are in a forum.
    * 
    * @access       public
    * @param        int 	[$forum_id] 	Which forum should be counted
    * @return       int 	Returns a count of how many topics there are
    * @package      forums
    */
    public function count_topics_in_forum($forum_id)
    {
        return $this->count_by(array(
            'parent_id' => 0,
			'forum_id' => $forum_id
		));		
    }

    /**
    * Count Replies in Forum
    *
    * How many replies have been made to topics in a forum.
    * 
    * @access       public
    * @param        int 	[$forum_id] 	Which forum should be counted
    * @return       int 	Returns a count of how many replies there are
    * @package      forums
    */
    public function count_replies_in_forum($forum_id)
    {
        return $this->count_by(array('parent_id >' => 0, 'forum_id' => $forum_id));
    }

    /**
    * Count Posts in Topic
    *
    * How many posts are in a topic.
    * 
    * @access       public
    * @param        int 	[$forum_id] 	Which topic should be counted
    * @return       int 	Returns a count of how many posts there are
    * @package      forums
    */
    public function count_posts_in_topic($topic_id)
    {
        return $this->select('id')->where('id', $topic_id)->or_where('parent_id', $topic_id)->count_all_results($this->table_name());
    }

    /**
    * Count Posts for user
    *
    * How many posts have been made by a user
    *
    * @access       public
    * @param        int 	[$forum_id] 	Which forum should be counted
    * @return       int 	Returns a count of how many replies there are
    * @package      forums
    */
    public function count_user_posts($user_id)
    {
        return $this->count_by(array('created_by' => $user_id));
    }

    /**
    * Count Prior Posts
    *
    * How many posts were before this one.  Used for pagination.
    *
    * @access       public
    * @param        int 	[$topic_id] 	Which topic
    * @param        int 	[$reply_time] 	Reply time o compair
    * @return       int
    * @package      forums
    */
    public function count_prior_posts($topic_id, $reply_time)
    {
        return $this->count_by(array('parent_id' => $topic_id, 'created <' => $reply_time)) + 1;
    }

    /**
    * Add a view to a topic
    *
    *
    * @access       public
    * @param        int 	[$topic_id]
    * @return       NULL
    * @package      forums
    */

    public function add_topic_view($topic_id)
    {
        $this->db->set('view_count', 'view_count + 1', FALSE);
        $this->db->where('id', (int) $topic_id);
        return $this->db->update( $this->table_name() );
    }

    /**
    * Get Posts in Topic
    *
    * Get all posts in a topic.
    * 
    * @access       public
    * @param        int 	[$forum_id] 	Which topic should be counted
    * @return       int 	Returns a count of how many posts there are
    * @package      forums
    */
    public function get_posts_by_topic($topic_id, $offset, $per_page)
    {
        return $this->get_entries(
            array(
                'where' => $this->db->protect_identifiers('id') . ' = ' . $this->db->escape($topic_id) . ' OR ' . $this->db->protect_identifiers('parent_id') . ' = ' . $this->db->escape($topic_id),
                'order_by' => 'created',
                'sort' => 'asc',
                'paginate' => 'yes',
                'offset' => $offset,
                'limit' => $per_page
            )
        );
    }

    /**
    * Get Topics in Forum
    *
    * Return an array of all topics in a forum.
    * 
    * @access       public
    * @param        int 	[$forum_id] 	Which forum should be counted
    * @return       int 	Returns a count of how many topics there are
    * @package      forums
    */
    public function get_topics_by_forum($forum_id, $offset, $per_page)
    {
        $entries = $this->get_entries(
            array(
                'where' => $this->db->protect_identifiers('forum_id') . ' = ' . $this->db->escape($forum_id) . ' AND ' . $this->db->protect_identifiers('parent_id') . ' = 0',
                'paginate' => 'yes',
                'order_by' => 'created',
                'sort' => 'asc',
                'offset' => $offset,
                'limit' => $per_page
            )
        );

        // TODO: something more dynamic and reuseable in the base model would be nice
        // return $this->multisort_entries($entries, array('field1' => 'asc'));

        $sticky = array();
        $updated_on = array();
        $created = array();

        foreach($entries['entries'] as $key => $post)
        {
           $sticky[$key] = $post['is_sticky'];
           $updated_on[$key] = $post['updated'];
           $created[$key] = $post['created'];
        }

        array_multisort($sticky, SORT_DESC, $updated_on, SORT_DESC, $created, SORT_DESC, $entries['entries']);

        return $entries;
    }

    public function delete_by_forum($id)
    {
        return $this->delete_by('forum_id', $id);
    }

    /**
    * Get latest post in Forum
    *
    * 
    * @access       public
    * @param        int 	[$forum_id] 	Which forum should be counted
    * @return       int 	Returns a count of how many replies there are
    * @package      forums
    */
    public function last_forum_post($forum_id)
    {
        $latest_post = $this->get_entries(
            array(
                'where' => $this->db->protect_identifiers($this->table_name() . '.forum_id') . ' = ' . $this->db->escape($forum_id),
                'order_by' => 'created',
                'sort' => 'desc',
                'limit' => 1
            )
        );

        $latest_post = reset($latest_post['entries']);

        // if this isn't a topic, we'll ned to get the title
        if($latest_post['parent_id'] != 0)
        {
            $topic = $this->get_topic($latest_post['parent_id']);
            $latest_post['title'] = $topic->title;
        }

        return $latest_post;
    }

    /**
    * Get latest post in Forum
    *
    * How many replies have been made to topics in a forum.
    * 
    * @access       public
    * @param        int 	[$forum_id] 	Which forum should be counted
    * @return       int 	Returns a count of how many replies there are
    * @package      forums
    */
    public function last_topic_post($topic_id)
    {
        $latest_post = $this->get_entries(
            array(
                'where' => $this->db->protect_identifiers($this->table_name() . '.parent_id') . ' = ' . $this->db->escape($topic_id),
                'order_by' => 'created',
                'sort' => 'desc',
                'limit' => 1
            )
        );

        $latest_post = reset($latest_post['entries']);
        $topic = $this->get_topic($latest_post['parent_id']);
        $latest_post['title'] = $topic->title;

        return $latest_post;
    }
	
    /**
    * Get Author Info
    *
    *
    * @access       public
    * @param        int 	[$author_id] 	The author ID.
    * @return       array
    * @package      forums
    */

    // we possibly don't even need this one, since streams is getting the author anyway
    public function author_info($author_id)
    {
       // should already be loaded, but anyway - maybe remove this in the future
        $this->load->library('ion_auth');
        return $this->ion_auth->get_user($author_id);
    }

    /**
    * Get topic
    *
    * Get the basic information about a topic (not the posts within it)
    * 
    * @access       public
    * @param        int 	[$topic_id] 	Which topic to look at
    * @return       int 	Returns an object containing a topic
    * @package      forums
    */

    // how the hell does the caller know that this is a topic without getting the infos first?
    // magic?  -Mike
    function get_topic($topic_id = 0)
    {
        return $this->get_entry($topic_id, false);
    }

    function set_topic_update($topic_id)
    {
        return $this->update_by($topic_id, array('updated' => date('Y-m-d H:s:i', time())));
    }

    function new_topic($user_id, $topic, $forum)
    {
        $this->load->helper('date');

        $insert = array(
        	    'forum_id' 		=> $forum->id,
        	    'created_by' 	=> $user_id,
        	    'parent_id' 	=> 0,
        	    'title' 		=> $topic->title,
        	    'content' 			=> $topic->content,
        	    'created' 	=> date('Y-m-d H:s:i', time()),
        	    'view_count' 	=> 0,
        	    );
        	
        $this->db->insert($this->table_name(), $insert);
        	
        return $this->db->insert_id();
    }

  function new_reply($user_id, $reply, $topic)
  {
    $this->load->helper('date');

    $insert = array(
		    'forum_id' 		=> $topic->forum_id,
		    'created_by' 	=> $user_id,
		    'parent_id' 	=> $topic->id,
		    'title' 		=> '',
		    'content'		=> $reply->content,
		    'created' 	=> date('Y-m-d H:s:i', time()),
		    'view_count' 	=> 0,
		    );
		
    $this->db->insert($this->table_name(), $insert);

    return $this->db->insert_id();
  }
	
    // again: how should the caller know that this is a reply?
    function get_reply($reply_id = 0)
    {
        return $this->get_entry($reply_id, false);
    }
	
    function get_post($post_id = 0)
    {
        return $this->get_entry($post_id);
    }
}