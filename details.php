<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Forums extends Module {

    private $module = 'forums';
    private $namespace = 'forums';
    private $stream_prefix = 'forums_';

    private $categories_stream = 'categories';
    private $forums_stream = 'forums';
    private $subscriptions_stream = 'subscriptions';
    private $posts_stream = 'posts';
    private $permission_labels_stream = 'permission_labels';
    private $permissions_stream = "permissions";

    public $version = '2.0.0';

    public function info() {
      return array(
        'name' => array(
          'en' => 'Forums',
        ),
        'description' => array(
          'en' => 'Navtive Forum for PyroCMS 2.2.x',
        ),

        'frontend'  => TRUE,
        'backend'   => TRUE,
        'menu'  => 'content',
        'sections'  => array(
          'forums' => array(
            'name' => 'forums:forums',
            'uri' => 'admin/forums/list_forums',
            'shortcuts' => array(
              array(
                'name' => 'forums:create_forum_title',
                'uri'  => 'admin/forums/create_forum',
                'class'  => 'add'
              ),
            ),
          ),
          'categories' => array(
            'name' => 'forums:category_title',
            'uri' => 'admin/forums/index',
            'shortcuts' => array(
              array(
                'name'  => 'forums:create_category_title',
                'uri'   => 'admin/forums/create_category',
                'class' => 'add'
              ),
            ),
          ),
          'permissions' => array(
            'name' => 'forums:permissions',
            'uri' => 'admin/forums/permissions',
            'shortcuts' => array(
              array(
                'name'  => 'forums:create_perms_title',
                'uri'   => 'admin/forums/create_permissions',
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
        $permission_labels_id = $this->streams->streams->add_stream( 'lang:forums:permission_labels', $this->permission_labels_stream, $this->namespace, $this->stream_prefix, null, array('title_column' => 'title'));
        $permissions_id = $this->streams->streams->add_stream( 'lang:forums:permissions', $this->permissions_stream, $this->namespace, $this->stream_prefix, null, array('title_column' => 'title'));

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

            This was a note to myself to write in permissions. - Mike
            */
          'description' => array(
            'name' => 'lang:forums:description_label',
            'slug' => 'description',
            'namespace' => $this->namespace,
            'type' => 'wysiwyg',
            'extra' => array(
              'editor_type' => 'simple'
            )
          ),
          'category_id' => array(
            'name' => 'lang:forums:category_label',
            'slug' => 'category_id',
            'namespace' => $this->namespace,
            'type' => 'relationship',
            'extra' => array(
              'choose_stream' => $categories_stream_id
            )
          ),
          'forum_id' => array(
            'name' => 'lang:forums:forum_title',
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
            'name' => 'lang:forums:view_count_label',
            'slug' => 'view_count',
            'namespace' => $this->namespace,
            'type' => 'integer',
            'extra' => array()
          ),
          'post_id' => array(
            'name' => 'lang:forums:topic_label',
            'slug' => 'post_id',
            'namespace' => $this->namespace,
            'type' => 'integer'
          ),
          'user_id' => array(
            'name' => 'lang:forums:user_label',
            'slug' => 'user_id',
            'namespace' => $this->namespace,
            'type' => 'integer'
          ),
          'slug' => array(
            'name' => 'lang:forums:slug_label',
            'slug' => 'slug',
            'namespace' => $this->namespace,
            'type' => 'text'
          ),
          'perm_id' => array(
            'name' => 'lang:forums:permid_label',
            'slug' => 'perm_id',
            'namespace' => $this->namespace,
            'type' => 'integer'
          ),
          'active' => array(
            'name' => 'lang:forums:active_label',
            'slug' => 'active',
            'namespace' => $this->namespace,
            'type' => 'choice',
            'extra' => array(
              'choice_type' => 'radio',
              'choice_data' => "yes : lang:forums:yes\nno : lang:forums:no",
              'default_value' => 'yes'
            )
          ),
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

        /*
        * assign fields to permission labels stream
        ***/
        $this->streams->fields->assign_field($this->namespace, $this->permission_labels_stream, $fields['title']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->permission_labels_stream, $fields['slug']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->permission_labels_stream, $fields['description']['slug'], array('required' => false, 'instructions' => 'lang:forums:perm_label_instructions'));

        /*
        * assign fields to permissions stream
        ***/
        $this->streams->fields->assign_field($this->namespace, $this->permissions_stream, $fields['user_id']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->permissions_stream, $fields['perm_id']['slug'], array('required' => true));
        $this->streams->fields->assign_field($this->namespace, $this->permissions_stream, $fields['active']['slug'], array('required' => true, 'instructions' => 'lang:forums:active_instructions'));


        // settings
        // remove old settings
        $this->db->delete('settings', array('module' => $this->module));
        // set the settings
        // remove. going to markdown
        $settings = array(
          array(
            'slug'         => 'forums_not_logged_in_access',
            'title'        => 'Allow Public Access',
            'description'  => 'Can people *not* logged in see the forums? Users are always required to log in to post.',
            'type'         => 'select',
            '`default`'    => 'no',
            '`value`'      => 'no',
            'options'      => 'no=Only logged in users see Forums|yes=Anyone can see the Forums',
            'is_required'  => 1,
            'is_gui'       => 1,
            'module'       => 'forums'
          ),
          array(
            'slug'         => 'forums_framework_support',
            'title'        => 'Frontend Framework Support',
            'description'  => 'Choose a supported framework below',
            'type'         => 'select',
            '`default`'    => 'basic',
            '`value`'      => 'basic',
            'options'      => 'basic=Basic|bootstrap3=Bootstrap 3',
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

      if ($old_version <= '1.4.9')
      {
        // this will catch original public version 1.0
        // lots to do to convert from dbforge to streams...

        // but for now..
        return false;
      }
      elseif ($old_version <= '1.5.9')
      {
        // this will catch my v1.5 to convert up to 2
        // lots to do to convert from dbforge to streams...

        // but for now..
        return false;
      }
      elseif ($old_version == '2.0.0')
      {
        // current version
        return true;
      }
      // default
      return false;
    }

    public function help() {
      // Return a string containing help info
      // You could include a file and return it here.
      return TRUE;
    }
}
/* End of file details.php */
