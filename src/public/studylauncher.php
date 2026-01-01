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
require_once PUBLIC_PATH . 'head.php';
require_once PUBLIC_PATH . 'header.php';

$lingobot_configured = !empty($user->hf_token);

$ai_card_border_class = $lingobot_configured ? 'border-success' : 'border-secondary';
$ai_card_text_bg_class = $lingobot_configured ? 'text-bg-success' : 'text-bg-secondary';
$ai_button_class = $lingobot_configured ? 'btn-success' : 'btn-secondary';
?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/texts">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Study</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-sm-12">
            <main>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-2">Choose how you want to study today</h5>
                                <p class="mb-0 text-secondary">Pick a mode below to practice the words saved in
                                    your library.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    <div class="col">
                        <div class="card h-100 border-primary shadow-sm">
                            <div class="card-header bg-gradient text-bg-primary d-flex align-items-center">
                                <span class="bi bi-card-text me-2"></span>
                                Classic Flashcards
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Prioritize Active Recall</h5>
                                <p class="card-text">Begin by recalling the word's meaning before viewing the example
                                    sentences. After reviewing the context, checking definitions (if needed), rate your
                                    confidence in your recall.</p>
                                <ul class="small text-start ps-3">
                                    <li><b>Benefits</b>: Ensures honest recall by forcing retrieval before context is
                                        revealed.</li>
                                    <li><b>Drawbacks</b>: Minimal writing/production practice; high risk of "skimming"
                                        or rushing through review.</li>
                                </ul>
                                <div class="mt-auto d-grid">
                                    <a href="/study" class="btn btn-primary">
                                        Start Classic Flashcards
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-warning shadow-sm">
                            <div class="card-header bg-gradient text-bg-warning d-flex align-items-center">
                                <span class="bi bi-keyboard me-2"></span>
                                Fill in the Blanks (Cloze)
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Reconstruct the Expression</h5>
                                <p class="card-text">
                                    Unscramble the hint to complete the blank. Each word in the expression 
                                    is scrambled but <b>retains its original first letter and position</b> to guide you.
                                </p>
                                <ul class="small text-start ps-3">
                                    <li>
                                        <b>Benefits</b>: Reinforces spelling and active recall of the specific 
                                        expression without the complexity of full sentence building.
                                    </li>
                                    <li>
                                        <b>Drawbacks</b>: Highly focused on vocabulary; provides less practice 
                                        with broader grammar and sentence structure.
                                    </li>
                                </ul>
                                <div class="mt-auto d-grid">
                                    <a href="/studycloze" class="btn btn-warning text-dark">
                                        Start Cloze Flaschards
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 <?php echo $ai_card_border_class; ?> shadow-sm">
                            <div class="card-header bg-gradient <?php echo $ai_card_text_bg_class; ?> d-flex align-items-center">
                                <span class="bi bi-stars me-2"></span>
                                AI Feedback Writing (Lingobot)
                                <?php if (!$lingobot_configured): ?>
                                    <span class="bi bi-lock-fill ms-auto text-warning"
                                        title="Configure Lingobot in your profile to use this feature"></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">Produce Full Sentences</h5>
                                <p class="card-text">Practice your learned vocabulary with a complete answer challenge.
                                    Get instant feedback from Lingobot with suggestions for natural language
                                    improvement.</p>
                                <ul class="small text-start ps-3">
                                    <li><b>Benefits</b>: Converts passive vocabulary into active, usable knowledge
                                        with high-quality, immediate feedback.</li>
                                    <li><b>Drawbacks</b>: The most demanding mode; requires the highest effort and
                                        results in a slower, more deliberate pace. Also, AI can make mistakes,
                                        so use with care.</li>
                                </ul>
                                <div class="mt-auto d-grid">
                                    <?php if ($lingobot_configured): ?>
                                        <a href="/studyai" class="btn btn-success text-light">
                                            Start AI Writing Feedback
                                        </a>
                                    <?php else: ?>
                                        <a href="/userprofile"
                                            class="btn <?php echo $ai_button_class; ?> text-light position-relative"
                                            title="Configure Lingobot in your profile to use this feature">
                                            Configure Lingobot
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>