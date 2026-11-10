<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Aprelendo\EbookPosition;
use Aprelendo\UserException;

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

expect(EbookPosition::normalize(null) === null, 'Null position');
expect(EbookPosition::normalize('0') === '0', 'Legacy first spine index');
expect(EbookPosition::normalize('chapter.xhtml#part') === 'chapter.xhtml#part', 'Legacy fragment');
$valid = '{"version":1,"href":"Text/chapter.xhtml","offset":12345}';
expect(EbookPosition::normalize($valid) === $valid, 'Structured position round trip');
foreach (['{', '{"version":2,"href":"a","offset":1}', '{"version":1,"href":"a","offset":-1}',
    '{"version":1,"href":"a","offset":"3"}', '{"version":1,"href":"","offset":0}',
    str_repeat('a', 8193), ['not a string']] as $invalid) {
    try {
        EbookPosition::normalize($invalid);
        throw new RuntimeException('Invalid position was accepted');
    } catch (UserException $expected) {
        // Expected validation rejection.
    }
}
echo "Ebook position validation passed.\n";
