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
    $class_name = str_replace('\\', '/', $class);
    require_once dirname(APP_ROOT) . '/' . strtolower($class_name) . '.php'; 
}

?>