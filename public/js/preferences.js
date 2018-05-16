$(document).ready(function () {

  /**
   * Saves preferences to the database and shows success/failure message
   * It is executed when user clicks the submit button
   * @param  {event object} e Used to prevent reloading of the page
   */
  $('#prefs-form').submit(function (e) {
    $.ajax({
        url: 'db/savepreferences.php',
        type: 'post',
        data: $('#prefs-form').serialize()
      })
      .done(function () {
        $('#msgbox').html('<strong>Great!</strong> Your preferences were successfully saved.')
          .removeClass()
          .addClass('alert alert-success')
          .fadeIn(2000, function () {
            $(this).fadeOut(2000);
          });
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        $('#msgbox').html('<strong>Oops!</strong> Something went wrong when trying to save your preferences.')
          .removeClass()
          .addClass('alert alert-danger')
          .fadeIn(2000, function () {
            $(this).fadeOut(2000);
          });
      });

    e.preventDefault(); // avoid to execute the actual submit of the form.
  });
});