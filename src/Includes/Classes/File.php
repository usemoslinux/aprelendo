<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class File
{
    public $name               = '';
    public $folder             = '';
    public $path               = '';
    public $extension          = '';
    public $size               = 0;
    protected $max_size           = 0;
    protected $allowed_extensions = [];

    /**
     * Constructor
     * @param string $file_name
     */
    public function __construct(string $file_name = '')
    {
        $resolved_path = false;

        $this->name = $file_name;
        $this->folder = $this->getUploadsFolder();
        $resolved_path = $this->resolveExistingPath($file_name);
        $this->path = ($resolved_path !== false && $this->isInUploadsFolder($resolved_path))
            ? $resolved_path
            : '';
        $this->extension = empty($file_name) ? '' : pathinfo($file_name, PATHINFO_EXTENSION);
        $this->size = is_file($this->path) ? filesize($this->path) : 0;
    } 

    /**
     * Deletes file from system
     * @return bool
     */
    public function delete(): bool
    {
        $file = $this->resolveExistingPath($this->name);

        if ($file === false || !$this->isInUploadsFolder($file)) {
            if (!empty($this->name)) {
                throw new UserException('Error deleting the associated file.');
            }
            return false;
        }

        if (is_file($file) && file_exists($file)) {
            return unlink($file);
        } else {
            if (!empty($this->name)) {
                throw new UserException('Error deleting the associated file.');
            }
        }
        return false;
    } 

    /**
     * Uploads files
     * @param array $file_array Array containing file info
     * @param bool $is_temporary If true, file is temporary and will be deleted
     * @return void
     */
    public function put(array $file_array, bool $is_temporary): void
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
        if ((!$is_temporary && $this->size > $this->max_size) || $file_array['error'] == UPLOAD_ERR_INI_SIZE) {
            $error_message = '<li>File size should be less than ' . $this->formatBytes($this->max_size) . '.</li>';

            if ($this->size > 0) {
                $error_message = '<li>File size should be less than ' . $this->formatBytes($this->max_size)
                    . '. Your file has ' . $this->formatBytes($this->size) . '.</li>';
            }

            $errors[] = $error_message;
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
                throw new UserException('Error moving file from the temporary folder');
            }

            $this->name = $target_file_name;
        } else {
            $error_str = '<ul>' . implode("<br>", $errors) . '</ul>'; // show upload errors
            throw new UserException($error_str);
        }
    } 
    
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
            throw new UserException("<li>There was an error uploading your file.</li>");
        }
    } 
    
    /**
     * Gets file
     * @return string
     */
    public function get(): string
    {
        $file = $this->resolveExistingPath($this->name);

        if ($file && !$this->isInUploadsFolder($file)) {
            throw new UserException('Unauthorized access.', 401);
        }

        // make sure it exists
        if (!$file || !is_file($file)) {
            throw new UserException('File does not exist.', 404);
        }

        // check for cheaters
        if (substr($file, 0, strlen($this->folder)) !== $this->folder) {
            throw new UserException('Unauthorized access.', 401);
        }

        $file_contents = file_get_contents($file);

        if ($file_contents === false) {
            throw new UserException('Error reading file.', 500);
        }

        return $file_contents;
    } 

    /**
     * Gets uploads folder path with a trailing separator.
     *
     * @return string
     */
    private function getUploadsFolder(): string
    {
        $uploads_folder = realpath(UPLOADS_PATH);
        if ($uploads_folder === false) {
            $uploads_folder = UPLOADS_PATH;
        }

        return rtrim($uploads_folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Resolves an existing file path relative to the uploads folder.
     *
     * @param string $file_name
     * @return string|false
     */
    private function resolveExistingPath(string $file_name): string|false
    {
        if ($file_name === '') {
            return false;
        }

        return realpath($this->folder . $file_name);
    }

    /**
     * Checks whether a resolved path stays inside the uploads folder.
     *
     * @param string $file_path
     * @return bool
     */
    private function isInUploadsFolder(string $file_path): bool
    {
        return substr($file_path, 0, strlen($this->folder)) === $this->folder;
    }

    /**
     * Formats bytes as megabytes for user-facing upload errors.
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $size_mb = $bytes / (1024 * 1024);

        return rtrim(rtrim(number_format($size_mb, 1), '0'), '.') . ' MB';
    }
}
