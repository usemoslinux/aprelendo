<?php 

spl_autoload_register('AutoLoader');

/**
 * Imports files based on the namespace as folder and class as filename.
 *
 * @param string $class
 * @return void
 */
function AutoLoader($class)
{
    $class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require_once dirname(APP_ROOT) . DIRECTORY_SEPARATOR . strtolower($class_name) . '.php';
}

?>