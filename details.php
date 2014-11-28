<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Forums extends Module {

    private $module = 'forums';
    private $namespace = 'forums';
    private $stream_prefix = 'forums_';

    private $categories_stream = 'categories';
    private $forums_stream = 'forums';
    private $subscriptions_stream = 'subscriptions';
    private $posts_stream = 'posts';

    public $version = '2.0';

    public function info() {
        return array(
            'name' => array(
                'en' => 'Forums',
            ),
            'description' => array(
                  'en' => 'The forum for your site',
            ),

            'frontend'  => TRUE,
            'backend'   => TRUE,
            'menu'  => 'content',

            'sections'  => array(
                'forums' => array(
                    'name' => 'forums_forum_label',
                    'uri' => 'admin/forums/list_forums',
                    'shortcuts' => array(
                    array(
                        'name' => 'forums_create_forum_title',
                        'uri'  => 'admin/forums/create_forum',
                        'class'  => 'add'
                      ),
                    ),
                ),
                'categories' => array(
                    'name' => 'forums_category_title',
                    'uri' => 'admin/forums/index',
                    'shortcuts' => array(
                        array(
                            'name'  => 'forums_create_category_title',
                            'uri'   => 'admin/forums/create_category',
                            'class' => 'add'
                        ),
                    ),
                ),
            )
        );
    }

    public function install() {

        $this->load->driver('Streams');

        // create the streams
        $categories_stream_id = $this->streams->streams->add_stream( 'lang:forums:categories', $this->categories_stream, $this->namespace, $this->stream_prefix, null, array('title_column' => 'title'));
        $forums_stream_id = $this->streams->streams->add_stream( 'lang:forums:forums', $this->forums_stream, $this->namespace, $this->stream_prefix, null, array('title_column' => 'title'));
        $posts_stream_id = $this->streams->streams->add_stream( 'lang:forums:posts', $this->posts_stream, $this->namespace, $this->stream_prefix, null, array('title_column' => 'title'));
        $subscriptions_stream_id = $this->streams->streams->add_stream( 'lang:forums:subscriptions', $this->subscriptions_stream, $this->namespace, $this->stream_prefix, null, array('title_column' => 'title'));

        $fields = array(
            'title' => array(
                'name' => 'lang:forums:title_label',
                'slug' => 'title',
                'namespace' => $this->namespace,
                'type' => 'text',
                'extra' => array(
                    'max_length' => 255
                )
            ),
            /* placeholder since uncertain how/why to use this field
            'permission' => array()
            */
            'description' => array(
                'name' => 'lang:forums:description',
                'slug' => 'description',
                'namespace' => $this->namespace,
                'type' => 'wysiwyg',
                'extra' => array(
                    'editor_type' => 'simple'
                )
            ),
            'category_id' => array(
                'name' => 'lang:forums:category',
                'slug' => 'category_id',
                'namespace' => $this->namespace,
                'type' => 'relationship',
                'extra' => array(
                    'choose_stream' => $categories_stream_id
                )
            ),
            'forum_id' => array(
                'name' => 'lang:forums:forum',
                'slug' => 'forum_id',
                'namespace' => $this->namespace,
                'type' => 'relationship',
                'extra' => array(
                    'choose_stream' => $forums_stream_id
                )
            ),
            'parent_id' => array(
                'name' => 'lang:forums:parent_post',
                'slug' => 'parent_id',
                'namespace' => $this->namespace,
                'type' => 'integer',
                'extra' => array(
                    'default_value' => 0
                )
            ),
            'content' => array(
                'name' => 'lang:forums:content',
                'slug' => 'content',
                'namespace' => $this->namespace,
                'type' => 'textarea',
                'extra' => array(
                    'content_type' => 'HTML',
                    'allow_lex_tags' => 'no'
                )
            ),
            'type' => array(
                'name' => 'lang:forums:type',
                'slug' => 'type',
                'namespace' => $this->namespace,
                'type' => 'integer',
                'extra' => array(
                    'max_length' => 1
                )
            ),
            'is_locked' => array(
                'name' => 'lang:forums:is_locked',
                'slug' => 'is_locked',
                'namespace' => $this->namespace,
                'type' => 'choice',
                'extra' => array(
                    'choice_type' => 'radio',
                    'choice_data' => "yes : lang:forums:yes\nno : lang:forums:no",
                    'default_value' => 'no',
                )
            ),
            'is_hidden' => array(
                'name' => 'lang:forums:is_hidden',
                'slug' => 'is_hidden',
                'namespace' => $this->namespace,
                'type' => 'choice',
                'extra' => array(
                    'choice_type' => 'radio',
                    'choice_data' => "yes : lang:forums:yes\nno : lang:forums:no",
                    'default_value' => 'no',
                )
            ),
            'is_sticky' => array(
                'name' => 'lang:forums:is_sticky',
                'slug' => 'is_sticky',
                'namespace' => $this->namespace,
                'type' => 'choice',
                'extra' => array(
                    'choice_type' => 'radio',
                    'choice_data' => "yes : lang:forums:yes\nno : lang:forums:no",
                    'default_value' => 'no',
                )
            ),
            'view_count' => array(
                'name' => 'lang:forums:view_count',
                'slug' => 'view_count',
                'namespace' => $this->namespace,
                'type' => 'integer',
                'extra' => array()
            ),
            'post_id' => array(
                'name' => 'lang:forum:topic',
                'slug' => 'post_id',
                'namespace' => $this->namespace,
                'type' => 'integer'
            ),
            'user_id' => array(
                'name' => 'lang:forum:user',
                'slug' => 'user_id',
                'namespace' => $this->namespace,
                'type' => 'integer'
            )
        );

        // add all the fields
        $this->streams->fields->add_fields($fields);

        /*
        * assign fields to categories stream
        ***/
        $this->streams->fields->assign_field($this->namespace, $this->categories_stream, $fields['title']['slug'], array('required' => true));

        /*
        * assign fields to forums stream
        ***/
        $this->streams->fields->assign_field($this->namespace, $this->forums_stream, $fields['title']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->forums_stream, $fields['description']['slug'], array('required' => false));
        $this->streams->fields->assign_field($this->namespace, $this->forums_stream, $fields['category_id']['slug'], array('required' => true));

        /*
        * assign fields to posts stream
        ***/
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['forum_id']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['parent_id']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['title']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['content']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['type']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['is_locked']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['is_hidden']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['is_sticky']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->posts_stream, $fields['view_count']['slug'], array('required' => false));

        /*
        * assign fields to subscriptions stream
        ***/
        $this->streams->fields->assign_field($this->namespace, $this->subscriptions_stream, $fields['post_id']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->subscriptions_stream, $fields['user_id']['slug'], array('required' => true));

        // settings
        // remove old settings
        $this->db->delete('settings', array('module' => $this->module));
        // set the settings
        $settings = array(
            array(
                'slug'         => 'forums_editor',
                'title'        => 'Forum Editor',
                'description'  => 'Which editor should the forums use?',
                'type'         => 'select',
                '`default`'    => 'bbcode',
                '`value`'      => 'bbcode',
                'options'      => 'bbcode=BBCode|textile=Textile',
                'is_required'  => 1,
                'is_gui'       => 1,
                'module'       => 'forums'
            ),
        );
        
        // install them settings
        foreach ($settings as $setting)
        {
            if ( ! $this->db->insert('settings', $setting))
            {
                return false;
            }
        }

        return true;
    }

    public function uninstall() {

        $this->load->driver('Streams');

        // remove all stream related stuff
        $this->streams->utilities->remove_namespace($this->namespace);

        // remove the settings
        if( ! $this->db->where('module', 'forums')->delete('settings') )
        {
            return false;
        }

        return true;
    }

    public function upgrade($old_version) {
      // Your Upgrade Logic
      return TRUE;
    }

    public function help() {
      // Return a string containing help info
      // You could include a file and return it here.
      return TRUE;
    }
}
/* End of file details.php */
