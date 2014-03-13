<?php
$this->heading = 'Manage Email Templates';
$this->breadcrumbs=array(
  'Email Templates',
);
$this->buildMenu('index');

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
  $('.search-form').toggle();
  return false;
});
$('.search-form form').submit(function(){
  $.fn.yiiGridView.update('email-template-grid', {
    data: $(this).serialize()
  });
  return false;
});
");
?>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn btn-sm btn-info pull-right')); ?>
<div class="search-form well well-light" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('VGridView', array(
  'id'=>'email-template-grid',
  'dataProvider'=>$model->search(),
  'filter'=>$model,
  'itemsCssClass' => 'table table-striped table-bordered',
  'columns'=>array(
    'title',
    array(
      'class'=>'VButtonColumn',
      'template'=>'{configure}{update}{delete}',
      'buttons'=>array(
        'configure' => array(
          'label'=>'<i class="icon-edit icon-white"></i> Configure',
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("/emails/template/configure", array("id"=>$data->id))',
          'options'=>array(
            'title'=>'Configure',
            'class'=>'btn btn-xs btn-success',
          ),
        ),
      ),
    ),
  ),
)); ?>
