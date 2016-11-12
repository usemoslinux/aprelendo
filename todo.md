# definitions

- why modal instead of iframe for adding words? iframes are more difficult to show on mobile devices


# bugs

- is it necessary to escape strings when saving? Probably yes because you save them using sql.

## addword.php
- agregar sound URI
- permitir inclusion de imagenes (drag & drop) o image URIs
- guardar imagen (tipo: binario)
- guardar un array de los ID de los tags en vez de los tags mismos?
- hace falta sanitizar los ID tags o el contenido binario?

# roadmap

DONE: subrayar con diferentes colores en showtext.php
DONE: mostrar traduccion como tooltip
DONE: Al hacer click, mostrar ventana de carga
TODO: ventana de carga
  a) vaciar campos al inicio (mudar de donde esta ahora al evento onclick de los elemtnos A)
  b) si la palabra no existe en la db, cargar solo el campo de la palabra
  c) si la palabra existe, cargar todos los campos
  d) al guardar, tiene que verificar si la palabra existe
BUG: pude cargar entradas con un word existente en la db!

TODO: interfaz de aprendizaje




$text = preg_replace("/(?<![a-zA-Zàèìòùâêîôûäëïöü])$word(?![a-zA-Zàèìòùâêîôûäëïöü'’-])(?![^<]*>)/i",
"<span class='word lvl{$lvlno}' data-toggle='tooltip' title='" . utf8_encode($tr) . "'>$0</span>", $text);
