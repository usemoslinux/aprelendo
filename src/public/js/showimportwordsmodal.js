/**
 * Copyright (C) 2019 Pablo Castagnino
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

    let wordsArray = [];

    $('#words-upload-input').on('change', function(e) {
        let file = e.target.files[0];
        let reader = new FileReader();
    
        reader.onload = function(e) {
            let txtFile = e.target.result;
            wordsArray = txtFile.split(/\r?\n|\r/);
            wordsArray = [...new Set(wordsArray)]; // remove duplicates

            // trim all elements and convert them to lower case
            // also, apply filter to remove empty and falsy elements and those including spaces
            wordsArray = wordsArray.map(element => element.trim().toLowerCase())
                .filter(element => element && !element.includes(' '));

            populateTable(wordsArray);
        };
    
        reader.readAsText(file);

        document.getElementById('words-upload-wrap').classList.toggle('d-none');
        document.getElementById('words-table-wrap').classList.toggle('d-none');
        document.getElementById('btn-import-words').disabled = false;
        document.getElementById('words-upload-input').value = '';
    }); // end #words-upload-input.on.change
    
    function populateTable(wordsArray) {
        let tableBody = document.getElementById('words-table').querySelector('tbody');
        tableBody.innerHTML = ''; // Clear existing rows
    
        wordsArray.forEach(item => {
            let row = tableBody.insertRow();
            let cellWord = row.insertCell(0);
    
            cellWord.textContent = item;
        });
    } // end populateTable

    $('#import-words-modal').on('hidden.bs.modal', function () {
        document.getElementById('words-table-wrap').classList.add('d-none');
        document.getElementById('words-upload-wrap').classList.remove('d-none');
        document.getElementById('words-table').querySelector('tbody').innerHTML = '';
        document.getElementById('btn-import-words').disabled = true;
    }) // end #import-words-modal.on.hidden.bs.modal

    $('#btn-import-words').on('click', function() {
        
        $.ajax({
            type: "POST",
            url: "/ajax/addword.php",
            data: {
                words: wordsArray
            }
        })
        .done(function(data) {
            location.reload();
        })
        .fail(function(xhr, ajaxOptions, thrownError) {
            alert('Oops! There was an unexpected error.');
        });
    }); // end #btn-import-words.on.click

    $('#words-upload-wrap').on('click', function(e) {
        e.preventDefault();
        $('#words-upload-input').trigger('click');
    }); // #words-upload-wrap.on.click

    // Drag and drop events
    $('#words-upload-wrap').on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // Add some visual feedback for drag over
        $(this).addClass('words-dropping');
    }); // end #words-upload-wrap.on.dragover

    $('#words-upload-wrap').on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // Remove visual feedback
        $(this).removeClass('words-dropping');
    }); // end #words-upload-wrap.on.dragleave
    
    $('#words-upload-wrap').on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        // Remove visual feedback
        $(this).removeClass('words-dropping');

        // Get the files from the event
        let files = e.originalEvent.dataTransfer.files;

        // Check if any file is uploaded
        if (files.length > 0) {
            // Set the file to the input
            $('#words-upload-input').prop('files', files);

            // Trigger change event
            $('#words-upload-input').trigger('change');
        }
    }); // end #words-upload-wrap.on.drop
});