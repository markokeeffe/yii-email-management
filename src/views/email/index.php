<?php
$this->heading = 'Manage Emails';
$this->breadcrumbs=array(
  'Emails',
);

$this->buildMenu('index');

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
  $('.search-form').toggle();
  return false;
});
$('.search-form form').submit(function(){
  $.fn.yiiGridView.update('email-grid', {
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
  'id'=>'email-grid',
  'dataProvider'=>$model->search(),
  'filter'=>$model,
  'itemsCssClass' => 'table table-striped table-bordered',
  'columns'=>array(
    array(
      'name' => 'title',
      'type' => 'raw',
      'value' => 'CHtml::link($data->title, array("/emails/email/content", "id"=>$data->id))',
      'footer' => '',
    ),
    array(
      'name' => 'subject',
      'visible' => $this->et->useSubject,
    ),
    array(
      'name' => 'subid_id',
      'visible' => count($this->et->getSubidValues()),
      'value' => function ($data, $row, $col) {
        return $this->et->getSubidValue($data->subid_id);
      },
    ),
    array(
      'name' => 'template_id',
      'value' => '$data->template->title',
    ),
    array(
      'class'=>'VButtonColumn',
      'template'=>'{content}{update}{delete}',
      'buttons'=>array(
        'content' => array(
          'label'=>'<i class="icon-eye-open icon-white"></i> Content',
          'imageUrl'=>false,
          'url' => 'Yii::app()->createUrl("/emails/email/content", array("id"=>$data->id))',
          'options'=>array(
            'title'=>'Preview',
            'class'=>'btn btn-xs btn-success',
          ),
        ),
      ),
    ),
  ),
)); ?>
