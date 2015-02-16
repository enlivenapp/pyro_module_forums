<div class="forum_buttons">
  <?php echo anchor('forums/topics/new_topic/'.$forum->id, ' New Topic ');?>
  <?php echo anchor('forums/posts/new_reply/'.$topic->id, ' Reply ');?>
  <br clear="both" />
</div>
<?php echo $pagination['links']; ?>
<table class="topic_table" border="0" cellspacing="0">
  <thead>
    <tr>
      <th colspan="4" class="header"><?php echo $topic->title;?></th>
    </tr>
  </thead>
  <tbody>
  <?php $i = $pagination['offset'];
  foreach($topic->posts['entries'] as $post):
  ?>
    <tr class="postinfo">
      <td width="20%">
        <?php
        if(Settings::get('enable_profiles')):
            echo anchor('user/'.$post['created_by']['user_id'], $post['created_by']['display_name']);
        else:
            echo $post['created_by']->full_name;
        endif;
        ?>
      </td>
      <td width="50%">
        {{ helper:date format="D M j Y - g:i a" timestamp="<?php echo $post['created'] ?>"}}
      </td>
      <?php if($post['parent_id'] == 0): ?>
      <td width="30%" class="postreport">
        [ <?php echo anchor('forums/posts/report/'.$post['id'], 'Report');?> ]
        <?php if($this->ion_auth->is_admin() && $topic->is_sticky == 'no'): ?>
        [ <?php echo anchor('forums/topics/stick/'.$post['id'], 'Make Sticky');?> ]
        <?php elseif($this->ion_auth->is_admin() && $topic->is_sticky == 'yes' ): ?>
        [ <?php echo anchor('forums/topics/unstick/'.$post['id'], 'Unstick');?> ]
        <?php endif; ?>
      </td>
      <?php else: ?>
      <td width="35%" class="postreport">
        [ <?php echo anchor('forums/posts/report/'.$post['id'], 'Report');?> ] 
        [ <?php echo anchor('forums/posts/view_reply/'.$post['id'], '# '.$i , array('title' => 'Permalink to this post', 'name' => $post['id']));?> ]
      </td>
      <?php endif; ?>
    </tr>
    <tr>
      <td valign="top" class="authorinfo">
        <p>
          <a href="<?php echo site_url('user/'.$post['created_by']['user_id']); ?>"> 
          {{ user:profile user_id="<?php echo $post['created_by']['user_id'] ?>" }}
          {{ if avatar }}<p>{{ avatar:img }} {{ else }}  <?php echo gravatar($post['created_by']['email']); ?></p>{{ endif }}
          {{ /user:profile }}
          </a>
        </p>
        <p>
          Joined:<br>
          {{ helper:date timestamp={ user:created_on user_id="<?php echo $post['created_by']['user_id'] ?>" } }}
        </p>
        <p>
          Posts:<?php echo $post['created_by']['post_count'];?>
        </p>
      </td>
      <td colspan="2" valign="top">
        <?php echo parse_markdown(htmlentities($post['content'])); ?>
      </td>
    </tr>
    <tr class="postlinks">
      <td>
        <?php if(isset($user->id)): ?>
        [ <?php echo anchor('messages/create/'.$post['created_by']['user_id'], 'Message');?> ]
        <?php endif; ?>
      </td>
      
      <td colspan="2" align="right">[ <?php echo anchor('forums/posts/quote_reply/'.$post['id'], 'Quote');?> ]
        <?php if($this->ion_auth->is_admin()): ?>
        [ <?php echo anchor('forums/posts/edit_reply/'.$post['id'], 'Edit');?> ]
        [ <?php echo anchor('forums/posts/delete_reply/'.$post['id'], 'Delete', 'class="delete"');?> ]
        <?php endif; ?>
        </td>
        
      
    </tr>
    <?php $i++; 
          endforeach; 
    ?>
    </tbody>
</table>

<?php echo $pagination['links']; ?>
<div class="forum_buttons">
  <?php echo anchor('forums/topics/new_topic/'.$forum->id, ' New Topic ');?>
  <?php echo anchor('forums/posts/new_reply/'.$topic->id, ' Reply ');?>
  <br clear="both" />
</div>
