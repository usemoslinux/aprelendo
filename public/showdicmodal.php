<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
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

<div id="myModal" class="modal fade" data-keyboard="true" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content mb-xs-3">
            <div class="modal-header">
                <button id="btnremove" type="button" data-dismiss="modal" class="btn btn-danger mr-3">Delete</button>
                <select class="modal-selPhrase" name="selPhrase" id="selPhrase">
                    <option value="translate_sentence">Translate sentence</option>
                </select>
                <button id="btncancel" type="button" data-dismiss="modal" class="btn btn-link float-right cancel-btn">Cancel</button>
                <button id="btnadd" type="button" class="btn btn-primary btn-success float-right add-btn" data-dismiss="modal">Add</button>
            </div>
            <div class="modal-body" id="definitions">
                <iframe id="dicFrame" style="width:100%;border:none;"></iframe>
            </div>
        </div>
    </div>
</div>