<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

/** Validates versioned ebook positions while accepting legacy section references. */
final class EbookPosition
{
    public static function normalize(mixed $position): ?string
    {
        if ($position === null || $position === '') {
            return null;
        }
        if (!is_string($position) || strlen($position) > 8192) {
            throw new UserException('Invalid ebook reading position.');
        }
        if (!str_starts_with(ltrim($position), '{')) {
            return $position;
        }
        $location = json_decode($position, true);
        if (!is_array($location) || ($location['version'] ?? null) !== 1
            || !is_string($location['href'] ?? null) || $location['href'] === ''
            || strlen($location['href']) > 4096
            || !is_int($location['offset'] ?? null) || $location['offset'] < 0
            || $location['offset'] > 2147483647) {
            throw new UserException('Invalid ebook reading position.');
        }
        return json_encode([
            'version' => 1,
            'href' => $location['href'],
            'offset' => $location['offset']
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
