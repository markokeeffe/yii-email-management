<?php
$this->heading = 'View Email Source '.$model->title;
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	$model->title => array('update', 'id' => $model->id),
	'View Source',
);
$more_menu = array(
  array('label'=>'Email Content', 'url'=>array('content', 'id' => $model->id)),
  array('label'=>'Email Source', 'url'=>array('source', 'id' => $model->id), 'active' => true),
  array('label'=>'Text Only', 'url'=>array('textOnly', 'id' => $model->id)),
  array('label'=>'Send Test', 'url'=>array('sendTest', 'id' => $model->id)),
);
$this->buildMenu('source', $model->id, $more_menu);
?>

<div class="row">

  <div class="col-md-7">
    <iframe id="email-preview-source" class="content" height="1000"
      src="<?php echo $this->createUrl('buildSource', array(
        'id'=>$model->id,
        'type' => 'source',
      )); ?>">
    </iframe>
  </div>

  <div class="col-md-5" id="email-content-forms">

  </div>

</div>
