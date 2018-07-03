$(document).ready(function () {
  $('#form_forgot_password').on('submit', function (e) {
    e.preventDefault();
    var form_data = $('#form_forgot_password').serialize();
    showMsg('Your request is being procesed. Please wait...', 'success');

    $.ajax({
      type: "post",
      url: "db/forgotpassword.php",
      data: form_data
    })
    .done(function (data) {
      if (data.error_msg == null) {
        showMsg('An email was sent. Access the link and create a new password.', 'success');  
      } else {
        showMsg(data.error_msg, 'error');
      }
    })
    .fail(function (xhr, ajaxOptions, thrownError) {
      showMsg('Oops! There was an unexpected error when trying to register you. Please try again later.', 'error');
    });
  });


  /**
   * Shows custom error message in the top section of the screen
   * @param {string} msg 
   */
  function showMsg(msg, msg_type) {
    var msg_class = msg_type == 'error' ? 'alert-danger' : 'alert-success';
    $('#alert_msg').text(msg)
      .removeClass('hidden')
      .addClass('alert ' + msg_class);
    $(window).scrollTop(0);
  } // end of showError
});