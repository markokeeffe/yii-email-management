
<?php if(!count($tagMaps)) : ?>
  <h2>This layout has no parts to map</h2>
<?php else : ?>

  <h2>Map the <?php echo $objectName; ?> object to the <?php echo $model->label; ?> layout</h2>
  <p>Select attributes from the <?php echo $objectName; ?> object that can be used
  to dynamically add data to the <?php echo $model->label; ?> layout when building
  an email.</p>

<?php $form=$this->beginWidget('VActiveForm', array(
	'id'=>'map-layout-form',
 'htmlOptions' => array(
  'class' => 'form-horizontal well well-small',
  'data-behavior' => 'ajaxForm',
 ),
)); ?>

  <?php foreach($tagMaps as $i => $map) : ?>

    <?php echo $form->hiddenField($map, "[$i]tag_id"); ?>
    <?php echo $form->hiddenField($map, "[$i]object"); ?>

    <fieldset>
      <legend><?php echo $map->tag->label.' ('.$map->tag->type.')'; ?></legend>

      <div class="form-group<?php echo ($map->getError("content_attr") ? ' has-error' : ''); ?>">
        <?php echo $form->labelEx($map,"[$i]content_attr"); ?>
          <?php echo $form->dropDownList($map,"[$i]content_attr",$attrs); ?>
          <?php echo $form->error($map,"[$i]content_attr"); ?>
      </div>
      <?php if ($map->tag->type !== 'multiline') : ?>
      <div class="form-group">
        <?php echo $form->labelEx($map,"[$i]href_attr"); ?>
          <?php echo $form->dropDownList($map,"[$i]href_attr",$attrs); ?>
          <?php echo $form->error($map,"[$i]href_attr"); ?>
      </div>
      <?php endif; ?>
      <?php if($map->tag->type == 'img') : ?>
      <div class="form-group">
        <?php echo $form->labelEx($map,"[$i]alt_attr"); ?>
          <?php echo $form->dropDownList($map,"[$i]alt_attr",$attrs); ?>
          <?php echo $form->error($map,"[$i]alt_attr"); ?>
      </div>
      <?php endif; ?>
    </fieldset>
  <?php endforeach; ?>

  <div class="form-group">
     <?php echo CHtml::submitButton('Save', array('class'=>'btn btn-primary')); ?>
  </div>

<?php $this->endWidget(); ?>

<?php endif; ?>
