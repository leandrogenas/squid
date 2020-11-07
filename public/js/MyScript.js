function abrir_loading(msg) {
    Swal.fire({
        title: msg, onBeforeOpen: function () {
            Swal.showLoading();
        }
    });
}

function marcar_checkbox(checkbox) {
    // let  checkado = $(checkbox).prop("checked");
    // console.log(checkado);
    // if(checkado){
    //     $(checkbox).removeAttr("checked");
    // }else{
    //     $(checkbox).attr("checked",true);
    // }
}
