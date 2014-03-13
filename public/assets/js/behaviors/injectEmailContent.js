/**
 * Author:  Mark O'Keeffe

 * Date:    08/11/13
 *
 * [Yii Workbench]
 */
MOK.Behaviors.injectEmailContent = function ($elem) {

  var url = $elem.data('url'),
    objectIds = $elem.data('objectIds');

  $.get(url, {
    object_ids: objectIds
  }, function(){
    if ($elem.data('reloadIframe')) {
      document.getElementById($elem.data('reloadIframe')).contentDocument.location.reload();
    }
  });

};
