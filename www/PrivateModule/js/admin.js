$(document).ready(function(){

    function createConfirm(options)
    {
        var html = $('<div id="confirm" class="modal fade">' +
            '<div class="modal-dialog" role="document">' +
                '<div class="modal-content">' +
                    '<div class="modal-body">' +
                        options.title +
                    '</div>' +
                    '<div class="modal-footer">' +
                        '<a href="'+options.okHref+'" class="btn btn-primary">'+options.okText+'</a>' +
                        '<a href="#close" data-dismiss="modal" class="btn">'+options.cancelText+'</a>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>');

        $('body').append(html);

        $('#confirm').modal({ backdrop: 'static', keyboard: false });
    }

    $('[data-toggle="confirm"]').on('click', function(e){

        //e.stopPropagation();
        e.preventDefault();

        createConfirm({
            title: $(this).data('confirm-title') ? $(this).data('confirm-title') : 'Opravdu?',
            okText: $(this).data('confirm-ok') ? $(this).data('confirm-ok') : 'ANO',
            okHref: $(this).attr('href'),
            cancelText: $(this).data('confirm-cancel') ? $(this).data('confirm-cancel') : 'NE'
        });
    });
});