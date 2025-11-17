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

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user


?>

<!-- Import words Modal -->
<div class="modal fade" id="import-words-modal" data-keyboard="true"
    aria-labelledby="import-words-modal-label" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="import-words-modal-label">Import words</h5>
            </div>

            <div class="modal-body">
                <input id="words-upload-input" class="words-upload-input d-none" type='file' accept=".txt">
                <div id="words-upload-wrap" class="words-upload-wrap user-select-none">
                    <div class="csvfile-drag-text">
                        <div class="fw-bold">Drag and drop a TXT file or click here</div>

                        <div>Each word must occupy a line</div>
                    </div>
                </div>
                <div id="words-table-wrap" class="d-none">
                    <table class="table table-bordered user-select-none" id="words-table" aria-label="Words table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Word</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table rows will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                <button id="btn-import-words" type="button" class="btn btn-primary"
                    data-bs-dismiss="modal" disabled>Import</button>
            </div>
        </div>
    </div>
</div>

<script defer src="/js/showimportwordsmodal.min.js"></script>
