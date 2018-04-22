$('body').on('click', '#delbtn', function(event) {
  if (!confirm('Are you sure? Please beware that deleting a language will also delete all associated texts.')) {
    event.preventDefault();
    event.stopPropagation();
  }
});
