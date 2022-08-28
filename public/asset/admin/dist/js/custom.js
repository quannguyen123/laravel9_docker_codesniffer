$(document).on('click','.modal-delete',function(){
    let id = $(this).attr('data-id');
    let href = $(this).attr('data-href');
    let method = $(this).attr('data-method');
    let title = $(this).attr('data-title');
    let description = $(this).attr('data-description');
    let btnNo = $(this).attr('data-btnNo');
    let btnYes = $(this).attr('data-btnYes');

    $('.modal-title').html(title);
    $('.modal-description').html(description);
    $('.modal-action-no').html(btnNo);
    $('.modal-action-yes').html(btnYes);

    $('.modal-action-yes').attr('data-id', id);
    $('.modal-action-yes').attr('data-href', href);
    $('.modal-action-yes').attr('data-method', method);
});


$(".modal-action-yes").on("click", function(){
    let id = $(this).attr('data-id');
    let href = $(this).attr('data-href');
    let method = $(this).attr('data-method');

    $.ajax({
        headers : {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: method,
        url: href,
        dataType:"json",
        success: function(response){
            $('.item-' + response.id).remove();
        }
    });

    $("#modalConfirm").modal('hide');
});
