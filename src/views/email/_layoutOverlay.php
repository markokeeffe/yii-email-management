<div class="layout-overlay" data-behavior="hoverToggle" data-hover-target="layout">
  <?php if(isset($layoutRepeated) && $layoutRepeated) : ?>
     <div class="grab-handle"><i class="fa fa-reorder"> </i>move</div>
     <ul class="buttons centered">
       <li>
         <?php echo CHtml::link('Remove', array('sortRepeated',
           'id' => $model->id,
           'layout_id' => $layout->id,
         ), array(
           'data-iframe-behavior' => 'deleteFromSortable',
           'data-selector' => 'layout',
           'data-sortable' => '#repeatable-layout-'.$layout->id.' .items',
           'class' => 'btn btn-danger remove-layout',
         )); ?>
       <li>
         <?php echo CHtml::link('Click to Edit', array('/emails/layout/editContent',
           'id'=>$layout->id,
           'email_id' => $model->id,
           'layout_repeated_id' => $layoutRepeated->id,
         ), array(
           'class'=>'btn btn-success',
           'data-behavior' => 'ajaxLink',
           'data-target' => '#email-form-container',
         )); ?>
       </li>
     </ul>
  <?php elseif (isset($layout->id)) : ?>
    <ul class="buttons centered">
      <li>
        <?php echo CHtml::link('Edit Content', array('/emails/layout/editContent',
          'id'=>$layout->id,
          'email_id' => $model->id,
        ), array(
          'class'=>'btn btn-lg btn-success',
          'data-behavior' => 'ajaxLink',
          'data-target' => '#email-form-container',
        )); ?>
      </li>
    </ul>
  <?php endif; ?>
</div>