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

<!-- Report Text Modal -->
<div class="modal fade" id="report-text-modal" data-keyboard="true" tabindex="-1"
    aria-labelledby="report-text-modal-label" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="report-text-modal-label">Flag Content for Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="report-alert-box" class="d-none"></div>
                <p>Please select a reason for reporting this content:</p>

                <form id="report-text-modal-form" method="post">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report-reason" id="report-sexual-content"
                            value="sexualContent">
                        <label class="form-check-label" for="report-sexual-content">
                            Sexual or inappropriate content
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report-reason" id="report-abusive-content"
                            value="abusiveContent">
                        <label class="form-check-label" for="report-abusive-content">
                            Violent, abusive, or hateful content
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report-reason" id="report-spam"
                            value="spamContent">
                        <label class="form-check-label" for="report-spam">
                            Misleading, spam, or useless content
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report-reason" id="report-legal-issue"
                            value="legalIssue">
                        <label class="form-check-label" for="report-legal-issue">
                            Legal issue (e.g., copyright violation)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report-reason" id="report-language-issue"
                            value="languageIssue">
                        <label class="form-check-label" for="report-language-issue">
                            Content is in a different language
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                <button id="btn-report-text" type="button" class="btn btn-primary">
                    Report
                </button>
            </div>
        </div>
    </div>
</div>

<script defer src="/js/showreporttextmodal.min.js"></script>
