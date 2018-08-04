$(document).ready(function () {
    
    /**
    * Saves preferences to the database and shows success/failure message
    * It is executed when user clicks the submit button
    * @param  {event object} e Used to prevent reloading of the page
    */
    $('#prefs-form').submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        
        $.ajax({
            url: 'db/savepreferences.php',
            type: 'post',
            data: $('#prefs-form').serialize()
        })
        .done(function (data) {
            if (data.error_msg) {
                $('#msgbox').html(data.error_msg)
                .removeClass()
                .addClass('alert alert-danger');    
            } else {
                $('#msgbox').html('<strong>Great!</strong> Your preferences were successfully saved.')
                .removeClass()
                .addClass('alert alert-success');
                
                window.location.replace('texts.php'); 
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $('#msgbox').html('<strong>Oops!</strong> Something went wrong when trying to save your preferences.')
            .removeClass()
            .addClass('alert alert-danger');
        });
    });
});