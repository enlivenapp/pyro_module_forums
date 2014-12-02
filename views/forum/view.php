<div class="forum_buttons">
    <?php echo anchor('forums/topics/new_topic/'.$forum->id, ' New Topic ');?>
    <br clear="both" />
</div>
<?php echo $pagination['links']; ?>
<table class="forum_table" border="0" cellspacing="0">
    <thead>
        <tr>
            <th colspan="5" class="header"><?php echo $forum->title;?></th>
        </tr>
        <tr>
            <th width="2%">&nbsp;</th>
            <th width="53%">Topic Name</th>
            <th width="10%" class="center_col">Posts</th>
            <th width="10%" class="center_col">Views</th>
            <th width="25%">Last Post Info</th>
        </tr>
    </thead>

    <tbody>

        <?php if($forum->topics['total'] == 0):?>
        <tr>
            <td colspan="5" align="center">There are no posts in this topic right now.</td>
        </tr>

        <?php else: ?>

            <?php foreach($forum->topics['entries'] as $topic): ?>
        <tr>
            <td class="forum_icon">
                        <?php echo $topic->sticky ? Asset::img('module::pin.png', 'forums') : Asset::img('module::folder.png', 'forums'); ?>
            </td>
            <td valign="top">
                        <?php echo $topic->sticky ? '<span class="sticky">Sticky: </span>' : ''; ?>
                <strong><?php echo anchor('forums/topics/view/'.$topic->id, $topic->title);?></strong><br/>
               <span class="description">Author : 
                    <?php echo user_displayname($topic->author_id,TRUE);
                    ?>
               </span>
            </td>
            <td class="center_col"><?php echo $topic->post_count;?></td>
            <td class="center_col"><?php echo $topic->view_count?></td>
            <td class="lastpost_info">
                        <?php if(!empty($topic->last_post)):?>
			<?php echo anchor('forums/posts/view_reply/'.$topic->last_post->id, date('M j Y | g:i a', $topic->last_post->created_on)); ?><br/>
			Author: <?php echo $topic->last_post->author->full_name; ?>
                        <?php endif;?>
            </td>
        </tr>
            <?php endforeach; ?>
    </tbody>

    <?php endif;?>
</table>
<?php echo $pagination['links']; ?>
<div class="forum_buttons">
    <?php echo anchor('forums/topics/new_topic/'.$forum->id, ' New Topic ');?>
    <br clear="both" />
</div>

