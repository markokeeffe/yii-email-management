<?php
$this->heading = 'View Email Text '.$model->title;
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	$model->title => array('update', 'id' => $model->id),
	'Text Only',
);
$more_menu = array(
  array('label'=>'Email Content', 'url'=>array('content', 'id' => $model->id)),
  array('label'=>'Email Source', 'url'=>array('source', 'id' => $model->id)),
  array('label'=>'Text Only', 'url'=>array('textOnly', 'id' => $model->id), 'active' => true),
  array('label'=>'Send Test', 'url'=>array('sendTest', 'id' => $model->id)),
);
$this->buildMenu('textOnly', $model->id, $more_menu);
?>

<div class="row">

  <div class="col-md-7">
    <iframe id="email-preview-text" class="content" height="1000"
      src="<?php echo $this->createUrl('buildSource', array(
        'id'=>$model->id,
        'type' => 'text',
      )); ?>">
    </iframe>
  </div>

  <div class="col-md-5" id="email-content-forms">

  </div>

</div>
