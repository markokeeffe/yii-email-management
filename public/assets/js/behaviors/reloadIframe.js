/**
 * Author:  Mark O'Keeffe

 * Date:    06/11/13
 *
 * [Yii Workbench]
 */
MOK.Behaviors.reloadIframe = function ($elem) {

  var iFrame = document.getElementById($elem.data('target'));
  if (!$elem.data('urlAdd')) {
    iFrame.contentDocument.location.reload();
  } else {
    var url = iFrame.contentDocument.location;
    url += $elem.data('urlAdd');
    console.log(url);
    iFrame.contentDocument.location = url;
  }

};
