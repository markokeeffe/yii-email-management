<?php
$this->heading = 'Configure Email Template '.$model->title;
$this->breadcrumbs=array(
  'Email Templates'=>array('index'),
  $model->title=>array('update', 'id'=>$model->id),
  'Update',
);
$more_menu=array(
  array('label'=>'Configure Email Template', 'url'=>array('configure', 'id'=>$model->id), 'active' => true),
);
$this->buildMenu('configure', $model->id, $more_menu);
?>

<style type="text/css">
  .iframe-container {
    position: relative;
  }
  .iframe-content-loading {
    display: none;
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background: rgba(255,255,255,0.9);
    text-align: center;
  }
  .iframe-content-loading h2 {
    line-height: 200px;
  }
  .iframe-content-loading.show {
    display: block;
  }
</style>

<h1>Email Template - <?php echo $model->title; ?></h1>

<div class="row">

  <div class="col-md-7">

    <div class="iframe-container">
      <div class="iframe-content-loading">
        <h2>Loading email content <i class="fa fa-spinner fa-spin"></i></h2>
      </div>

      <iframe
        id="email-preview"
        data-src="<?php echo $this->createUrl('buildSource', array(
          'id'=>$model->id,
        )); ?>"
        data-css-url="<?php echo $this->module->cssUrl;?>"
        data-jquery-url="<?php echo $this->module->jQueryUrl;?>"
        data-jquery-ui-url="<?php echo $this->module->jQueryUiUrl;?>"
        height="1500">
      </iframe>
    </div>

  </div>

  <div class="col-md-5">

    <div id="email-form-container">

    </div>

    <?php if (!$model->is_fixed) : ?>

      <h2>Available Layouts</h2>

      <?php foreach ($layouts as $layout) : ?>
        <strong class="email-layout clearfix">
          <span><?php echo $layout; ?></span>
          <?php echo CHtml::link('<i class="icon-plus"></i> Add', array('addLayout',
            'id' => $model->id,
            'label' => $layout,
          ), array(
            'class' => 'btn btn-default btn-xs pull-right',
          )); ?>
        </strong>
      <?php endforeach; ?>

    <?php endif; ?>

  </div>

</div>