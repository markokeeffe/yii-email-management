<?php
$this->heading = 'Update Email Template &#187; '.$model->title;
$this->breadcrumbs=array(
	'Email Templates'=>array('index'),
	$model->title,
	'Update',
);
$more_menu=array(
	array('label'=>'Configure Email Template', 'url'=>array('configure', 'id'=>$model->id)),
);
$this->buildMenu('update', $model->id, $more_menu);

echo $this->renderPartial('_form', compact('model', 'cm_templates'));
