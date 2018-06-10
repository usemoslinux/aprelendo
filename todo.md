## ALPHA 1

- Chequear si las estadisticas funcionan bien
  - Si las palabras que ya aprendi (que no aparecen subrayadas) y que yo vuelvo a agregar aparecen como "forgotten"

  Usar phpmyadmin->sql: SELECT COUNT(word) FROM words WHERE wordStatus=2 AND wordModified>wordCreated AND wordModified < CURDATE() - INTERVAL 0-1 DAY AND wordModified > CURDATE() - INTERVAL 0 DAY

  Esto es diferente de una palabra que estoy aprendiendo, a la cual ley doy clic al boton "forgot meaning".
  Deberian figurar ambas bajo el concepto "forgotten".

- [ ] showtexts.php: sanitize text when opening dictionary & translator URLs

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

- [ ] Add "back" button in showtext.php ?

- [ ] listtexts.php: Allow to edit text ? Hmmm... don't know...

## BETA

- [ ] Enable login w/facebook or google

- [ ] Add support for ebook uploading

- [ ] Enable multi-language site

- [ ] Develop an API and a Firefox addon
