<?php
/**
 * Author:  Mark O'Keeffe

 * Date:    07/11/13
 *
 * [Yii Workbench] _repeatableLayoutOverlay.php
 */
?>
<div id="repeatable-layout-<?php echo $layout->id; ?>" class="repeatable-layout<?php echo ($layout->id == $editing_id ? ' editing' : ''); ?>">
  <div class="buttons centered show-on-edit">
    <?php echo CHtml::link('Exit', '#', array(
      'class'=>'btn btn-default btn-block',
      'data-iframe-behavior' => 'editRepeatable',
      'data-target' => '#repeatable-layout-'.$layout->id,
    )); ?>
  </div>
  <div class="items"
       data-iframe-behavior="sortable"
       data-sort-url="<?php echo $this->createUrl('sortRepeated', array(
         'id' => $model->id,
         'layout_id' => $layout->id,
       )); ?>">
  </div>
  <div class="buttons centered show-on-edit">
    <?php echo CHtml::link('Add', array('addRepeated',
      'id' => $model->id,
      'layout_id' => $layout->id
    ), array(
      'class'=>'btn btn-success btn-block',
      'data-behavior' => 'ajaxLink',
    )); ?>
    <?php echo CHtml::link('Exit', '#', array(
      'class'=>'btn btn-default btn-block',
      'data-iframe-behavior' => 'editRepeatable',
      'data-target' => '#repeatable-layout-'.$layout->id,
    )); ?>
  </div>
  <div class="layout-overlay repeatable-overlay"
       data-behavior="hoverToggle hitTarget"
       data-hover-target=".repeatable-layout"
       data-iframe-behavior="editRepeatable"
       data-target="#repeatable-layout-<?php echo $layout->id; ?>"
  >
    <ul class="buttons centered">
      <li>
        <?php echo CHtml::link('Manage Repeating Content', '#', array(
          'id' => 'manage-'.$layout->id,
          'class'=>'btn btn-lg btn-info centered edit',
        )); ?>
      </li>
    </ul>
  </div>
</div>
