/**
 * Author:  Mark O'Keeffe

 * Date:    07/11/13
 *
 * [Yii Workbench]
 */
MOK.IBehaviors.sortable = function (jQuery, $elem) {


  // Add a sortable to the layout items in the template
  $elem.sortable({
    cursor: 'move',
    items: 'layout',
    axis: 'y',
    handle: '.layout-overlay .grab-handle',
    distance: 10,
    update: function(){
      jQuery.post($elem.data('sortUrl'), $elem.sortable('serialize'));
    }
  });

};
