<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

class UserException extends InternalException {
    public function getJsonError(): string {
        return $this->encodeJsonError($this->getMessage());
    }
}
