<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

enum CreationMethod: int
{
    case human = 1;
    case machine = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::human => 'Human-made',
            self::machine => 'Machine/AI-made',
        };
    }
}
