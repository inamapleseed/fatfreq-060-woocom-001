(function ($) {
  'use strict';
  $(document).ready(function () {

    var files_data = [];
    var extension = '';
    var ipf_name = '';
    var name_backup = '';

    $(document).on("change", 'form.formbuilt input[type="file"]', function (e) {
      $('form.formbuilt').find('[name]').each(function (index, value) {
        var that = $(this),
          name = that.attr('name'),
          value = that.attr('value'),
          type = that.prop('type');
        if (type == 'file') {
          var file = that.prop('files');
          if (file[0]) {
            name_backup = value;
            files_data = file[0];
            var ext = getFileExtension(file[0]['name']);
            extension = '.' + ext;
            ipf_name = name;
          }
        }
      });
      if (files_data != null) {
        var form_data = new FormData();
        form_data.append("ext", extension);
        form_data.append("files", files_data);
        form_data.append("name", ipf_name);
        form_data.append("name_backup", name_backup);
        form_data.append("action", "save_files");
        // console.log(form_data);
        $.ajax({
          type: 'POST',
          url: ajax_object.ajax_url,
          data: form_data,
          processData: false,
          contentType: false
        }).done(function (data) {
          // console.log(data);
          var data = jQuery.parseJSON(data);
          if ($('#' + data.name + '').length) {
            $('#' + data.name + '').attr('href', data.url);
            $('#' + data.name + '').html('Test your file');
          } else {
            $(' <a id="' + data.name + '" href ="' + data.url + '" download="' + data.name + '_file">Test your file</a>').insertAfter($('input[name=' + data.name + ']'));
          }
          var hidden_val = "<a href=" + data.url + "> Test your file </a>";
          $('<input type="hidden" name="' + data.name + '" value="' + hidden_val + '">').insertAfter($('input[name=' + data.name + ']'));
        });
      }
    });

    function getFileExtension(name) {
      var found = name.lastIndexOf('.') + 1;
      return (found > 0 ? name.substr(found) : "");
    }

    if (typeof vpc != 'undefined') {
      if (vpc.preload) {
        var form_data_to_load = vpc.preload;
        // console.log(form_data_to_load);
        if (!$.isEmptyObject(form_data_to_load)) {
          window.vpc_build_preview;
        }
      }
    }

  });

})(jQuery);
