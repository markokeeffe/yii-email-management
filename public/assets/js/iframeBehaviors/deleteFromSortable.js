/**
 * Author:  Mark O'Keeffe

 * Date:    06/11/13
 *
 * [Yii Workbench]
 */
MOK.IBehaviors.deleteFromSortable = function (jQuery, $link) {

  var $elem = $link.closest($link.data('selector')),
    $sortable = jQuery($link.data('sortable'));

  $link.on('click', function(){

    // Remove the element HTML from the page
    $elem.remove();

    // Serialize the sortable elements and POST to re-save the positions
    // and delete the missing object
    jQuery.post($link.attr('href'), $sortable.sortable('serialize'));

    return false;
  });

};
