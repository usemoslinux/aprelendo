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

use Aprelendo\Includes\Classes\DBEntity;

class Preferences extends DBEntity {
    private $font_family        = 'Helvetica';
    private $font_size          = '12pt';
    private $line_height        = '1.5';
    private $text_alignment     = 'left';
    private $display_mode       = 'light';
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
     * @param string $text_alignment
     * @param string $display_mode
     * @param bool $assisted_learning
     * @return void
     */
    public function edit(string $font_family, string $font_size, string $line_height, string $text_alignment, 
                         string $display_mode, bool $assisted_learning): void {
        $this->font_family       = isset($font_family)       && !empty($font_family)       ? $font_family            : $this->font_family;
        $this->font_size         = isset($font_size)         && !empty($font_size)         ? $font_size              : $this->font_size;
        $this->line_height       = isset($line_height)       && !empty($line_height)       ? $line_height            : $this->line_height;
        $this->text_alignment    = isset($text_alignment)    && !empty($text_alignment)    ? $text_alignment         : $this->text_alignment;
        $this->display_mode      = isset($display_mode)      && !empty($display_mode)      ? $display_mode           : $this->display_mode;
        $this->assisted_learning = isset($assisted_learning)                               ? (int)$assisted_learning : $this->assisted_learning;

        try {
            $sql = "REPLACE INTO `{$this->table}` (`user_id`, `font_family`,
                    `font_size`, `line_height`, `text_alignment`, `display_mode`, `assisted_learning`)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id, $this->font_family, $this->font_size, $this->line_height, $this->text_alignment, 
                            $this->display_mode, $this->assisted_learning]);
            if ($stmt->rowCount() == 0) {
                throw new \Exception('There was an unexpected error trying to save your preferences. Please, try again later.');
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to save your preferences. Please, try again later.');
        } finally {
            $stmt = null;
        }
    } // end edit()

    /**
     * Loads user preferences data
     *
     * @param int $user_id
     * @return void
     */
    public function loadRecord(): void {
        try {
            $sql = "SELECT * FROM `{$this->table}` WHERE `user_id` = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$this->user_id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
           
            if ($row) {
                $this->font_family       = $row['font_family']; 
                $this->font_size         = $row['font_size']; 
                $this->line_height       = $row['line_height']; 
                $this->text_alignment    = $row['text_alignment'];
                $this->display_mode      = $row['display_mode']; 
                $this->assisted_learning = $row['assisted_learning']; 
            }
        } catch (\PDOException $e) {
            throw new \Exception('There was an unexpected error trying to load user preferences record.');
        } finally {
            $stmt = null;
        }
    } // end loadRecord()

    /**
     * Get the value of font_family
     * @return string
     */ 
    public function getFontFamily(): string
    {
        return $this->font_family;
    }

    /**
     * Get the value of font_size
     * @return string
     */ 
    public function getFontSize(): string
    {
        return $this->font_size;
    }

    /**
     * Get the value of line_height
     * @return string
     */ 
    public function getLineHeight(): string
    {
        return $this->line_height;
    }

    /**
     * Get the value of alignment
     * @return string
     */ 
    public function getTextAlignment(): string
    {
        return $this->text_alignment;
    }

    /**
     * Get the value of mode
     * @return string
     */ 
    public function getDisplayMode(): string
    {
        return $this->display_mode;
    }

    /**
     * Get the value of assisted_learning
     * @return bool
     */ 
    public function getAssistedLearning(): bool
    {
        return $this->assisted_learning;
    }
}

?>