## ALPHA 1
- [x] error uploading sound file ...343.mp3

- [x] fix modal padding and height

- [x] fix play rate range width

- [x] remove audio autoplay

- [x] add audio player shortcuts (play, pause)

- [x] error when uploading big file not shown

- [x] listtexts.php: texts are listed from old to new. Shouldn't it be the other way around?

- [ ] showtexts.php: sanitize text when opening dictionary & translator URLs

- [x] implement "archived texts" section

- [ ] add support for ogg audio

- [x] Add support for phrases and multi-word selection

- [x] Fixed CSS, JS & PHP linter warnings

- [ ] Bugs:
    - [x] listtexts.php: don't show table header when removing all texts from table
    - [ ] Add word not working after translating whole paragraph
    - [ ] After editing language, current language gets messed up
    - [x] It still shows audio player even if no audio to be played
    - [ ] Are audio files being properly deleted?
    - [ ] SESSION expires if it takes too much time to add text?

- [ ] Implement sandwich menu

  - [x] Texts

  - [x] Archived texts

  - [ ] Words learning

  - [x] Languages

    >> todo: when adding new language, not checking if all fields are settings
    >> todo: what happens when no active language is set on preferences table?

  - [ ] Statistics (Add statistics and metrics to show progress)
    - Use chart.js or Google Charts (they both have a CDN) and use Jquery
    https://developers.google.com/chart/interactive/docs/php_example

    - Chart:
        - Filter: today, last 7 days, last 30 days, last 365 days
        - Data:
            - New words added (status = 2)
            - Words recalled (0 < status < 2 )
            - Words learnt (status = 0)
            - Forgotten words (status = 3?)
            - Words already known? (another table?)
            - Amount of words per level? (fixed value for each level)

  - [x] Preferences (Add configuration options (select dictionary, fonts, etc.)
    - Appearance
        - Font: Helvetica, Open Sans, Times New Roman, Georgia
        - Size: 5 options?
        - Line height: 5 options
        - Alignment: left, right or justify
        - Mode: light, sepia, dark
    - Dictionary
        - URL
    - Other
        - Highlight common words

- [ ] Implement pagination

- [ ] Add installation instructions to readme.md

- [ ] preferences.php: Remember last reading position

Things that need more thinking

- [ ] AJAX validate addtext.php (http://michaelsoriano.com/how-to-ajax-validate-forms/) ?

- [ ] listtexts.php: more efficient way to delete texts (instead of sending ajax requests for each selected text) ?

- [ ] Error when loading very big file as audio ?

- [ ] Add "back" button in showtext.php ?

- [ ] listtexts.php: Allow to edit text ? Hmmm... don't know...

## BETA

- [x] Add support for more than one language

- [ ] Enable login w/facebook or google

- [ ] Add support to import rss texts

- [ ] Add support for ebook uploading

- [ ] Enable multi-language site

- [ ] Develop an API and a Firefox addon
