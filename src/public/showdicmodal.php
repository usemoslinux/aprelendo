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

<div id="dic-modal" class="modal fade" data-keyboard="true" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mb-xs-3">
            <div class="modal-header p-2">
                <button id="btn-remove" type="button" data-bs-dismiss="modal" class="btn btn-lg btn-danger me-3"
                    data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                    data-bs-title="Delete">
                    <span class="bi bi-trash3-fill"></span>
                </button>
                <div id="btn-more-dics" class="btn-group" role="group"
                    aria-label="Additional dictionaries and translator">
                    <button id="btn-translate" type="button" class="btn btn-lg btn-primary px-3"
                        data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Open translator">
                        <span class="bi bi-translate"></span>
                    </button>
                    <button id="btn-img-dic" type="button" class="btn btn-primary px-3" data-bs-toggle="tooltip"
                        data-bs-custom-class="custom-tooltip" data-bs-placement="bottom"
                        data-bs-title="Open visual dictionary">
                        <span class="bi bi-card-image"></span>
                    </button>
                </div>
                <button id="btn-cancel" type="button" data-bs-dismiss="modal" class="btn btn-lg btn-link ms-auto">
                    Cancel
                </button>
                <button id="btn-add" type="button" class="btn btn-lg btn-primary" data-bs-dismiss="modal">
                    Add
                </button>
            </div>
            <span id="bdgfreqlvl" class="badge d-none"></span>
            <div class="modal-body" id="definitions">
                <div id="loading-spinner" class="lds-ellipsis m-auto">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <iframe id="dicFrame" title="User dictionary" style="width:100%;border:none;"></iframe>
            </div>
        </div>
    </div>
</div>
