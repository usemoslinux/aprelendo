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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Aprelendo;

class EbookFile extends File
{
    /**
     * Constructor
     * @param string $file_name
     */
    public function __construct(string $file_name)
    {
        parent::__construct($file_name);
        $this->allowed_extensions = ['epub'];
        $this->max_size = 67108864; // 64 MB
    } // end __construct()

    /**
     * Strips images, scripts and styles from the uploaded epub file
     * @return void
     * @throws InternalException on failure
     */
    public function strip(): void
    {
        $uploads_folder = realpath(UPLOADS_PATH) . DIRECTORY_SEPARATOR;
        $file_name = $uploads_folder . $this->name;
        $stripped_file_name = $uploads_folder . 'stripped_' . $this->name;

        $escaped_file_name = escapeshellarg($file_name);
        $escaped_stripped_file_name = escapeshellarg($stripped_file_name);

        $command = PYTHON_VENV . "/bin/python " . APP_ROOT . "scripts/epub-strip.py "
            . "$escaped_file_name $escaped_stripped_file_name 2>&1";

        // One cleanup place: delete both files if anything goes wrong
        $cleanup = function () use ($file_name, $stripped_file_name): void {
            if (is_file($file_name)) {
                @unlink($file_name);
            }
            if (is_file($stripped_file_name)) {
                @unlink($stripped_file_name);
            }
        };

        try {
            $output = [];
            $return_var = 0;
            exec($command, $output, $return_var);

            if ($return_var !== 0) {
                throw new InternalException("Strip failed: " . implode("\n", $output));
            }

            // Sanity check: Python should have produced the stripped file
            if (!is_file($stripped_file_name) || filesize($stripped_file_name) === 0) {
                throw new InternalException("Strip failed: output file not created or empty.");
            }

            // Delete original, then rename stripped to original name
            if (!unlink($file_name)) {
                throw new InternalException("Could not delete original file");
            }

            if (!rename($stripped_file_name, $file_name)) {
                throw new InternalException("Could not rename stripped file to original file name");
            }

            return; // Success path ends here; nothing else to do.
        } catch (\Throwable $e) {
            // On any failure, ensure both files are removed
            $cleanup();
            throw $e;
        }
    }
}
