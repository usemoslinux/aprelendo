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

namespace Aprelendo;

use Aprelendo\UserException;

abstract class WordsUtilities
{
    /**
     * Exports words to a CSV file
     *
     * It exports either the whole set of words corresponding to a user & language combination,
     * or the specific subset that results from applying additional filters (e.g. $search_text).
     * Results are ordered using $order_by.
     *
     * @param SearchWordsParameters $search_params
     * @return void
     */
    public static function exportToCSV(array $words): void
    {
        $headers = ['Words', 'Status', 'Freq_Level'];

        $fp = fopen('php://output', 'w');

        if ($fp) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            fputcsv($fp, $headers);

            foreach ($words as $word) {
                fputcsv($fp, [$word['word'], $word['status'], $word['freq_level']]);
            }

            fclose($fp);
        }
    } // end exportToCSV()
}
