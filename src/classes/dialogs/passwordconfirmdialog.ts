
import * as bootstrap from "bootstrap";
import PasswordConfirmDialogInterface from "src/interfaces/dialogs/passwordconfirmdialog.interface";

export default class PasswordConfirmDialog{
    private _bt_ok: Element; //'OK' button
    private _bt_canc: Element; // 'canc' button
    private _div_dialog: HTMLElement; //Dialog container
    private _instance: bootstrap.Modal; //Bootstrap dialog instance
    private _title: string; //Dialog title
    private _html: string; //HTML of bootstrap dialog

    constructor(pcdi: PasswordConfirmDialogInterface){
        this._title = pcdi.title;
        this.htmlDialog();
        this.showDialog();
    }

    get bt_ok(){return this._bt_ok;}
    get bt_canc(){return this._bt_canc;}
    get div_dialog(){return this._div_dialog;}
    get instance(){return this._instance;}
    get title(){return this._title;}
    get html(){return this._html;}

    //Set html for the dialog
    private htmlDialog(): void{
        this._html = `
        <div id="pc_dialog" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${this._title}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column">
                        <label for="pcd_password" class="form-label">Password</label>
                        <input type="password" id="pcd_password" class="form-control">
                    </div>
                    <div class="d-flex flex-column">
                        <label for="pcd_confpass" class="form-label">Conferma password</label>
                        <input type="password" id="pcd_confpass" class="form-control">
                    </div>
                    <div>
                        <input type="checkbox" id="pci_showPass" class="form-check-input me-2">
                        <label for="pci_showPass" class="form-check-label">Mostra password</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pcd_okbutton">OK</button>
                    <button type="button" class="btn btn-secondary pcd_cancbutton" data-bs-dismiss="modal">ANNULLA</button>
                </div>
            </div>
        </div>
</div>   
`;
    }

    //Add dialog to DOM & show
    private showDialog(): void{
        this._div_dialog = document.createElement('div');
        this._div_dialog.setAttribute('id','pcd_div_dialog');
        this._div_dialog.innerHTML = this._html;
        document.body.appendChild(this._div_dialog);
        let modalEl = document.getElementById('pc_dialog');
        this._instance = new bootstrap.Modal(modalEl as Element,{
            focus: true
        });
        this._instance.show();
        this._bt_ok = document.querySelector('.pcd_okbutton') as Element;
        this._bt_canc = document.querySelector('.pcd_cancbutton') as Element;
    }


}