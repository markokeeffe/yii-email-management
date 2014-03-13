///**
// * Author:  Mark O'Keeffe
//
// * Date:    05/11/13
// *
// * [Yii Workbench]
// */
//MOK.Behaviors.emailTemplateConfigure = function($iframe) {
//
//  $iframe.load(function(){
//    var
//      win = this.contentWindow,
//      doc = win.document,
//      body = doc.body,
//      jQueryLoaded = false,
//      jQuery,
//      cssUrl = $iframe.data('cssUrl'),
//      jqueryUrl = $iframe.data('jqueryUrl'),
//      jqueryUiUrl = $iframe.data('jqueryUiUrl');
//
//    // Add the 'email_templates.css' stylesheet to the head of the iFrame
//    $(doc.head).append('<link rel="stylesheet" href="'+cssUrl+'email-templates.css" type="text/css" />');
//    // Add the Twitter Bootstrap buttons stylesheet to the head of the iFrame
//    $(doc.head).append('<link rel="stylesheet" href="'+cssUrl+'bootstrap-buttons.css" type="text/css" />');
//
//    function loadJQueryUI() {
//      body.removeChild(jQuery);
//      jQuery = null;
//
//      win.jQuery.ajax({
//        url: $iframe.data('jqueryUiUrl'),
//        dataType: 'script',
//        cache: true,
//        success: function () {
//
//          var $repeater = win.jQuery('repeater', $contents);
//
//          // Add a sortable to the layout items in the template
//          $repeater.sortable({
//            cursor: 'move',
//            items: 'layout',
//            axis: 'y',
//            handle: '.overlay h2',
//            distance: 10,
//            update: function(){
//              $.post($iframe.data('sortUrl'), $repeater.sortable('serialize'));
//            }
//          });
//
//        }
//      });
//    }
//
//    jQuery = doc.createElement('script');
//
//    // based on https://gist.github.com/getify/603980
//    jQuery.onload = jQuery.onreadystatechange = function () {
//      if ((jQuery.readyState && jQuery.readyState !== 'complete' && jQuery.readyState !== 'loaded') || jQueryLoaded) {
//        return false;
//      }
//      jQuery.onload = jQuery.onreadystatechange = null;
//      jQueryLoaded = true;
//      loadJQueryUI();
//    };
//
//    jQuery.src = $iframe.data('jqueryUrl');
//    $body.append(jQuery);
//    body.appendChild(jQuery);
//
//  }).prop('src', $iframe.data('src'));
//
//
//};
//
//
//
//// HOWTO: load LABjs itself dynamically!
//// inline this code in your page to load LABjs itself dynamically, if you're so inclined.
//
//(function (global, oDOC, handler) {
//  var head = oDOC.head || oDOC.getElementsByTagName("head");
//
//  function LABjsLoaded() {
//    // do cool stuff with $LAB here
//  }
//
//  // loading code borrowed directly from LABjs itself
//  setTimeout(function () {
//    if ("item" in head) { // check if ref is still a live node list
//      if (!head[0]) { // append_to node not yet ready
//        setTimeout(arguments.callee, 25);
//        return;
//      }
//      head = head[0]; // reassign from live node list ref to pure node ref -- avoids nasty IE bug where changes to DOM invalidate live node lists
//    }
//    var scriptElem = oDOC.createElement("script"),
//      scriptdone = false;
//    scriptElem.onload = scriptElem.onreadystatechange = function () {
//      if ((scriptElem.readyState && scriptElem.readyState !== "complete" && scriptElem.readyState !== "loaded") || scriptdone) {
//        return false;
//      }
//      scriptElem.onload = scriptElem.onreadystatechange = null;
//      scriptdone = true;
//      LABjsLoaded();
//    };
//    scriptElem.src = "/path/to/LAB.js";
//    head.insertBefore(scriptElem, head.firstChild);
//  }, 0);
//
//  // required: shim for FF <= 3.5 not having document.readyState
//  if (oDOC.readyState == null && oDOC.addEventListener) {
//    oDOC.readyState = "loading";
//    oDOC.addEventListener("DOMContentLoaded", handler = function () {
//      oDOC.removeEventListener("DOMContentLoaded", handler, false);
//      oDOC.readyState = "complete";
//    }, false);
//  }
//})(window, document);
