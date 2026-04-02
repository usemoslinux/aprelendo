<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class Preferences extends DBEntity
{
    public int    $user_id            = 0;
    public string $font_family        = 'var(--bs-body-font-family)';
    public string $font_size          = '1';
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
    } 

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
    } 

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
    } 

    /**
     * Returns the CSS font size value for reader views.
     *
     * @return string
     */
    public function getFontSizeCssValue(): string
    {
        return $this->font_size . 'rem';
    } 

}
