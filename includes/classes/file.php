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

use Aprelendo\Includes\Classes\AprelendoException;

class File
{
    protected $allowed_extensions = [];
    protected $max_size           = 0;
    protected $name               = '';
    protected $folder             = '';
    protected $path               = '';
    protected $extension          = '';
    protected $size               = 0;

    /**
     * Constructor
     * @param string $file_name
     */
    public function __construct(string $file_name = '')
    {
        $this->name = $file_name;
        $this->folder = APP_ROOT . 'uploads' . DIRECTORY_SEPARATOR;
        $this->path = realpath(APP_ROOT . 'uploads' . DIRECTORY_SEPARATOR . $file_name);
        $this->extension = empty($file_name) ? 0 : pathinfo($file_name, PATHINFO_EXTENSION);
        $this->size = empty($file_name) ? 0 : filesize($this->path);
    } // end __construct()

    /**
     * Deletes file from system
     * @return bool
     */
    public function delete(): bool
    {
        if (is_file($this->path) && file_exists($this->path)) {
            return unlink($this->path);
        } else {
            if (!empty($this->name)) {
                throw new AprelendoException('There was an error deleting the associated file.');
            }
        }
        return false;
    } // end delete()

    /**
     * Uploads files
     * @param array $file_array Array containing file info
     * @return void
     */
    public function put(array $file_array): void
    {
        $this->name = basename($file_array['name']);
        $this->extension = pathinfo($this->name, PATHINFO_EXTENSION);
        $this->size = $file_array['size'];
        
        $temp_file_path = $file_array['tmp_name'];
        
        // Create unique filename for file
        do {
            $target_file_name = uniqid() . '.' . $this->extension; // create unique filename for file
            $this->path = $this->folder . $target_file_name;
        } while (file_exists($this->path));
        
        // Check file size
        if ($this->size > $this->max_size || $file_array['error'] == UPLOAD_ERR_INI_SIZE) {
            $errors[] = '<li>File size should be less than ' . number_format($this->max_size) .
                ' bytes. Your file has ' . number_format($this->size) . ' bytes.<br>';
        }
        
        // Check file extension
        $allowed_ext = false;
        for ($i=0; $i < sizeof($this->allowed_extensions); $i++) {
            if (strcasecmp($this->allowed_extensions[$i], $this->extension) == 0) {
                $allowed_ext = true;
            }
        }
        
        if (!$allowed_ext) {
            $errors[] = '<li>Only the following file types are supported: '
                . implode(', ', $this->allowed_extensions)
                . "</li>";
        }
        
        // upload file
        if (empty($errors) && $file_array['error'] == UPLOAD_ERR_OK) {
            try {
                $this->move($temp_file_path, $this->path);
            } catch (\Exception $th) {
                throw new AprelendoException('There was an unexpected error trying to move this file from the '
                . 'temporary folder');
            }

            $this->name = $target_file_name;
        } else {
            $error_str = '<ul>' . implode("<br>", $errors) . '</ul>'; // show upload errors
            throw new AprelendoException($error_str);
        }
    } // end put()
    
    /**
     * Moves file from temporary folder to uploads folder
     * @param string $source_path Source file path
     * @param string $destination_path Destination file path
     * @return void
     */
    private function move(string $source_path, string $destination_path): void
    {
        // if target dir does not exist, create it
        if (!is_dir($this->folder)) {
            mkdir($this->folder);
        }
        // try to move file to uploads folder. If this fails, show error message
        if (!move_uploaded_file($source_path, $destination_path)) {
            throw new AprelendoException("<li>Sorry, there was an error uploading your file.</li>");
        }
    } // end move()
    
    /**
     * Gets file
     * @return string
     */
    public function get(): string
    {
        // make sure it exists
        if (!$file = realpath($this->folder . $this->name)) {
            return $this->error(404);
        }
            
        if (!is_file($file)) {
            return $this->error(404);
        }

        // check for cheaters
        if (substr($file, 0, strlen($this->folder)) !== $this->folder) {
            return $this->error(401);
        }

        return readfile($file);
    } // end get()

    /**
     * Gets file name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Creates error header
     * @param int $code HTML error code
     * @param string $msg Error message
     * @return string HTML header
     */
    private function error(int $code = 401, string $msg = null): string
    {
        $msgs = array(
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
        );

        if (!$msg) {
            $msg = $msgs[$code];
        }

        header(sprintf('HTTP/1.0 %s %s', $code, $msg));
        return "<html><head><title>$code $msg</title></head><body><h1>$msg</h1></body></html>";
    } // end error()
}
