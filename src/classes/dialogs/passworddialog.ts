
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
    }

    get bt_ok(){return this._bt_ok;}
    get bt_canc(){return this._bt_canc;}
    get div_dialog(){return this._div_dialog;}
    get i_pass(){return this._i_pass;}
    get instance(){return this._instance;}
    get title(){return this._title;}
    get html(){return this._html;}
}