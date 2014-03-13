/**
 * Author:  Mark O'Keeffe

 * Date:    07/11/13
 *
 * [Yii Workbench]
 */
MOK.IBehaviors.editRepeatable = function (jQuery, $link) {

  var $repeatable = jQuery($link.data('target'));

  $link.on('click', function(){

    // Is the repeatable region open for editing?
    if ($repeatable.hasClass('editing')) {
      // Stop editing
      $repeatable.removeClass('editing');
      return false;
    }

    // Start editing
    $repeatable.addClass('editing');

    return false;
  });

};
