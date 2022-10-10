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
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */
?>

<div id="web-speech-modal" class="modal fade" data-keyboard="true" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content mb-xs-3">
      <div class="modal-header pb-2">
        <h5 class="modal-title">Text to Speech</h1>
      </div>
      <div class="modal-body">
        <label for="ws-voices" class="form-label">Voices</label>
        <select id="ws-voices" class="form-select"></select>
        <div class="d-flex flex-column">
          <div class="mt-3">
            <label for="ws-volume" class="form-label">Volume</label>
            <div class="d-flex">
                <input type="range" min="0" max="1" value="1" step="0.1" id="ws-volume" class="form-range flex-grow-1" />
                
                <span id="ws-volume-label" class="ms-2">1</span>
            </div>
          </div>
          <div class="mt-3">
            <label for="ws-rate" class="form-label">Rate</label>
            <div class="d-flex">
                <input type="range" min="0.1" max="10" value="1" id="ws-rate" step="0.1" class="form-range flex-grow-1"/>
                <span id="ws-rate-label" class="ms-2">1</span>
            </div>
          </div>
          <div class="mt-3">
            <label for="ws-pitch" class="form-label">Pitch</label>
            <div class="d-flex">
                <input type="range" min="0" max="2" value="1" step="0.1" id="ws-pitch" class="form-range flex-grow-1"/>
                <span id="ws-pitch-label" class="ms-2">1</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer mb-xs-3">
        <button id="ws-cancel" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
        <button id="ws-start" class="btn btn-success me-1"><i class="fa-solid fa-play"></i></button>
        <button id="ws-pause" class="btn btn-warning me-1"><i class="fa-solid fa-pause"></i></button>
        <!-- <button id="ws-resume" class="btn btn-info me-1">Resume</button> -->
      </div>
      
    </div>
  </div>
</div>

<script defer src="js/showwebspeechmodal.js"></script>