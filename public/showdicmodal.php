<div id="myModal" class="modal fade" data-keyboard="true" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
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