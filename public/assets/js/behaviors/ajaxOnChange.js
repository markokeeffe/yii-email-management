/**
 * Author:  Mark O'Keeffe

 * Date:    05/11/13
 *
 * [Yii Workbench]
 */
MOK.Behaviors.ajaxOnChange = function ($elem) {

  var url = $elem.data('url');

  $elem.on('change', function(){
    var data = {};
    data[$elem.attr('name')] = $elem.val();

    $.ajax({
      type: 'GET',
      url: url,
      data: data,
      success: function(rtn) {
        if (rtn.type == 'modal') {
          MOK.Functions.modal(rtn.msg);
        }
      }
    });

  });

};
