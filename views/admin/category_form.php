<section class="title">
  <?php if($this->method == 'create_category'): ?>
  <h4><?php echo lang('forums_create_category_title');?></h4>
  <?php else: ?>
  <h4><?php echo sprintf(lang('forums_edit_category_title'), $category->title);?></h4>
  <?php endif; ?>
</section>

<section class="item">
  <div class="content">
      <?php echo form_open($this->uri->uri_string(), 'class="crud"', array('id' => $category->id)); ?>
      
      <ol>
	
        <li>
          <label for="title"><?php echo lang('forums_title_label');?></label>
          <?php echo form_input('title', $category->title, 'maxlength="100"'); ?>
          <span class="required-icon tooltip"><?php echo lang('required_label');?></span>
        </li>
	
      </ol>
      
      
      <div class="buttons">
        <?php $this->load->view('admin/partials/buttons', array('buttons' =>array('save','cancel') )); ?>
      </div>
      <?php echo form_close(); ?>
      
  </div>
</section>
