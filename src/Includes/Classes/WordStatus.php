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
    } // end fromInt()

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
    } // end getAll()

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
    } // end getLabel()

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
    } // end getIconClass()
}
