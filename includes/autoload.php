<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
 * 
 * This file is part of aprelendo.
 * 
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

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