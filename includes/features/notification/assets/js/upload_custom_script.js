jQuery(function($){

    // on upload button click
    $('body').on( 'click', '.misha-upl', function(e){

        e.preventDefault();

        var button = $(this);
        var custom_uploader = wp.media({
                title:button.data('ae-img-title'),
                library : {
                    type : 'image'
                },
                button: {
                    text: button.data('ae-btn-title') // button label text
                },
                multiple: false
            }).on('select', function() { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                if(["jpeg","png","bmp"].indexOf(attachment.subtype)<-1||attachment.filesizeInBytes>1000000){
                    $('#ae_img_error').removeClass('hidden');
                }
                button.html('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);
            }).open();

    });

    // on remove button click
    $('body').on('click', '.misha-rmv', function(e){

        e.preventDefault();

        var button = $(this);
        button.next().val(''); // emptying the hidden field
        button.hide().prev().html(button.data('ae-img-title'));
    });
    $('._ae_segments').select2({
        maximumSelectionSize: 3
    })

});