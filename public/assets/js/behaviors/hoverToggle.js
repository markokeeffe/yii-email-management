/**
 * Author:  Mark O'Keeffe

 * Date:    06/11/13
 *
 * [Yii Workbench]
 */
MOK.Behaviors.hoverToggle = function ($elem) {

  var $target = $elem.closest($elem.data('hoverTarget'));

  $target.hover(
    function(){
      $elem.addClass('show');
    },function(){
      $elem.removeClass('show');
    }
  );

};
