
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
    }

    get bt_ok(){return this._bt_ok;}
    get bt_canc(){return this._bt_canc;}
    get div_dialog(){return this._div_dialog;}
    get instance(){return this._instance;}
    get title(){return this._title;}
    get html(){return this._html;}
}