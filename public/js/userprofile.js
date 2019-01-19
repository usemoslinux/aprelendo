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

$(document).ready(function () {

  /**
   * Saves user profile information to the database and shows success/failure message
   * It is executed when user clicks the submit button
   * @param  {event object} e Used to prevent reloading of the page
   */
  $('#userprofile-form').submit(function (e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.
    
    $.ajax({
        url: 'ajax/saveuserprofile.php',
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

  $('#btn-delete-account').on('click', function () {
    $(document).find('#delete-account-modal').modal('show');
  });

  $('#btn-confirm-delete-account').on('click', function () {
    $.ajax({
        url: 'ajax/deleteaccount.php',
        type: 'post',
        data: $('#userprofile-form').serialize()
      })
      .done(function (data) {
        if (data.error_msg) {
            $('#msgbox').html(data.error_msg)
            .removeClass()
            .addClass('alert alert-danger');    
        } else {
            window.location.replace('index.php'); 
        }
      })
      .fail(function () {
        $('#msgbox').html('<strong>Oops!</strong> Something went wrong when trying to delete your user account.')
          .removeClass()
          .addClass('alert alert-danger');
      });
  });
});