<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    31/10/13
 *
 * [Free Stuff World] _results.php
 */
$content = Content::model();
?>

<div id="search-results">

  <?php $this->widget('VGridView', array(
    'id' => 'search-results-grid',
    'dataProvider'=>$results['dataProvider'],
    'itemsCssClass' => 'table nowrap table-striped table-condensed table-bordered table-small-text bg-white',
    'ajaxLinks' => true,
    'linkTarget' => '#email-form-container',
    'columns' => array(
      array(
        'name' => 'title',
        'type' => 'raw',
        'value' => "CHtml::link(\$data->title, \$data->emailDirectLink, array(
          'class' => 'link-block',
          'title' => \$data->emailDirectLink,
          'data-behavior' => 'tooltipBS',
          'target' => '_blank',
        ))",
        'header' => 'Title',
        'headerHtmlOptions' => array(
          'class' => 'col-md-4',
        ),
      ),
      array(
        'name' => 'description',
        'header' => 'Description',
        'value' => '$data->tinyDescription',
        'headerHtmlOptions' => array(
          'class' => 'col-md-4',
        ),
      ),
      array(
        'name' => 'last_in_update',
        'header' => 'LIU',
        'value' => "(\$data->last_in_update ? vdate('d/m/Y', \$data->last_in_update) : 'Never')",
        'headerHtmlOptions' => array(
          'class' => 'col-md-2',
          'title' => 'Last in Update',
        ),
      ),
      array(
        'name' => 'type',
        'header' => 'Type',
        'value' => '$data->type->title',
        'headerHtmlOptions' => array(
          'class' => 'col-md-1',
        ),
      ),
      array(
        'class'=>'VButtonColumn',
        'template'=>'{add}',
        'headerHtmlOptions' => array(
          'class' => 'col-md-1',
        ),
        'buttons'=>array(
          'add' => array(
            'label' => 'Add',
            'url' => '"'.preg_replace('/\?.*/', '', Yii::app()->request->requestUri).'?object_id=".$data->id',
            'options' => array(
              'class' => 'btn btn-xs btn-primary',
              'data-behavior' => 'ajaxAddFromSearch',
              'data-target' => '#email-form-container',
            ),
          ),
        ),
      ),
    ),
  )); ?>
</div>
