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

<div id="myModal" class="modal fade" data-keyboard="true" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mb-xs-3">
            <div class="modal-header p-2">
                <button id="btnremove" type="button" data-bs-dismiss="modal" class="btn btn-danger me-3" title="Delete"><i class="fas fa-trash-alt"></i></button>
                <button id="btn-translate" type="button" class="btn btn-link p-0 me-auto" title="Open translator"><i id="gt-icon" class="fas fa-language"></i></button>
                <button id="btncancel" type="button" data-bs-dismiss="modal" class="btn btn-link">Cancel</button>
                <button id="btnadd" type="button" class="btn btn-primary" data-bs-dismiss="modal">Add</button>
            </div>
            <span id="bdgfreqlvl" class="badge d-none"></span>
            <div class="modal-body" id="definitions">
                <div id="iframe-loader" class="lds-ellipsis m-auto">
                    <div></div><div></div><div></div><div></div>
                </div>
                <iframe id="dicFrame" style="width:100%;border:none;"></iframe>
            </div>
        </div>
    </div>
</div>