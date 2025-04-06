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

class SM2 {
    private float $easiness;
    private int $repetitions;
    private int $interval;

    // Constructor with default initial values: EF starts at 2.5, repetition and interval at 0.
    public function __construct(float $easiness = 2.5, int $repetitions = 0, int $interval = 0) {
        $this->easiness = $easiness;
        $this->repetitions = $repetitions;
        $this->interval = $interval;
    }

    /**
     * Inverts the quality rating for internal processing.
     *
     * @param int $quality
     * @return int
     */
    private function invertQuality(int $quality): int {
        // Reverse the scale (0 becomes 3, 1 becomes 2, etc.).
        return 3 - $quality;
    }

    /**
     * Processes a review using Anki-like quality ratings.
     *
     * Qualities:
     *   0 = Again       (completely forgot)
     *   1 = Hard        (barely recalled)
     *   2 = Good        (recalled with some effort)
     *   3 = Easy        (perfect recall)
     *
     * @param int $quality
     * @throws \InternalException if quality is not between 0 and 3.
     */
    public function processReview(int $quality): void {
        if ($quality < 0 || $quality > 3) {
            throw new InternalException("Quality must be between 0 (Again) and 3 (Easy).");
        }

        $quality = $this->invertQuality($quality);

        // Update the easiness factor using Anki's formula.
        // For quality 3: easiness increases slightly; for quality 2: easiness remains nearly the same;
        // for quality 1: easiness decreases a bit; for quality 0: easiness decreases more.
        $this->easiness = $this->easiness + (0.1 - (3 - $quality) * (0.08 + (3 - $quality) * 0.02));
        // Ensure that easiness does not drop below 1.3.
        if ($this->easiness < 1.3) {
            $this->easiness = 1.3;
        }

        // If the answer is "Again", treat it as a failure.
        if ($quality === 0) {
            $this->repetitions = 0;
            // For a failed review, the card goes back to the beginning.
            $this->interval = 1;
        } else {
            // For successful responses ("Hard", "Good", or "Easy"):
            // Increase the repetition count.
            $this->repetitions++;

            if ($this->repetitions === 1) {
                // First successful review: set interval to 1 day.
                $this->interval = 1;
            } elseif ($this->repetitions === 2) {
                // Second successful review: set interval to 6 days.
                $this->interval = 6;
            } else {
                // For subsequent reviews, adjust the interval based on quality.
                if ($quality === 1) { // Hard
                    // Apply a penalty multiplier for "Hard" responses.
                    $hard_multiplier = 1.2;
                    $this->interval = round($this->interval * $this->easiness * $hard_multiplier);
                } elseif ($quality === 2) { // Good
                    $this->interval = round($this->interval * $this->easiness);
                } elseif ($quality === 3) { // Easy
                    // Apply a bonus multiplier for "Easy" responses.
                    $easy_bonus = 1.3;
                    $this->interval = round($this->interval * $this->easiness * $easy_bonus);
                }
            }
        }
    }

    // Getter for the current easiness factor.
    public function getEasiness(): float {
        return $this->easiness;
    }

    // Getter for the current repetition count.
    public function getRepetitions(): int {
        return $this->repetitions;
    }

    // Getter for the current interval (in days).
    public function getInterval(): int {
        return $this->interval;
    }
}
