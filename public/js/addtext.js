$(document).ready(function() {
  $('#form_addtext').on('submit', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("form_addtext"));
    
    $.ajax({
        type: "POST",
        url: "db/addtext.php",
        data: form_data,
        //cache: false,
        dataType: 'json',
        contentType: false,
        processData: false
      })
      .done(function (data) {
        if(data.success_msg != null) {
          window.location.replace('index.php');
        } else {
          showError(data.error_msg);
        }
      })
      .fail(function (xhr, ajaxOptions, thrownError) {
        showError('Oops! There was an unexpected error when uploading this text.');
      });
  });

  function showError(error_msg) {
    $('#alert_error_msg').text(error_msg)
      .removeClass('hidden')
      .addClass('alert alert-danger');
    $(window).scrollTop(0);
  }

  $('#audio_uri').on('change', function() {
    var $input_audio = $(this);
    if ($input_audio[0].files[0].size > 10485760) {
      showError('This file is bigger than the allowed limit (10 MB). Please try again.');
      $input_audio.val('');
    }
  });

  $('#upload_text').on('change', function () {
    var file = $(this)[0].files[0];
    var reader = new FileReader();
    reader.onload = (function(e) {
      var text = e.target.result;
      if (text.length > 65535) {
        showError('This file has more than 65535 characters. Please try again with a shorter one.')
      } else {
        $('#text').val($.trim(text));  
      }
    })
    reader.readAsText(file);
  });

});