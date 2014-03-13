<?php
$this->heading = 'Manage Email Layout Fill Behaviours';
$this->breadcrumbs=array(
	'Email Layout Fill Behaviours'=>array('index'),
	'Create',
);
$this->buildMenu('index');
?>

<?php $this->widget('VGridView', array(
  'id'=>'email-layout-fill-behaviour-grid',
  'dataProvider'=>$model->search(),
  'filter'=>$model,
  'itemsCssClass' => 'table table-striped table-bordered',
  'columns'=>array(
    'id',
    'class',
    array(
      'class'=>'VButtonColumn',
      'template'=>'{update}{delete}',
    ),
  ),
)); ?>
