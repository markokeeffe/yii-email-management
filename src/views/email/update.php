<?php
$this->heading = 'Update Email &#187; '.$model->title;
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	$model->title,
	'Update',
);
$more_menu = array(
  array('label'=>'Email Content', 'url'=>array('content', 'id' => $model->id)),
  array('label'=>'Email Source', 'url'=>array('source', 'id' => $model->id)),
);
$this->buildMenu('update', $model->id, $more_menu);

echo $this->renderPartial('_form', compact('model', 'templates', 'fillable', 'email_campaign'));
