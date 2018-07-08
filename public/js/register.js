$(document).ready(function () {
  $('#form_register').on('submit', function (e) {
    e.preventDefault();

    var form_data = $('#form_register').serialize();
    
    $.ajax({
      type: "POST",
      url: "db/register.php",
      data: form_data
    })
    .done(function (data) {
      if(data.error_msg == null) {
        window.location.replace('texts.php');
      } else {
        showError(data.error_msg);
      }
    })
    .fail(function (xhr, ajaxOptions, thrownError) {
      showError('Oops! There was an unexpected error when trying to register you. Please try again later.');
    }); // end of ajax
  }); // end of #form_register.on.submit


  /**
   * Shows custom error message in the top section of the screen
   * @param {string} error_msg 
   */
  function showError(error_msg) {
    $('#error-msg').text(error_msg)
      .removeClass('hidden')
      .addClass('alert alert-danger');
    $(window).scrollTop(0);
  } // end of showError

});