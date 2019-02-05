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
  $('#form_login').on('submit', function (e) {
    e.preventDefault();

    var form_data = $('#form_login').serialize();
    
    $.ajax({
      type: "POST",
      url: "ajax/login.php",
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
      showError('Oops! There was an unexpected error when trying to log you in. Please try again later.');
    }); // end of ajax
  }); // end of #form_login.on.submit


  /**
   * Shows custom error message in the top section of the screen
   * @param {string} error_msg 
   */
  function showError(error_msg) {
    $('#error-msg').text(error_msg)
      .removeClass('d-none')
      .addClass('alert alert-danger');
    $(window).scrollTop(0);
  } // end of showError

});