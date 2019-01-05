/**
 * Copyright (C) 2018 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function() {

    // Check for an external api call. If detected, try to fetch text using Mozilla's readability parser
    if ($('#external_call').length) {
        fetch_url();
    }
    
    /**
    * Adds text to database
    * This is triggered when user presses the "Save" button & submits the form
    */
    $('#form-addtext').on('submit', function (e) {
        e.preventDefault();
        
        var form_data = new FormData(document.getElementById("form-addtext"));
        
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
                if (form_data.get('shared-text') == 'on') {
                    window.location.replace('sharedtexts.php');
                } else {
                    window.location.replace('texts.php');    
                }
            }
        })
        .fail(function (xhr, ajaxOptions, thrownError) {
            showError('Oops! There was an unexpected error when uploading this text.');
        }); // end of ajax
    }); // end of #form-addtext.on.submit
    
    
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
     * Makes #btn-upload-audio (button) behave like #audio-uri (input).
     * #audio-uri is the input element used for selecting audio files to upload.
     * It is hidden by default and replaced by a nicer button element (#btn-upload-audio).
     */
    $('#btn-upload-audio').on('click', function () {
        $('#audio-uri').trigger('click');
    });

    /**
    * Checks if the audio file being uploaded is bigger than the allowed limit
    * This is triggered when the user clicks the "upload" audio file button
    */
    $('#audio-uri').on('change', function() {
        var $input_audio = $(this);
        var max_file_size = $('#form-addtext').attr('data-premium') == '0' ? 2097152 : 10485760;
        if ($input_audio[0].files[0].size > max_file_size) {
            showError('This file is bigger than the allowed limit (' + max_file_size / 1048576 + ' MB). ' +
                'Notice that if f you decide to continue the text will be uploaded without an audio file.');
            $input_audio.val('');
        }
    }); // end of #audio-uri.on.change
    
    /**
    * Checks if the text file being uploaded is bigger than the allowed limit
    * This is triggered when the user clicks the "upload" text button
    */
    $('#upload-text').on('change', function () {
        var file = $(this)[0].files[0];
        var reader = new FileReader();
        reader.onload = (function(e) {
            var text = e.target.result;
            if (text.length > 20000) {
                showError('This file has more than 20000 characters. Please try again with a shorter one.')
            } else {
                $('#text').val($.trim(text));  
            }
        })
        reader.readAsText(file);
    }); // end of #upload-text.on.change
    
    /**
    * Fetches text from url using Mozilla's redability parser
    * This is triggered when user clicks the Fetch button or, externally, by bookmarklet/addons calls
    */
    function fetch_url() {
        emptyForm(true);

        var url = $('#url').val();
        
        if (url != '') {
            $('#btn-fetch-img').removeClass().addClass('fas fa-sync fa-spin');
            $.ajax({
                type: "GET",
                url: 'db/fetchurl.php',
                data: { url: url },
                dataType: "html"
            })
            .done(function(data) {
                var doc = document.implementation.createHTMLDocument("New Document");
                doc.body.parentElement.innerHTML = data;
                var article = new Readability(doc).parse();  
                $('#title').val(article.title);
                $('#author').val(article.byline);
                var txt = '';
                var $tempDom = $('<output>').append($.parseHTML(article.content));
                $('p, h1, h2, h3, h4, h5, h6', $tempDom).each(function() {
                    txt += $(this).text().replace(/\s+/g, ' ') + '\n\n';
                });

                txt = txt.replace(/(\n){3,}/g, '\n'); // remove multiple line breaks
                txt = txt.replace(/\t/g, ''); // remove tabs
                // txt = txt.replace(/  /g, ' '); // remove multiple spaces
                
                $('#text').val($.trim(txt)); 
            })
            .fail(function(xhr, ajaxOptions, thrownError) {
                alert('Oops! There was an error trying to fetch this text.');
            })
            .always(function() {
                $('#btn-fetch-img').removeClass().addClass('fas fa-arrow-down');
            }); // end ajax  
        } // end if  
    }
    
    $('#btn-fetch').on('click', fetch_url);
    
});

function emptyForm(exceptSourceURI) {
    $('#alert-error-msg').addClass('hidden');
    $('#type').prop('selectedIndex', 0);
    $('#title').val('');
    $('#author').val('');
    $('#title').val('');
    $('#text').val('');
    $('#upload-text').val('');
    $('#audio-uri').val('');
    if (!exceptSourceURI) {
        $('#url').val('');
    }
    $('#shared-text').prop('checked', false);
}
