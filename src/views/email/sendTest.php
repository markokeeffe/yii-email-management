<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    24/02/14
 *
 * [Yii Workbench] sendTest.php
 */

$this->heading = 'Send Test - '.$model->title;
$this->breadcrumbs=array(
  'Emails'=>array('index'),
  $model->title => array('update', 'id' => $model->id),
  'Send Test',
);
$more_menu = array(
  array('label'=>'Email Content', 'url'=>array('content', 'id' => $model->id)),
  array('label'=>'Email Source', 'url'=>array('source', 'id' => $model->id)),
  array('label'=>'Text Only', 'url'=>array('textOnly', 'id' => $model->id)),
  array('label'=>'Send Test', 'url'=>array('sendTest', 'id' => $model->id), 'active' => true),
);
$this->buildMenu('sendTest', $model->id, $more_menu);
?>

<div class="row">

  <div class="col-md-7">

    <div class="row">
      <div class="col-md-12">

        <?php $form=$this->beginWidget('VActiveForm', array(
          'id'=>'email-test-form',
          'htmlOptions' => array(
            'class' => 'form-inline',
          ),
        )); ?>

          <div class="form-group<?php echo ($formModel->getError('email') ? ' has-error' : ''); ?>">
            <?php echo $form->labelEx($formModel,'email', array('class' => 'sr-only')); ?>
            <?php echo $form->textField($formModel,'email', array(
              'placeholder' => 'Enter email',
            )); ?>
            <?php echo $form->error($formModel,'email'); ?>
          </div>

          <?php echo CHtml::submitButton('Send', array(
            'class' => 'btn btn-primary',
          ));?>

        <?php $this->endWidget(); ?>

      </div>
    </div>

  </div>

</div>
