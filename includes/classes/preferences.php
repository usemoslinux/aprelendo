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

namespace Aprelendo\Includes\Classes;

use Aprelendo\Includes\Classes\DBEntity;

class Preferences extends DBEntity {
    private $font_family        = 'Helvetica';
    private $font_size          = '12pt';
    private $line_height        = '1.5';
    private $alignment          = 'left';
    private $mode               = 'light';
    private $assisted_learning  = true;

    /**
     * Constructor
     *
     * @param PDO $pdo
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $user_id) {
        parent::__construct($pdo, $user_id);
        $this->table = 'preferences';
    } // end __construct()

    /**
     * Saves user preferences
     *
     * @param string $font_family
     * @param string $font_size
     * @param string $line_height
     * @param string $alignment
     * @param string $mode
     * @param bool $assisted_learning
     * @return void
     */
    public function edit(string $font_family, string $font_size, string $line_height, string $alignment, 
                         string $mode, bool $assisted_learning): void {
        $this->font_family       = isset($font_family)       && !empty($font_family)       ? $font_family       : $this->font_family;
        $this->font_size         = isset($font_size)         && !empty($font_size)         ? $font_size         : $this->font_size;
        $this->line_height       = isset($line_height)       && !empty($line_height)       ? $line_height       : $this->line_height;
        $this->alignment         = isset($alignment)         && !empty($alignment)         ? $alignment         : $this->alignment;
        $this->mode              = isset($mode)              && !empty($mode)              ? $mode              : $this->mode;
        $this->assisted_learning = isset($assisted_learning) && !empty($assisted_learning) ? $assisted_learning : $this->assisted_learning;

        try {
            $sql = "REPLACE INTO `{$this->table}` (`user_id`, `font_family`,
                    `font_size`, `line_height`, `text_alignment`, `learning_mode`, `assisted_learning`)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->font_family, $this->font_size, $this->line_height, $this->alignment, 
                            $this->mode, $this->assisted_learning]);
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to save your preferences. Please, try again later.');
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to save your preferences. Please, try again later.');
        } finally {
            $stmt = null;
        }
    } // end edit()
}

?>