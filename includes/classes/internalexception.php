<?php
/**
* Copyright (C) 2019 Pablo Castagnino
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

namespace Aprelendo\Includes\Classes;

use Exception;

class InternalException extends Exception {
    public function getJsonError(): string
    {
        return $this->encodeJsonError('Oops! There was an unexpected error processing your request.');
    }

    protected function encodeJsonError(string $error_msg): string
    {
        $error = ['error_msg' => $error_msg];
    
        // Set the JSON content type header
        header('Content-Type: application/json');
    
        // Encode the error array as JSON and return it
        return json_encode($error);
    }
}
