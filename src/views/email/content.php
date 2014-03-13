<?php
$this->heading = 'Manage Email Content '.$model->title;
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	$model->title => array('update', 'id' => $model->id),
	'Email Content',
);
$more_menu = array(
  array('label'=>'Email Content', 'url'=>array('content', 'id' => $model->id), 'active' => true),
  array('label'=>'View Source', 'url'=>array('source', 'id' => $model->id)),
  array('label'=>'Text Only', 'url'=>array('textOnly', 'id' => $model->id)),
  array('label'=>'Send Test', 'url'=>array('sendTest', 'id' => $model->id)),
);
$this->buildMenu('content', $model->id, $more_menu);
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

<div class="row" >

  <div class="col-md-7">
    <div id=email-container>

      <div class="iframe-container">

        <div class="iframe-content-loading">
          <h2>Loading email content <i class="fa fa-spinner fa-spin"></i></h2>
        </div>

        <iframe
          id="email-preview"
          data-src="<?php echo $this->createUrl('buildSource', array(
            'id'=>$model->id,
            'type' => 'preview',
            'overlays' => 1,
          )); ?>"
          data-css-url="<?php echo $this->module->cssUrl;?>"
          data-jquery-url="<?php echo $this->module->jQueryUrl;?>"
          data-jquery-ui-url="<?php echo $this->module->jQueryUiUrl;?>"
          data-sort-url="<?php echo $this->createUrl('sort', array(
            'id' => $model->id,
          )); ?>"
          class="template"
          height="1500">
        </iframe>
      </div>
    </div>
  </div>
  <div class="col-md-5">

    <?php if ($model->is_injectable && $model->repeatableLayout) : ?>
      <?php $this->renderPartial($this->module->injectContent['form'], array(
        'model' => new $this->module->injectContent['model'],
        'injectUrl' => $this->createUrl('/emails/layout/injectContent', array(
          'id' => $model->repeatableLayout->id,
          'email_id' => $model->id,
        )),
      )); ?>
    <?php endif; ?>

    <?php if ($model->fill_form) : ?>
      <?php $this->renderPartial($model->fill_form, array(
        'action' => $this->createUrl('/emails/email/autoFill', array(
          'id' => $model->id,
        )),
      )); ?>
    <?php endif; ?>


    <div id="email-form-container">

    </div>
  </div>

</div>