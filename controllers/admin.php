<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* PyroCMS
*
* An open source CMS based on CodeIgniter
*
* @package		PyroCMS
* @author		Prajwol Shrestha
* @license		Apache License v1.0
* @link		http://semicolondev.com
* @since		Version 0.9.8-rc2
* @filesource
*/

/**
* PyroCMS Forums Admin Controller
*
* Provides an admin for the forums module.
*
* @author		Prajwol Shrestha <prajwols@semicolodev.com>
* @package		PyroCMS
* @subpackage	Forums
*/

class Admin extends Admin_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('forums');
        $this->load->model('forumsbase_m');
        $this->load->driver('Streams');
    }

    /**
    * Index
    *
    * Lists categories.
    *
    * @access	public
    * @return	void
    */
    public function index()
    {
        $this->template->active_section = 'categories';

        $this->load->model('categories_m');
        $extra = array(
            'columns' => array('title', 'created_by', 'created'),
            'title' => 'lang:forums:categories',
            'buttons' => array(
                array(
                    'label'     => lang('global:edit'),
                    'url'       => 'admin/forums/edit_category/-entry_id-'
                ),
                array(
                    'label'     => lang('global:delete'),
                    'url'       => 'admin/forums/delete/category/-entry_id-',
                    'confirm'   => true
                )
            ),
            $extra['filters'] = array(
                'title'
            )
        );

        $this->streams->cp->entries_table($this->categories_m->stream_slug(), $this->categories_m->namespace_slug(), null, null, true, $extra);
    }

    /**
    * List Forums
    *
    * Lists all the forums.
    *
    * @access	public
    * @return	void
    */
    public function list_forums()
    {
        $this->template->active_section = 'forums';

        $this->load->model('forums_m');

        

        $extra = array(
            'columns' => array('title', 'description', 'category_id', 'created_by', 'created'),
            'title' => 'lang:forums:forums',
            'buttons' => array(
                array(
                    'label'     => lang('global:edit'),
                    'url'       => 'admin/forums/edit_forum/-entry_id-'
                ),
                array(
                    'label'     => lang('global:delete'),
                    'url'       => 'admin/forums/delete/forum/-entry_id-',
                    'confirm'   => true
                )
            ),
            $extra['filters'] = array(
                'title'
            )
        );

        $this->streams->cp->entries_table($this->forums_m->stream_slug(), $this->forums_m->namespace_slug(), null, null, true, $extra);
  }

    /**
    * Create Category
    *
    * Displays a form to create a category.
    * Creates the category if it passes form validation.
    *
    * @todo	Check for duplicate categories.
    * @access	public
    * @return	void
    */
    public function create_category()
    {
        $this->load->model('categories_m');

        $extra = array(
            'return' => 'admin/forums/index'
        );

        $this->streams->cp->entry_form($this->categories_m->stream_slug(), $this->categories_m->namespace_slug(), 'new', null, true, $extra);
    }

    /**
    * Edit Category
    *
    * Allows admins to edit a category
    *
    * @param	int	The id of the category to edit.
    * @access	public
    * @return void
    */
    public function edit_category($id) {
        $this->load->model('categories_m');

        $extra = array(
            'return' => 'admin/forums/index'
        );

        $this->streams->cp->entry_form($this->categories_m->stream_slug(), $this->categories_m->namespace_slug(), 'edit', $id, true, $extra);
    }

    /**
    * Create Forum
    *
    * Displays a form to create a forum.
    * Creates the forum if it passes form validation.
    *
    * @todo	Check for duplicate forums.
    * @access	public
    * @return	void
    */
    public function create_forum() {
        $this->load->model('forums_m');

        $extra = array(
            'return' => 'admin/forums/list_forums'
        );

        $this->streams->cp->entry_form($this->forums_m->stream_slug(), $this->forums_m->namespace_slug(), 'new', null, true, $extra);
    }

    /**
    * Edit Forum
    *
    * Allows admins to edit forums.
    *
    * @param	int	The id of the forum to edit.
    * @access	public
    * @return	void
    */
    public function edit_forum($id) {
        $this->load->model('forums_m');

        $extra = array(
            'return' => 'admin/forums/list_forums'
        );

        $this->streams->cp->entry_form($this->forums_m->stream_slug(), $this->forums_m->namespace_slug(), 'edit', $id, true, $extra);
    }

    /**
    * Delete
    *
    * This deletes both categories and forums (based on $type).
    * It recursivly deletes all children.
    *
    * @param	string	The type of item to delete.
    * @param	int		The id of the category or forum to delete.
    * @access	public
    * @return	void
    */
    public function delete($type, $id) {

        $this->load->library('forums_lib');

        switch ($type) {
            // Delete the category, all related forums and its posts
            case 'category':
                // delete a category and everything in ti
                if( $this->forums_lib->delete_category($id) ) {
                    // yay
                    $this->session->set_flashdata('success', $this->lang->line('forums_category_delete_success'));    
                } else {
                    // nay
                    // TODO: log this failure
                    $this->session->set_flashdata('error', $this->lang->line('global:error'));    
                }
                
                redirect('/admin/forums');
            break;

            // Delete the forum
            case 'forum':
                // delete all the subscriptions on posts of this forum
                $rv = $this->forums_lib->delete_subscriptions_by_forum($id);
                // delete all posts
                $rv = $rv && $this->posts_m->delete_by_forum($id);
                // Delete the forum
                $rv = $rv && $this->forums_m->delete($id);

                if($rv) {
                    // yay
                    $this->session->set_flashdata('success', $this->lang->line('forums_forum_delete_success'));
                } else {
                    // nay
                    $this->session->set_flashdata('error', $this->lang->line('global:error'));
                }
                
                redirect('/admin/forums/list_forums');

            break;

            default:
            break;
        }
    }
}
?>
