# ROADMAP


## ALPHA

- [ ] Finish building basic layout for "My Texts" tab

  - [x] Build basic structure

  - [x] Implement "Delete"

  - [x] Implement search box

  - [ ] Show "total words" & "status"

  - [ ] Implement "mark as read" (mark as read & archive are the same thing? remove archive option?)

  - [x] Correct footer's 4th column

  - [x] Implement "add text" button

    - [x] Show addtext.php inside MyTexts tab & redo layout

  - [ ] Implement pagination

- [x] Restructure folders

- [x] Apply new theme

- [ ] Add "back" button in showtext.php ?

- [ ] Add minutes to read

- [ ] Remove finished reading (file & implementation in showtext.php)?

- [ ] Add audio

- [ ] Add support for more than one language

- [ ] Add support to import rss texts

- [ ] Add support for ebook uploading

- [ ] Add configuration options (select dictionary, fonts, etc.)

## BUGS

- [ ] listtexts.php: deletion not working well when selecting multiple texts

- [ ] listtexts.php: don't show table header when no text in db

- [ ] listtexts.php: enable actions only when at least 1 text is selected

- [ ] listtexts.php: confirm deletion ?

- [ ] listtexts.php: when search fails, focus on input box again (to search once again) ?

- [x] footer.php: are all the bootstrap javascript files loading correctly?

- [ ] showtext.php: show error when user enters without and id ?

- [ ] addtext.php: handle errors (like empty fields, text too long or string too long for title)

## BETA

- [feature] Enable login w/facebook or google

- [feature] Add support for phrases and multi-word selection
