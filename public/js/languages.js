$(document).ready(function() {

  /**
  * Deletes selected language and all associated texts
  * @param  {event object} e Used to stop execution if user does not confirm deletion
  */
  $('body').on('click', '#delbtn', function(e) {
    if (!confirm('Are you sure? Please beware that deleting a language will also delete all associated texts.')) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
});
