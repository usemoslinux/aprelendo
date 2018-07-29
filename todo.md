## ALPHA 1

- esta borrando bien los archivos de audio? hay muchos acumulados...

- fixes #68, fixes #69, fixes #66, fixes #60

- calculate level for texts
 - https://www.universeofmemory.com/how-many-words-you-should-know/
 - http://www.php.net/manual/en/function.array-intersect.php
 - http://php.net/preg_match_all

- active reading
  - https://www.fluentinmandarin.com/content/making-your-foreign-language-study-effective-active-not-passive/

- get user location for "my profile":
  - https://nominatim.openstreetmap.org/reverse.php?format=json&lat=50.842750099999996&lon=4.3515499&zoom=18

- export to csv
  - https://stackoverflow.com/questions/125113/php-code-to-convert-a-mysql-query-to-csv (2da respuesta)
  - https://stackoverflow.com/questions/16251625/how-to-create-and-download-a-csv-file-from-php-script

- lazy loading colorizewords
    - Jquery plugin: http://morr.github.io/appear.html

- Chequear si las estadisticas funcionan bien
  - Si las palabras que ya aprendi (que no aparecen subrayadas) y que yo vuelvo a agregar aparecen como "forgotten"

  Usar phpmyadmin->sql: SELECT COUNT(word) FROM words WHERE wordStatus=2 AND wordModified>wordCreated AND wordModified < CURDATE() - INTERVAL 0-1 DAY AND wordModified > CURDATE() - INTERVAL 0 DAY

  Esto es diferente de una palabra que estoy aprendiendo, a la cual ley doy clic al boton "forgot meaning".
  Deberian figurar ambas bajo el concepto "forgotten".


- que pasa cuando recien empiezo a usar el programa y $_COOKIE['actlangid'] todavia no tiene ningun valor? Ver lugares donde uso esta variable.

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

- [ ] Add installation instructions to readme.md

- [ ] preferences.php: Remember last reading position

- firefoxx & chrome addon
  - ya lo cree. esta en la carpeta /private/langx-addon
  - 

- replace audio player with jplayer?
  - http://jplayer.org/ >> medio viejo
  - https://plyr.io/ >> tiene playback speed pero no me gusta el menu de seleccion, demasiado armatoste
  - http://www.mediaelementjs.com/ >> no veo que tenga playback speed controls
  - vertical slider
    - https://codepen.io/ATC-test/pen/myPNqW
    
- Video support
 - https://video.google.com/timedtext?lang=en&v=VIDEOID
 - youtube api AIzaSyCrLewIG56vdL5TN4ls4S4E64aRogUaiz0
 - https://www.googleapis.com/youtube/v3/videos?id=8zhYDFjniTo&key=AIzaSyCrLewIG56vdL5TN4ls4S4E64aRogUaiz0&part=snippet


Things that need more thinking

- [ ] Add "back" button in showtext.php ?

- [ ] listtexts.php: Allow to edit text ? Hmmm... don't know...

## BETA

- [ ] Enable login w/facebook or google

- [ ] Add support for ebook/large texts uploading

- [ ] Create landing page

- [ ] More professional design of main pages

- [ ] Enable multi-language site

- [ ] Develop Firefox/Google Chrome addon

# PRICING

- [ ] Exclusive for premium

  - [ ] Access to community shared texts (this might hinder viralization)
  - [ ] Practice using cards
  - [ ] Upload more than 2 texts per day
  - [ ] Upload bigger audio files
  - [ ] 

- hosting: gnutransfer => precio basico: 10 USD x mes
- perfomance: 20000 characters (FR) => 2,68 secs (777 entries in words table + freqlist on) (indexeddb freqlist table?)

# OBJECTIVE FOR BETA

- [ ] Allow teachers/bloggers to upload their material. This will increase content quality & it will be a way to monetize?

- [ ] En la medida en que es para aprender hace falta la inmersion, el sistema tiene que permitir esto:
  - Agregar funcionalidad tipo skype: chat + video con amigos






