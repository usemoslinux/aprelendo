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
?>

<!-- Action buttons -->

<div id="action-buttons" style="display: none;">
    <div class="btn-group" id="word-actions-g1">
        <button type="button" class="btn btn-primary" id="btn-add" data-bs-toggle="tooltip"
        data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="New">
            <span class="bi bi-bookmark-plus"></span>
        </button>
    </div>
    <div class="btn-group" id="word-actions-g2">
        <button type="button" class="btn btn-danger" id="btn-forgot" data-bs-toggle="tooltip"
        data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Forgot">
            <span class="bi bi-bookmark-dash"></span>
        </button>
        <button type="button" class="btn btn-danger" id="btn-remove" data-bs-toggle="tooltip"
        data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Remove">
            <span class="bi bi-trash"></span>
        </button>
    </div>
    <div class="btn-group" id="dict-actions">
        <button type="button" class="btn btn-primary" id="btn-open-dict" data-bs-toggle="tooltip"
        data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Dictionary">
            <span class="bi bi-book"></span>
        </button>
        <button type="button" class="btn btn-warning" id="btn-open-img-dict" data-bs-toggle="tooltip"
        data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Visual Dictionary">
            <span class="bi bi-card-image"></span>
        </button>
        <button type="button" class="btn btn-info" id="btn-open-translator" data-bs-toggle="tooltip"
            data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Translate">
            <span class="bi bi-translate"></span>
        </button>
    </div>
</div>

