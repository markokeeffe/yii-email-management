<div class="clearfix well well-small form-inline">
  <h3>Auto-fill Values From <?php echo $model->mappedObject; ?>:</h3>
  <!-- SEARCH -->
  <?php
  $this->widget('VSearchWidget', array(
    // Which class will be used to get result data?
    'className' => $model->mappedObject,
    // Any related models for the above class?
    'classRelations' => array('type'),
    // The model used to run the search form
    'formModel' => new SearchForm,
    'formView' => '_searchForm',

    // The view to display results
    'resultsView' => '_searchResults',
    'resultsPageSize' => 10,
    'dbSort' => array(
      'defaultOrder' => 'last_in_update DESC',
      // Attributes for the results sorting
      'attributes' => array(
        'title',
        'description',
        'last_in_update',
        'type' => array(
          'asc' => 'type.title',
          'desc' => 'type.title DESC',
        ),
        'status',
      ),
    ),
  ));
  ?>
</div>
