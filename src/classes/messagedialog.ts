import * as bootstrap from "bootstrap";
import MessageDialogInterface from "./messagedialog.interface";

export default class MessageDialog{
    private _bt_ok: Element; //'OK' button
    private _div_dialog: HTMLElement; //Dialog container
    private _instance: bootstrap.Modal; //Bootstrap dialog instance
    private _message: string; //Dialog message
    private _title: string; //Dialog title
    private _html: string; //HTML of bootstrap dialog

    constructor(mdi: MessageDialogInterface){
        this._title = mdi.title;
        this._message = mdi.message;
        this.htmlDialog();
        this.showDialog();
    }

    get bt_ok(){return this._bt_ok;}
    get div_dialog(){return this._div_dialog;}
    get instance(){return this._instance;}
    get message(){return this._message;}
    get title(){return this._title;}
    get html(){return this._html;}

    //Set html for the dialog
    private htmlDialog(): void{
        this._html = `
<div id="dialog" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">${this._title}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>${this._message}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary md_okbutton">OK</button>
            </div>
        </div>
    </div>
</div>
        `;
    }

    //Add dialog to DOM & show
    private showDialog(): void{
        this._div_dialog = document.createElement('div');
        this._div_dialog.setAttribute('id','md_div_dialog');
        this._div_dialog.innerHTML = this._html;
        document.body.appendChild(this._div_dialog);
        let modalEl = document.getElementById('dialog');
        this._instance = new bootstrap.Modal(modalEl as Element,{
            focus: true
        });
        this._instance.show();
        this._bt_ok = document.querySelector('.md_okbutton') as Element;
    }
}