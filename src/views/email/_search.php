<div class="wide form">

<?php $form=$this->beginWidget('VActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
  'htmlOptions' => array(
    'class'=>'form-horizontal'
  ),
)); ?>

	<h2>Search Emails</h2>

	<div class="form-group">
		<?php echo $form->label($model,'id',array('class'=>'control-label')); ?>
  <div class="col-lg-5">
    <?php echo $form->textField($model,'id',array('size'=>10,'maxlength'=>10)); ?>
  </div>
	</div>

	<div class="form-group">
		<?php echo $form->label($model,'template_id',array('class'=>'control-label')); ?>
  <div class="col-lg-5">
    <?php echo $form->textField($model,'template_id',array('size'=>10,'maxlength'=>10)); ?>
  </div>
	</div>

	<div class="form-group">
		<?php echo $form->label($model,'title',array('class'=>'control-label')); ?>
  <div class="col-lg-5">
    <?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
  </div>
	</div>

	<div class="form-group">
		<?php echo $form->label($model,'subject',array('class'=>'control-label')); ?>
  <div class="col-lg-5">
    <?php echo $form->textField($model,'subject',array('size'=>60,'maxlength'=>255)); ?>
  </div>
	</div>

	<div class="form-group">
    <div class="col-lg-5 col-lg-offset-2">
  		<?php echo CHtml::submitButton('Search', array('class'=>'btn btn-primary')); ?>
    </div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->