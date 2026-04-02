<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

/**
 * Backed enum for the `words.status` field.
 */
enum WordStatus: int
{
    case learned = 0;
    case learning = 1;
    case new_word = 2;
    case forgotten = 3;

    /**
     * Converts a numeric value into a valid word status.
     *
     * @param int $status_value
     * @return self
     */
    public static function fromInt(int $status_value): self
    {
        $status = self::tryFrom($status_value);
        if ($status === null) {
            throw new \InvalidArgumentException("Invalid word status value: {$status_value}");
        }

        return $status;
    } 

    /**
     * Returns all possible word statuses in logical progression order.
     *
     * @return array<WordStatus>
     */
    public static function getAll(): array
    {
        return [
            self::learned,
            self::learning,
            self::new_word,
            self::forgotten,
        ];
    } 

    /**
     * Returns the human-readable label for the status.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::learned => 'Learned',
            self::learning => 'Learning',
            self::new_word => 'New',
            self::forgotten => 'Forgotten',
        };
    } 

    /**
     * Returns the icon class used to render the status in tables.
     *
     * @return string
     */
    public function getIconClass(): string
    {
        return match ($this) {
            self::learned => 'bi-hourglass-top status-learned',
            self::learning => 'bi-hourglass-split status-learning',
            self::new_word => 'bi-hourglass-bottom status-new',
            self::forgotten => 'bi-hourglass-bottom status-forgotten',
        };
    } 
}
