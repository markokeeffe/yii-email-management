<?php
$this->heading = 'Create Email Template';
$this->breadcrumbs=array(
	'Email Templates'=>array('index'),
	'Create',
);
$this->buildMenu('create');

echo $this->renderPartial('_form', compact('model', 'cm_templates'));
