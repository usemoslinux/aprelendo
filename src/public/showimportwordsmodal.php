<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once APP_ROOT . 'Includes/checklogin.php'; // check if logged in and set $user


?>

<!-- Import words Modal -->
<div class="modal fade" id="import-words-modal" data-keyboard="true"
    aria-labelledby="import-words-modal-label" aria-modal="true" role="dialog" tabindex="-1">
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
                <button id="btn-cancel-import" type="button" class="btn btn-link">Cancel</button>
                <button id="btn-import-words" type="button" class="btn btn-primary"
                    disabled>Import</button>
            </div>
        </div>
    </div>
</div>

<script defer src="/js/showimportwordsmodal.js"></script>
