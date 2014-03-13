<?php
$this->heading = 'Update Email Layout Fill Behaviour  &#187; '.$model->id;
$this->breadcrumbs=array(
	'Email Layout Fill Behaviours'=>array('index'),
 $model->id,
	'Update',
);
$this->buildMenu('update', $model->id);

echo $this->renderPartial('_form', array('model'=>$model));