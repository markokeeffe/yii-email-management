
<?php $form=$this->beginWidget('VActiveForm', array(
	'id'=>'email-template-form',
)); ?>

  <div class="form-group<?php echo ($model->getError('title') ? ' has-error' : ''); ?>">
    <?php echo $form->labelEx($model,'title', array('class' => 'col-lg-2')); ?>
    <div class="col-lg-5">
      <?php echo $form->textField($model,'title', array('class'=>'col-md-10')); ?>
      <?php echo $form->error($model,'title'); ?>
    </div>
  </div>

  <div class="form-group<?php echo ($model->getError('is_fixed') ? ' has-error' : ''); ?>">
    <div class="col-lg-5 col-lg-offset-2">
      <div class="checkbox">
        <?php echo $form->label($model, 'is_fixed', array(
          'label' => $model->getAttributeLabel('is_fixed').' '.$form->checkbox($model, 'is_fixed'),
        )); ?>
      </div>
      <?php echo $form->error($model,'is_fixed'); ?>
    </div>
  </div>

  <div class="form-group<?php echo ($model->getError('body') ? ' has-error' : ''); ?>">
    <?php echo $form->labelEx($model,'body', array('class' => 'col-lg-2')); ?>
    <div class="col-lg-5">
      <?php echo $form->textArea($model,'body',array(
        'class' => 'col-md-10 monospace',
        'rows' => 50,
      )); ?>
      <?php echo $form->error($model,'body'); ?>
    </div>
  </div>

  <div class="form-group">
    <div class="col-lg-5 col-lg-offset-2">
     <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class'=>'btn btn-primary')); ?>
    </div>
  </div>

<?php $this->endWidget(); ?>
