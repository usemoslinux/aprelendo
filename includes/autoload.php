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
    $path = dirname(APP_ROOT) . '/' . strtolower($class) . '.php';
    require_once $path; 
}

?>