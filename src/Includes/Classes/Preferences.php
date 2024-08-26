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

class Preferences extends DBEntity
{
    public int    $user_id            = 0;
    public string $font_family        = 'Arial';
    public string $font_size          = '12pt';
    public string $line_height        = '1.5';
    public string $text_alignment     = 'left';
    public string $display_mode       = 'light';
    public bool $assisted_learning    = true;

    /**
     * Constructor
     *
     * @param \PDO $pdo
     * @param int $user_id
     */
    public function __construct(\PDO $pdo, int $user_id)
    {
        parent::__construct($pdo);
        $this->table = 'preferences';
        $this->user_id = $user_id;
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
    public function edit(
        string $font_family,
        string $font_size,
        string $line_height,
        string $text_alignment,
        string $display_mode,
        bool $assisted_learning
        ): void
        {
        
        $this->font_family = $font_family ?? $this->font_family;
        $this->font_size = $font_size ?? $this->font_size;
        $this->line_height = $line_height ?? $this->line_height;
        $this->text_alignment = $text_alignment ?? $this->text_alignment;
        $this->display_mode = $display_mode ?? $this->display_mode;
        $this->assisted_learning = (int)$assisted_learning ?? $this->assisted_learning;

        $sql = "REPLACE INTO `{$this->table}` (`user_id`, `font_family`,
                `font_size`, `line_height`, `text_alignment`, `display_mode`, `assisted_learning`)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->sqlExecute($sql, [
            $this->user_id, $this->font_family, $this->font_size, $this->line_height,
            $this->text_alignment, $this->display_mode, (int)$this->assisted_learning
        ]);
    } // end edit()

    /**
     * Loads user preferences data
     *
     * @param int $user_id
     * @return void
     */
    public function loadRecord(): void
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `user_id` = ?";
        $row = $this->sqlFetch($sql, [$this->user_id]);
        
        if ($row) {
            $this->font_family       = $row['font_family'];
            $this->font_size         = $row['font_size'];
            $this->line_height       = $row['line_height'];
            $this->text_alignment    = $row['text_alignment'];
            $this->display_mode      = $row['display_mode'];
            $this->assisted_learning = $row['assisted_learning'];
        }
    } // end loadRecord()
}
