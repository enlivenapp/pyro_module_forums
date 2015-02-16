<?php
class Forums extends Public_Controller {

  /**
   * The constructor
   * @access public
   * @return void
   */
  function __construct()
  {
    parent::__construct();

    // set the admin's preferred framework, if any.
    // we use this to switch which frontend framework
    // set we're using in the views files.  IE:
    // /frameworks/basic/...  or  /frameworks/bootstrap3/... etc...
    $this->framework = 'frameworks/' . Settings::get('forums_framework_support') . '/';

    // added in 2.0.0  ability for admin to choose if
    // users must be logged in or if the public at-large
    // can see the forums without loggin in.
    if (!is_logged_in() && Setttings::get('forums_not_logged_in_access') == 'no')
    {
      // oops
      $this->session->set_flashdata('error', 'Sorry,  You must be logged in to see the forums. Please login and try again.');
      
      // uri_segments() allows the user to be redirected back
      // to the original URL once they've logged in
      redirect(site_url('users/login/' . uri_string()));
    }

    $this->load->models(array('forumsbase_m', 'forums_m', 'categories_m', 'posts_m'));

    $this->lang->load('forums');
    $this->load->config('forums');

    // turn off Lex
    $this->template->enable_parser_body(FALSE);

    // if they're using the basic settings, we'll add the basic CSS
    // file.  Otherwise, the code will be written to depend on the
    // css file in the theme.
    if (Settings::get('forums_framework_support') == 'basic')
    {
      // add basic CSS file
      $this->template->append_css('module::forums.css');
    }

    // we make need to worry about including this later
    // but leaving it in the wild for now.
  	$this->template->append_js('module::forums.js');

    $this->template->set_breadcrumb('Home', '/');

  }
	
	/**
   * Index
   *
   * Default/Starting controller
   *
   * @access public
   * @return void
   */
  function index()
  {
    if( $forum_categories = $this->categories_m->get_all() )
    {
	   // Get list of categories
	   foreach($forum_categories as &$category)
	   {
       $category->forums = $this->forums_m->get_many_by('category_id', $category->id);
			
       // Get a list of forums in each category
       foreach($category->forums as &$forum)
       {
  		  $forum->topic_count = $this->posts_m->count_topics_in_forum( $forum->id );
  		  $forum->reply_count = $this->posts_m->count_replies_in_forum( $forum->id );
  		  $forum->last_post = $this->posts_m->last_forum_post($forum->id);
  	   }
      }
    }
    $data->forum_categories =& $forum_categories;
    $this->template->set_breadcrumb('Forums');
    $this->template->build($this->framework . 'forum/index', $data);
  }


  /**
   * View
   *
   * Shows a specific forum and it's
   * contents.
   *
   * @access public
   * @return void
   */
  function view($forum_id = 0, $offset = 0)
  {
    // Check if forum exists, if not 404
    ($forum = $this->forums_m->get($forum_id)) || show_404();

    // Pagination junk
    $per_page = '25';
    $pagination = create_pagination('forums/view/'.$forum_id, $this->posts_m->count_topics_in_forum($forum_id), $per_page, 4);

    $offset = ($offset < $per_page) ? 0 : $offset;
    
    $pagination['offset'] = $offset;
    // End Pagination

    // Get all topics for this forum
    $forum->topics = $this->posts_m->get_topics_by_forum($forum_id, $offset, $per_page);
	
    // Get a list of posts which have no parents (topics) in this forum
    foreach($forum->topics['entries'] as &$topic)
    {
      //echo "<pre>";
      //print_r($topic);
      //echo "</pre>";
      $topic['post_count'] = $this->posts_m->count_posts_in_topic($topic['id']);
      $topic['last_post'] = $this->posts_m->last_topic_post($topic['parent_id']);

    }

    $data->forum =& $forum;
    $data->pagination = $pagination;

    $this->template->set_breadcrumb('Forums', 'forums');
    $this->template->set_breadcrumb($forum->title);
    $this->template->build($this->framework . 'forum/view', $data); 
  }




  function unsubscribe($user_id, $topic_id)
  {
    $this->load->model('subscriptions_m');
    $topic = $this->posts_m->get($topic_id);
    $this->forum_subscriptions_m->delete_by(array('user_id' => $user_id, 'topic_id' => $topic_id));
    $data->topic =& $topic;
    $this->template->build($this->framework . 'posts/unsubscribe', $data);
  }
}
