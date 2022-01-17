
export function dialogMessage(jQuery: any, title: string,message: string): void{
    let dialogHtml = `
<div id="dialog" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">${title}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>${message}</p>
      </div>
      <div class="modal-footer">
        <button id="okBtn" type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
    `;
    let div = $('<div>');
    div.html(dialogHtml);
    jQuery('body').append(div);
    jQuery('#dialog').modal('show');
    jQuery('#okBtn').on('click',function(){
        jQuery('#dialog').modal('hide');
    });
    jQuery('#dialog').on('hidden.bs.modal',function(){
        jQuery('#dialog').modal('dispose');
    });
}