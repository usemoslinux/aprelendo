$(document).ready(function () {
    emptyForm();

    $("#btn-upload-epub").on("click", function() {
        $("#url").trigger("click");
    });

    $('#url').on('change', function() {
        $('#alert-error-msg').addClass('hidden');
        
        var $epub_file = $(this);
        var file_name = $epub_file[0].files[0].name.split('.');
        var ext = file_name.pop().toLowerCase();
 
        if ($epub_file[0].files[0].size > 10485760) {
            showError('This file is bigger than the allowed limit (10 MB). Please try again.');
            $epub_file.val('');
        } else if (ext != 'epub') {
            showError('Invalid file extension. Only .epub files are allowed.');
            $epub_file.val('');
        } else {
            if ($('#title').val() == '') {
                $('#title').val(file_name);    
            }
        }
    }); // end of #file-upload-epub.on.change


    /**
    * Adds text to database
    * This is triggered when user presses the "Save" button & submits the form
    */
   $('#form-addebook').on('submit', function (e) {
        e.preventDefault();
        
        var form_data = new FormData(document.getElementById("form-addebook"));
        
        $.ajax({
            type: "POST",
            url: "db/addtext.php",
            data: form_data,
            dataType: 'json',
            contentType: false,
            processData: false
        })
        .done(function (data) {
            if(typeof data != 'undefined') {
                showError(data.error_msg);
            } else {
                window.location.replace('texts.php');
            }
        })
        .fail(function (xhr, ajaxOptions, thrownError) {
            showError('Oops! There was an unexpected error uploading this text.');
        }); // end of ajax
    }); // end of #form-addebook.on.submit


    /**
    * Shows custom error message in the top section of the screen
    * @param {string} error_msg 
    */
   function showError(error_msg) {
        $('#alert-error-msg').html(error_msg)
        .removeClass('hidden')
        .addClass('alert alert-danger');
        $(window).scrollTop(0);
    } // end of showError


    /**
     * Empties form input fields
     */
    function emptyForm() {
        $('#alert-error-msg').addClass('hidden');
        $('#title').val('');
        $('#author').val('');
        $('#url').val('');
    }
});