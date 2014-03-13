<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    31/10/13
 *
 * [Free Stuff World] _form.php
 */
$types = CHtml::listData(
  ContentType::model()->cache()->findAll(), 'id', 'title'
);
?>
<?php $form=$this->beginWidget('CActiveForm', array(
  'id'=>'search-form',
  'method' => 'GET',
  'action' => preg_replace('/\?.*/', '', Yii::app()->request->requestUri),
  'enableAjaxValidation'=>false,
  'htmlOptions' => array(
    'class' => 'form-inline',
    'data-behavior' => 'ajaxSearch',
    'data-target' => '#email-form-container',
  ),
));?>

  <div class="form-group">
    <?php echo $form->labelEx($model,'q', array(
      'class' => 'sr-only',
    )); ?>
    <?php echo $form->textField($model,'q', array(
      'name' => 'q',
      'id' => CHtml::modelName($model).'_q',
      'placeholder' => 'Search...',
      'class' => 'form-control input-sm',
      'data-behavior' => 'focus',
    )); ?>
    <?php echo $form->error($model,'q'); ?>
  </div>

  <div class="search-filters form-inline-container">

    <div class="form-group">
      <?php echo $form->label($model, 'type_id', array(
        'class' => 'sr-only',
      )); ?>
      <?php echo $form->dropDownList($model, 'type_id', $types, array(
        'name' => 'type_id',
        'id' => CHtml::modelName($model).'_type_id',
        'prompt' => 'Content type...',
        'class' => 'form-control input-sm',
        'data-behavior' => 'toggleHidden',
        'data-listener' => 'change',
        'data-selector' => '#page-opts',
        'data-condition' => array_search('Page', $types),
      )); ?>
    </div>

    <div class="form-group">
      <?php echo $form->label($model, 'category_id', array(
        'class' => 'sr-only',
      )); ?>
      <?php echo $form->dropDownList($model, 'category_id', CHtml::listData(
        Category::model()->findAll(), 'id', 'title'
      ), array(
        'name' => 'category_id',
        'id' => CHtml::modelName($model).'_category_id',
        'prompt' => 'Category...',
        'class' => 'form-control input-sm',
      )); ?>
    </div>

    <div class="form-group">
      <?php echo $form->label($model, 'is_aff_link', array(
        'class' => 'sr-only',
      )); ?>
      <?php echo $form->dropDownList($model, 'is_aff_link', array(
        0 => 'Genuine freebie',
        1 => 'Affiliate link',
      ), array(
        'name' => 'is_aff_link',
        'id' => CHtml::modelName($model).'_is_aff_link',
        'prompt' => 'Offer type...',
        'class' => 'form-control input-sm',
      )); ?>
    </div>

    <div id="page-opts" class="checkbox" <?php echo ($model->is_admin_page == '' ? 'style="display: none;"' : ''); ?>>
      <?php echo $form->label($model, 'is_admin_page', array(
        'class' => 'sr-only',
      )); ?>
      <?php echo $form->dropDownList($model, 'is_admin_page', array(
        0 => 'Article',
        1 => 'Index page',
      ), array(
        'name' => 'is_admin_page',
        'id' => CHtml::modelName($model).'_is_admin_page',
        'prompt' => 'Page type...',
        'class' => 'form-control input-sm',
      )); ?>
    </div>

  </div>

  <?php echo CHtml::submitButton('Go', array(
    'class' => 'btn btn-sm btn-primary',
  )); ?>

<?php $this->endWidget(); ?>
