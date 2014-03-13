<?php

$this->heading = 'Create Email';
$this->breadcrumbs=array(
	'Emails'=>array('index'),
	'Create',
);

$this->buildMenu('create');

echo $this->renderPartial('_form', compact('model', 'templates', 'fillable', 'email_campaign'));
