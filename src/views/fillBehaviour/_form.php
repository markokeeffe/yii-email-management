
<?php $form=$this->beginWidget('VActiveForm', array(
	'id'=>'email-layout-fill-behaviour-form',
)); ?>

	<p class="controls note">Fields with <span class="required">*</span> are required.</p>

<div class="control-group<?php echo ($model->getError('class') ? ' error' : ''); ?>">
		<?php echo $form->labelEx($model,'class'); ?>
  <div class="controls">
    <?php echo $form->textField($model,'class',array('size'=>60,'maxlength'=>255)); ?>
    <?php echo $form->error($model,'class'); ?>
  </div>
</div>
	<div class="form-actions">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class'=>'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>
