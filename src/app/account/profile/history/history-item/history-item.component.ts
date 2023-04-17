import { Component, Input, OnInit, OnChanges, SimpleChanges} from '@angular/core';
import { FormControl} from '@angular/forms';
import ConfirmDialog from 'src/classes/dialogs/confirmdialog';
import ConfirmDialogInterface from 'src/interfaces/dialogs/confirmdialog.interface';

@Component({
  selector: 'app-history-item',
  templateUrl: './history-item.component.html',
  styleUrls: ['./history-item.component.scss']
})
export class HistoryItemComponent implements OnInit, OnChanges{

  @Input() id: string;
  @Input() date: Date;
  @Input() description: string;
  @Input() title: string;
  dateString: string;
  actionId: FormControl = new FormControl();

  constructor() { }

  
  ngOnChanges(changes: SimpleChanges): void{
    console.log(changes)
    this.actionId.setValue(changes['id'].currentValue)
  }

  ngOnInit(): void {
    this.dateString = this.date.toLocaleString('it-It',{timeZone: 'UTC'});
  }

  removeButtonClick(): void{
    const action_id_val: string = this.actionId.value;
    //console.log(action_id_val)
    const cdData: ConfirmDialogInterface = {
      title: 'Rimuovi azione',
      message: 'Sei sicuro di voler rimuovere questa azione?'
    }
    const cd: ConfirmDialog = new ConfirmDialog(cdData)
    cd.bt_yes.addEventListener('click',()=>{
      cd.instance.dispose();
      document.body.removeChild(cd.div_dialog);
    })
    cd.bt_no.addEventListener('click',()=>{
      cd.instance.dispose();
      document.body.removeChild(cd.div_dialog);
    })
  }

}
