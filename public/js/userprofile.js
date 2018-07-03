$(document).ready(function () {

  /**
   * Saves user profile information to the database and shows success/failure message
   * It is executed when user clicks the submit button
   * @param  {event object} e Used to prevent reloading of the page
   */
  $('#userprofile-form').submit(function (e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.
    
    $.ajax({
        url: 'db/saveuserprofile.php',
        type: 'post',
        data: $('#userprofile-form').serialize()
      })
      .done(function (data) {
        if (data.error_msg) {
            $('#msgbox').html(data.error_msg)
            .removeClass()
            .addClass('alert alert-danger');    
        } else {
            $('#msgbox').html('<strong>Great!</strong> Your user profile information was successfully saved.')
            .removeClass()
            .addClass('alert alert-success');

            window.location.replace('texts.php'); 
        }
      })
      .fail(function () {
        $('#msgbox').html('<strong>Oops!</strong> Something went wrong when trying to save your user profile information.')
          .removeClass()
          .addClass('alert alert-danger');
      })
      .always(function () {
        $('#password, #newpassword1, #newpassword2').val('');
      });
  });
});