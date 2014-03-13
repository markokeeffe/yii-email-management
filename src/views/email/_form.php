
<?php $form=$this->beginWidget('VActiveForm', array(
	'id'=>'email-form',
)); ?>

	<p>Fields with <span class="required">*</span> are required.</p>

  <div class="form-group<?php echo ($model->getError('template_id') ? ' has-error' : ''); ?>">
      <?php echo $form->labelEx($model,'template_id', array('class' => 'col-lg-2')); ?>
    <div class="col-lg-5">
      <?php echo $form->dropDownList($model,'template_id',CHtml::listData($templates, 'id', 'title'), array(
        'prompt' => 'Choose template...',
        'data-behavior' => 'toggleHidden',
        'data-listener' => 'change',
        'data-condition' => '('.implode('|', $fillable).')',
        'data-selector' => '.auto-fill-fields',
      )); ?>
      <?php echo $form->error($model,'template_id'); ?>
    </div>
  </div>
  <div class="form-group<?php echo ($model->getError('title') ? ' has-error' : ''); ?>">
      <?php echo $form->labelEx($model,'title', array('class' => 'col-lg-2')); ?>
    <div class="col-lg-5">
      <?php echo $form->textField($model,'title',array(
        'size'=>60,
        'maxlength'=>255,
        'spellcheck' => 'true'
      )); ?>
      <?php echo $form->error($model,'title'); ?>
    </div>
  </div>

  <?php if ($this->et->useSubject) : ?>
    <div class="form-group<?php echo ($model->getError('subject') ? ' has-error' : ''); ?>">
        <?php echo $form->labelEx($model,'subject', array('class' => 'col-lg-2')); ?>
      <div class="col-lg-5">
        <?php echo $form->textField($model,'subject',array(
          'size'=>60,
          'maxlength'=>255,
          'spellcheck' => 'true'
        )); ?>
        <?php echo $form->error($model,'subject'); ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($subids = $this->et->getSubidValues()) : ?>
    <div class="form-group<?php echo ($model->getError('subid_id') ? ' has-error' : ''); ?>">
      <?php echo $form->labelEx($model,'subid_id', array('class' => 'col-lg-2')); ?>
      <div class="col-lg-5">
        <?php echo $form->dropDownList($model, 'subid_id', $subids, array(
          'prompt' => 'Select Sub ID value...',
        )); ?>
        <?php echo $form->error($model,'subid_id'); ?>
      </div>
    </div>
  <?php endif; ?>


  <div class="auto-fill-fields" style="display: <?php echo (in_array($model->template_id, $fillable) ? 'block' : 'none'); ?>;">
    <div class="form-group<?php echo ($model->getError('fill_form') ? ' has-error' : ''); ?>">
      <?php echo $form->labelEx($model,'fill_form', array('class' => 'col-lg-2')); ?>
      <div class="col-lg-5">
        <?php echo $form->textField($model,'fill_form',array(
          'size'=>60,
          'maxlength'=>255,
        )); ?>
        <?php echo $form->error($model,'fill_form'); ?>
      </div>
    </div>
  </div>


	<div class="form-group">
    <div class="col-lg-5 col-lg-offset-2">
  		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class'=>'btn btn-primary')); ?>
    </div>
	</div>

<?php $this->endWidget(); ?>
