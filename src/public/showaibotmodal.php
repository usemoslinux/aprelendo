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

<!-- Lingobot Text Modal -->
<div class="modal fade" id="ask-ai-bot-modal" data-keyboard="true" tabindex="-1" 
     aria-labelledby="ask-ai-bot-modal-label" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ask-ai-bot-modal-label">Ask Lingobot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <div id="lingobot-alert-box" class="d-none"></div>
                <!-- Prompt Form -->
                <div id="prompt-form">
                    <form>
                        <!-- Category Selection -->
                        <div class="mb-3">
                            <label for="prompt-category" class="form-label">Select a Category</label>
                            <select class="form-select" id="prompt-category">
                                <option value="">Choose a category...</option>
                                <option value="synonyms">Synonyms, Antonyms & Nuances</option>
                                <option value="formality">Formal & Informal</option>
                                <option value="context">Context & Regional Usage</option>
                                <option value="practical">Collocations & Idioms</option>
                                <option value="pop">Popular Culture</option>
                                <option value="personalized">Personalized Learning</option>
                            </select>
                        </div>

                        <!-- Prompt Selection -->
                        <div class="mb-3">
                            <label for="prompt-select" class="form-label">Select Prompt</label>
                            <select class="form-select" id="prompt-select" disabled>
                                <option value="">First select a category...</option>
                            </select>
                        </div>

                        <!-- Custom Prompt Textarea -->
                        <div class="mb-3">
                            <label for="custom-prompt" class="form-label">Your Prompt</label>
                            <textarea class="form-control" id="custom-prompt" rows="6"></textarea>
                        </div>
                    </form>
                </div>

                <!-- AI Answer Section -->
                <div id="ai-answer" class="d-none">
                    <!-- AI's response will be displayed here -->
                    <div id="text-ai-answer" class="form-control overflow-auto" style="height: calc(1.2em * 15);"></div>
                    <small>Lingobot can make mistakes. Use its answers as a reference only.</small>
                </div>
            </div>
            <div class="modal-footer">
                <div id="normal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                    <button id="btn-ask-ai-bot" type="button" class="btn btn-primary">
                        Ask AI
                    </button>
                </div>
                <div id="back-footer" class="d-none">
                    <button id="back-to-form" class="btn btn-primary">Back</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Showdown is a Markdown to HTML converter -->
<script src="https://cdn.jsdelivr.net/npm/showdown/dist/showdown.min.js"></script>
<script defer src="/js/showaibotmodal.min.js"></script>
<script defer src="/js/aibot.min.js"></script>