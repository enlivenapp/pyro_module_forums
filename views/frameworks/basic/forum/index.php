<?php if ($forum_categories): ?>
  <?php foreach($forum_categories as $category): ?>
    <?php if($category->forums): ?>

<table class="forum_table" border="0" cellspacing="0">
    <thead>
        <tr>
            <th colspan="5" class="header"><?php echo $category->title;?></th>
        </tr>
        <tr>
            <th width="2%">&nbsp;</th>
            <th width="53%">Forum Name</th>
            <th width="10%" class="center_col">Topics</th>
            <th width="10%" class="center_col">Replies</th>
            <th width="25%">Last Post Info </th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($category->forums as $forum): ?>
      <tr>
        <td class="forum_icon">
          <?php echo Asset::img('module::folder.png', 'Forum'); ?>
        </td>
        <td>
          <b><?php echo anchor('forums/view/'.$forum->id, $forum->title);?></b><br/>
              <span class="description"><?php echo $forum->description;?></span>
        </td>
        <td class="center_col">
          <?php echo $forum->topic_count; ?>
        </td>
        <td class="center_col">
          <?php echo $forum->reply_count; ?>
        </td>
        <td class="lastpost_info">
        <?php if(isset($forum->last_post['title'])):?>
          <?php echo anchor('forums/posts/view_reply/'.$forum->last_post['id'], $forum->last_post['title']); ?><br/>
		        {{ helper:date format="M j Y | g:i a" timestamp="<?php echo $forum->last_post['created'] ?>"}}<br/>
		        Author: <?php echo $forum->last_post['created_by']['display_name']; ?>
        <?php endif;?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
</table>
  <?php else: ?>
    <div class="errors center">- No Posts -</div>
    <?php endif; ?>
  <?php endforeach; ?>

<?php else: ?>
<div class="errors center">- No Posts -</div>


<?php endif ?>