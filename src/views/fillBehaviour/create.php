<?php
$this->heading = 'Create Email Layout Fill Behaviour';
$this->breadcrumbs=array(
	'Email Layout Fill Behaviours'=>array('index'),
	'Create',
);
$this->buildMenu('create');

echo $this->renderPartial('_form', array('model'=>$model));