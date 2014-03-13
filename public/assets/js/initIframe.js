/**
 * Author:  Mark O'Keeffe

 * Date:    06/11/13
 *
 * [Yii Workbench]
 */

var
$emailPreview = $('#email-preview'),
$loading = $('.iframe-content-loading'),
emailPreview = {
  sourceUrl: $emailPreview.data('src'),
  cssBaseUrl: $emailPreview.data('cssUrl'),
  jQueryUrl: $emailPreview.data('jqueryUrl'),
  jQueryUiUrl: $emailPreview.data('jqueryUiUrl'),
  sortUrl: $emailPreview.data('sortUrl')
};

MOK.IBehaviors = {};

// Show the loading overlay
$loading.addClass('show');

/**
 * Search the DOM for elements using a 'data-behavior' attribute
 * Load the behaviour script files to execute the necessary behaviours
 * and activate the behaviours when the script finishes loading
 *
 * @param jQuery
 * @param context Optional context in which to find elements
 *
 */
MOK.loadIframeBehaviors = function(jQuery, context){

  // Find all behaviours within the provided context
  MOK.iframeBehaviors = context.find("*[data-iframe-behavior]");

  // Run the behavior functions on their attached objects
  // Find all elements with the 'data-behavior' attribute
  MOK.iframeBehaviors.each(function(){
    var $that = jQuery(this),
      behaviors = $that.attr('data-iframe-behavior');
    $.each(behaviors.split(' '),function(index,behaviorName){
      try{
        // Load the behavior functions and execute
        var BehaviorClass = MOK.IBehaviors[behaviorName];
        var initializedBehavior = new BehaviorClass(jQuery, $that);
      } catch(e){
        // No Operation
      }
    });
  });

};

/**
 * When the iFrame loads...
 */
$emailPreview.load(function() {
  var win = this.contentWindow,
    doc = win.document,
    body = doc.body,
    jQueryLoaded = false,
    jQuery;

  // Add a loading overlay to the email content
  $(body).append('<div class="iframe-content-loading">Loading email content...</div>');
  // Add the 'email_templates.css' stylesheet to the head of the iFrame
  $(doc.head).append('<link rel="stylesheet" href="'+emailPreview.cssBaseUrl+'email-templates.css" type="text/css" />');
//  // Add the ink template css
//  $(doc.head).append('<link rel="stylesheet" href="'+emailPreview.cssBaseUrl+'ink.css" type="text/css" />');
  // Add the Twitter Bootstrap buttons stylesheet to the head of the iFrame
  $(doc.head).append('<link rel="stylesheet" href="'+emailPreview.cssBaseUrl+'bootstrap-buttons.css" type="text/css" />');

  function loadJQueryUI() {
    body.removeChild(jQuery);
    jQuery = null;

    win.jQuery.ajax({
      url: emailPreview.jQueryUiUrl,
      dataType: 'script',
      cache: true,
      success: function () {

        // Load Iframe specific behaviours
        MOK.loadIframeBehaviors(win.jQuery, win.jQuery(body));

        // Load normal behaviours
        MOK.loadBehavior($emailPreview.contents());

        // Hide the loading overlay
        $loading.removeClass('show');

      }
    });
  }

  jQuery = doc.createElement('script');

  // based on https://gist.github.com/getify/603980
  jQuery.onload = jQuery.onreadystatechange = function () {
    if ((jQuery.readyState && jQuery.readyState !== 'complete' && jQuery.readyState !== 'loaded') || jQueryLoaded) {
      return false;
    }
    jQuery.onload = jQuery.onreadystatechange = null;
    jQueryLoaded = true;
    loadJQueryUI();
  };

  jQuery.src = emailPreview.jQueryUrl;
  body.appendChild(jQuery);
}).prop('src', emailPreview.sourceUrl);
