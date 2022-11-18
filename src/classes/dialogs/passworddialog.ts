
import * as bootstrap from "bootstrap";
import PasswordDialogInterface from "src/interfaces/dialogs/passworddialog.interface";

export default class PasswordDialog{
    private _bt_ok: HTMLButtonElement; //'OK' button
    private _bt_canc: HTMLButtonElement; // 'canc' button
    private _cb_show_pass: HTMLInputElement; // 'show password checkbox'
    private _div_dialog: HTMLElement; //Dialog container
    private _i_pass: HTMLInputElement; //Password input field
    private _instance: bootstrap.Modal; //Bootstrap dialog instance
    private _title: string; //Dialog title
    private _html: string; //HTML of bootstrap dialog

    constructor(pdi: PasswordDialogInterface){
        this._title = pdi.title;
        this.htmlDialog();
        this.showDialog();
        this.showPassListener();
    }

    get bt_ok(){return this._bt_ok;}
    get bt_canc(){return this._bt_canc;}
    get div_dialog(){return this._div_dialog;}
    get i_pass(){return this._i_pass;}
    get instance(){return this._instance;}
    get title(){return this._title;}
    get html(){return this._html;}

    /**
     * Set the HTML for the dialog
     */
     private htmlDialog(): void{
        this._html = `
        <div id="pd_dialog" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header w-100 text-center">
                    <h3 class="modal-title">${this._title}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column mb-4">
                        <label for="pcd_password" class="form-label">Password</label>
                        <input type="password" id="pd_password" class="form-control">
                    </div>
                    <div>
                        <input type="checkbox" id="pd_showpass" class="form-check-input me-2">
                        <label for="pcd_showpass" class="form-check-label">Mostra password</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pd_okbutton">OK</button>
                    <button type="button" class="btn btn-secondary pd_cancbutton" data-bs-dismiss="modal">ANNULLA</button>
                </div>
            </div>
        </div>
</div>   
`;
    }

    /**
     * Add the dialog to DOM and show
     */
     private showDialog(): void{
        this._div_dialog = document.createElement('div');
        this._div_dialog.setAttribute('id','pd_div_dialog');
        this._div_dialog.innerHTML = this._html;
        document.body.appendChild(this._div_dialog);
        let modalEl = document.getElementById('pd_dialog');
        this._instance = new bootstrap.Modal(modalEl as Element,{
            focus: true
        });
        this._instance.show();
        this._bt_ok = document.querySelector('.pd_okbutton') as HTMLButtonElement;
        this._bt_canc = document.querySelector('.pd_cancbutton') as HTMLButtonElement;
        this._i_pass = document.getElementById('pd_password') as HTMLInputElement;
        this._cb_show_pass = document.getElementById('pd_showpass') as HTMLInputElement;
    }

    /**
     * Change password input fields type on checkbox change
     */
     private showPassListener(): void{
        this._cb_show_pass.addEventListener('change', (ev)=>{
            let this_cb: HTMLInputElement = ev.target as HTMLInputElement;
            if(this_cb.checked)
                this._i_pass.setAttribute('type','text');  
            else
                this._i_pass.setAttribute('type','password');   
        });
    }
}