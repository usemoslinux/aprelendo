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