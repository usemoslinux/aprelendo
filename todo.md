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

- [ ] Remove finished reading (file & implementation in showtext.php)?

- [ ] Apply new theme

- [ ] Add audio

- [ ] Add support for more than one language

- [ ] Add support to import rss texts

- [ ] Add support for ebook uploading

## BUGS

- [x] footer.php: are all the bootstrap javascript files loading correctly?

- [ ] showtext.php: show error when user enters without and id ?

- [ ] addtext.php: handle errors (like text too long or string too long for title)

## BETA

- [feature] Enable login w/facebook or google

- [feature] Add support for phrases and multi-word selection




<form class="" action="" method="post">
  <div class="input-group searchbox">
    <input type="text" id="search" name="searchtext" class="form-control" placeholder="Search...">
    <div class="input-group-btn">
      <button type="submit" name="submit" class="btn btn-default"><i class="glyphicon glyphicon-search"></i></button>
    </div>
  </div>
</form>
