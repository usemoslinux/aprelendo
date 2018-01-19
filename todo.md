# ALPHA 0.5

- [x] Restructure folders

- [x] Apply new theme

- [x] Add readme & license

- [x] listtexts.php: Implement "Delete"

- [x] listtexts.php: deletion not working well when selecting multiple texts

- [x] listtexts.php: Implement search box

- [x] listtexts.php: enable actions only when at least 1 text is selected

- [x] Implement "mark as read" (mark as read & archive are the same thing? remove archive option?)

- [x] listtexts.php: confirm deletion

- [x] listtexts.php: focus on input box on page load

- [x] listtexts.php: Fix columns width in main page

- [x] listtexts.php: Delete audio file when user deletes text with audio

- [x] showtext.php: show error when user enters without and id ?

- [x] showtext.php: Finish to implement audio player (now always plays same file)

- [x] showtext.php & finishedreading.php: Fix error when pressing finished reading

- [x] showtext.php: Pause audio when adding word and resume after that

- [x] showtext.php: Modal windows breaks browser history (back)

- [x] showtext.php: Add estimated reading time

- [x] addtext.php: Add source url ?

- [x] addtext.php: Don't allow to update multiple files

- [x] addtext.php: handle errors (like empty fields, text too long or string too long for title)

- [x] addtext.php: What happens if user tries to upload existing file? > when error shown form is not auto-filled

- [x] addtext.php & showtext.php: Add possibility to upload audio & reproduce audio file when text is shown

- [x] header.php: Add logo

- [x] footer.php: are all the bootstrap javascript files loading correctly?


## ALPHA 1

- [ ] AJAX validate addtext.php (http://michaelsoriano.com/how-to-ajax-validate-forms/)

- [ ] Error when loading very big file as audio

- [ ] listtexts.php: don't show table header when removing all texts from table

- [ ] listtexts.php: more efficient way to delete texts (instead of sending ajax requests for each selected text) ?

- [ ] Implement pagination

- [ ] Add "back" button in showtext.php ?

- [ ] Add support for more than one language

- [ ] Add support to import rss texts

- [ ] Add support for ebook uploading

- [ ] Add configuration options (select dictionary, fonts, etc.)

- [ ] Add statistics and metrics to show progress

- [ ] listtexts.php: Allow to edit text ? Hmmm... don't know...

- [ ] Implement sandwich menu

  - [ ] Texts

  - [ ] Archived texts

  - [ ] Languages (hmm... but then how should I filtered the texts shown in listtexts?)

  - [ ] Words learning

  - [ ] Statistics

  - [ ] Preferences

## BETA

- [feature] Enable login w/facebook or google

- [feature] Allow selection of dictionary when adding a word: for some words it is better to have an image, for others a standard dictionary and for others a mere translation (this is especially true for phrases)

  - combobox to change dictionary?

- [feature] Add support for phrases and multi-word selection

- [feature] Enable multi-language site

- [feature] Develop an API and a Firefox addon
