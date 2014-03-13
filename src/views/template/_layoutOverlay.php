<div class="layout-overlay show">
  <h2 class="grab-handle"><?php echo CHtml::encode($layout->label); ?></h2>
  <?php if ($layout->mappedObject) : ?>
  <h2 class="layout-mapped">
    <small>Mapped to <?php echo $layout->mappedObject; ?></small>
  </h2>
  <?php endif; ?>
  <?php if ($model->is_fixed) : ?>
    <label for="fill_behaviour_id">Auto Fill Behaviour: </label>
    <?php echo CHtml::dropDownList(
      'fill_behaviour_id',
      $layout->fill_behaviour_id,
      CHtml::listData(EmailLayoutFillBehaviour::model()->findAll(), 'id', 'class'),
      array(
        'prompt' => 'Auto fill behaviour...',
        'class' => 'fill-behaviour',
        'data-behavior' => 'ajaxOnChange',
        'data-url' => $this->createUrl('/emails/layout/fillBehaviour', array('id' => $layout->id))
      )
    ); ?>
  <?php endif; ?>
  <div class="col-lg-5 clearfix form-inline">
    <label for="data_object">Map to object: </label>
    <?php echo CHtml::dropDownList(
      'objectName',
      $layout->mappedObject,
      EmailTagObjectMap::model()->objects,
      array(
        'class' => 'map-layout',
        'data-behavior' => 'ajaxOnChange',
        'data-url' => $this->createUrl('/emails/layout/mapObject', array('id' => $layout->id))
      )
    ); ?>
    &nbsp;
    <?php echo CHtml::link('Default Content', array('/emails/layout/editDefaultContent',
      'id' => $layout->id,
    ), array(
      'class' => 'btn btn-xs btn-primary edit-layout-content',
      'data-behavior' => 'ajaxLink',
      'data-target' => '#email-form-container',
    )); ?>
    <?php echo CHtml::link('Remove', array('sort', 'id' => $model->id), array(
      'data-iframe-behavior' => 'deleteFromSortable',
      'data-selector' => 'layout',
      'data-sortable' => '#template-layout-container',
      'class' => 'btn btn-xs btn-danger remove-layout',
    )); ?>
    <?php echo CHtml::checkBox('is_repeatable', $layout->is_repeatable, array(
      'id' => 'is_repeatable-'.$layout->id,
      'class' => 'is_repeatable',
      'data-behavior' => 'ajaxOnChange',
      'data-url'=>$this->createUrl('/emails/layout/toggleIsRepeatable', array('id' => $layout->id))
    )). CHtml::label('Is repeatable', 'is_repeatable-'.$layout->id); ?> 
  </div>
</div>
