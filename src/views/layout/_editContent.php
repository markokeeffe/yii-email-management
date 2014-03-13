<div id="template-layout-content">
  <?php if(!count($datas)) : ?>
    <h2>This layout has no editable regions.</h2>
  <?php else : ?>

  <h2>Update Template Content</h2>

  <?php if ($model->mappedObject) : ?>
    <?php $this->renderPartial('_selectObject', compact('model')); ?>
  <?php endif; ?>

  <?php $form=$this->beginWidget('VActiveForm', array(
   'id'=>'layout-content-form',
   'enableAjaxValidation' => false,
   'htmlOptions' => array(
     'class' => 'well well-small',
     'data-behavior' => 'ajaxForm',
     'data-target' => '#email-form-container',
   ),
  )); ?>

    <?php foreach($datas as $i => $data) : ?>

      <fieldset>
        <legend><?php echo $data->tag->label.' ('.$data->tag->type.')'; ?></legend>

        <div class="form-group<?php echo ($data->getError('content') ? ' has-error' : ''); ?>">
          <?php echo $form->labelEx($data,"[$i]content"); ?>
            <?php if ($data->tag->type == 'multiline') : ?>
                <?php echo $form->textArea($data,"[$i]content", array(
                  'data-behavior' => 'textEditorMCE',
                )); ?>
              <?php elseif ($data->tag->type == 'img') : ?>
                <?php echo $form->textField($data,"[$i]content"); ?>
              <?php else : ?>
                <?php echo $form->textField($data,"[$i]content"); ?>
            <?php endif; ?>
            <?php echo $form->error($data,"[$i]content"); ?>
        </div>
        <?php if ($data->tag->type !== 'multiline') : ?>
        <div class="form-group<?php echo ($data->getError('href') ? ' has-error' : ''); ?>">
          <?php echo $form->labelEx($data,"[$i]href"); ?>
            <?php echo $form->textField($data,"[$i]href"); ?>
            <?php echo $form->error($data,"[$i]href"); ?>
        </div>
        <?php endif; ?>
        <?php if($data->tag->type == 'img') : ?>
        <div class="form-group<?php echo ($data->getError('alt') ? ' has-error' : ''); ?>">
          <?php echo $form->labelEx($data,"[$i]alt"); ?>
            <?php echo $form->textField($data,"[$i]alt"); ?>
            <?php echo $form->error($data,"[$i]alt"); ?>
        </div>
        <?php endif; ?>
      </fieldset>
    <?php endforeach; ?>

    <?php echo CHtml::button('Cancel', array(
      'class' => 'cancel btn btn-default',
      'data-behavior' => 'toggleHidden',
      'data-hide' => '#template-layout-content',
    )); ?>
   <?php echo CHtml::submitButton('Save', array('class'=>'btn btn-primary')); ?>

  <?php $this->endWidget(); ?>

  <?php endif; ?>
</div>
