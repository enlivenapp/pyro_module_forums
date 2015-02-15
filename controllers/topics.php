<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Topics Controller
 *
 * 
 *
 * @package   PyroCMS
 * @author    Michael Webber
 * @author    Marco Gruter
 * @license   Apache License v2.0
 * @link    http://pyrocms.com
 * @since   Version 1.5.x
 * @filesource
 */

/**
 * PyroCMS
 *
 * An open source CMS based on CodeIgniter
 *
 * @package		PyroCMS
 * @author		PyroCMS Dev Team
 * @license		Apache License v2.0
 * @link		http://pyrocms.com
 * @since		Version 0.9.8-rc2
 * @filesource
 */

/**
 * PyroCMS Forums Topic Controller
 *
 * Provides viewing and CRUD for topics
 *
 * @author		Dan Horrigan <dan@dhorrigan.com>
 * @package		PyroCMS
 * @subpackage	Forums
 */
class Topics extends Public_Controller {

  /**
   * Constructor
   *
   * Loads dependencies and template settings
   *
   * @access	public
   * @return	void
   */
  public function __construct() {
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
      $this->session->set_flashdata('error', 'Sorry,  You must be logged in to see the forums. Please login and try again.');
      redirect(site_url('users/login'));
    }

    $this->load->models(array('forumsbase_m', 'forums_m', 'subscriptions_m', 'posts_m'));

    $this->load->helper('smiley');
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

    $this->template->set_breadcrumb('Home', '/')
                   ->set_breadcrumb('Forums', 'forums');

  }

  /**
   * View
   *
   * Loads the topic and displays it with all replies.
   *
   * @param	int	$topic_id	Id of the topic to display
   * @param	int	$offset		The offset used for pagination
   * @access	public
   * @return	void
   */
  public function view($topic_id, $offset = 0) {
    // Update view counter
    $this->posts_m->add_topic_view($topic_id);

    // Pagination junk
    $per_page = '10';
    $pagination = create_pagination('forums/topics/view/'.$topic_id, $this->posts_m->count_posts_in_topic($topic_id), $per_page, 5);
    if($offset < $per_page) {
      $offset = 0;
    }
    $pagination['offset'] = $offset;
    // End Pagination

    // If topic or forum do not exist then 404
    ($topic = $this->posts_m->get($topic_id)) or show_404();
    ($forum = $this->forums_m->get($topic->forum_id)) or show_404();

    // Get a list of posts which have no parents (topics) in this forum
    $topic->posts = $this->posts_m->get_posts_by_topic($topic_id, $offset, $per_page);
    foreach($topic->posts['entries'] as &$post) {
      $post['created_by']['post_count'] =  $this->posts_m->count_user_posts($post['created_by']['user_id']);
    }
    $data->topic =& $topic;
    $data->forum =& $forum;
    $data->pagination = &$pagination;

    // Create page
    $this->template->title($topic->title);
    $this->template->set_breadcrumb($forum->title, 'forums/view/'.$forum->id);
    $this->template->set_breadcrumb($topic->title);
    $this->template->build($this->framework . 'posts/view', $data);
  }


  function new_topic($forum_id = 0) {
    if(!$this->ion_auth->logged_in()) {
      redirect('users/login');
    }

    // Get the forum name
    $forum = $this->forums_m->get($forum_id);

    // Chech if there is a forum with that ID
    if(!$forum) {
      show_404();
    }
		$data  = new stdClass();
    // Default this to a nope
    $data->show_preview = FALSE;

    if($this->input->post('submit') or $this->input->post('preview')) {
      $this->load->library('form_validation');

      $this->form_validation->set_rules('title', 'Title', 'trim|strip_tags|required|max_length[100]');
      $this->form_validation->set_rules('content', 'Message', 'trim|required');

      if ($this->form_validation->run() === TRUE) {
	if( $this->input->post('submit') ) {
		$topic  = new stdClass();
	  $topic->title = set_value('title');
	  $topic->content = htmlspecialchars_decode(set_value('content'), ENT_QUOTES);

	  if($topic->id = $this->posts_m->new_topic($this->current_user->id, $topic, $forum)) {
	    $this->posts_m->set_topic_update($topic->id);

	    // Add user to notify
	    if($this->input->post('notify') == 1) {
	      $this->subscriptions_m->add($this->current_user->id, $topic->id);
	    }
	    else {
	      $this->subscriptions_m->delete_by(array('user_id' => $this->current_user->id, 'post_id' => $topic->id));
	    }
	    redirect('forums/topics/view/'.$topic->id);
	  }

	  else {
	    show_error("Error Message:  Error Accured While Adding Topic");
	  }
	}

	// Preview button was hit, just show em what the post will look like
	elseif( $this->input->post('preview') ) {
	  // Define and Parse Preview
	  //$data->preview = $this->posts_m->postParse($message, $smileys);

	  $data->show_preview = TRUE;
	}
      }

      else {
	$data->validation_errors = $this->form_validation->error_string();
      }
    }

    $data->forum =& $forum;
    $data->topic =& $topic;


    $this->template->set_breadcrumb($forum->title, 'forums/view/'.$forum->id);
    $this->template->set_breadcrumb('New Topic');
    $this->template->build($this->framework . 'posts/new_topic', $data);
  }

  function stick($topic_id) {
    $this->ion_auth->logged_in() or redirect('users/login');

    $this->ion_auth->is_admin() or show_404();

    if($this->posts_m->update($topic_id, array('sticky' => 1))) {
      $this->session->set_flashdata('success', 'Topic has been made sticky.');
    }
    else {
      $this->session->set_flashdata('error', 'Topic could not be made sticky.');

    }
    redirect('forums/topics/view/' . $topic_id);
  }

  function unstick($topic_id) {
    $this->ion_auth->logged_in() or redirect('users/login');

    $this->ion_auth->is_admin() or show_404();

    if($this->posts_m->update($topic_id, array('sticky' => 0))) {
      $this->session->set_flashdata('success', 'Topic has been unstuck.');
    }
    else {
      $this->session->set_flashdata('error', 'Topic could not be unstuck.');

    }
    redirect('forums/topics/view/' . $topic_id);
  }

}
?>