import MessageDialog from "src/classes/dialogs/messagedialog";
import MessageDialogInterface from "src/interfaces/dialogs/messagedialog.interface";

export function messageDialog(mdi: MessageDialogInterface): void{
  let md: MessageDialog = new MessageDialog(mdi);
  md.bt_ok.addEventListener('click', ()=>{
    md.instance.dispose();
    md.div_dialog.remove();
    document.body.style.overflow = 'auto';
  });
}