<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

use Exception;

class InternalException extends Exception {
    public function getJsonError(): string
    {
        return $this->encodeJsonError('Oops! There was an unexpected error processing your request.');
    }

    protected function encodeJsonError(string $error_msg): string
    {
        $error = ['success' => false, 'error_msg' => $error_msg];
        return json_encode($error);
    }
}
