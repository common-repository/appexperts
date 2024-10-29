let _aes_$=jQuery;

    function SetClipboard( data, $el ) {
        if ( 'undefined' === typeof $el ) {
            $el = jQuery( document );
        }
        var $temp_input = jQuery( '<textarea style="opacity:0">' );
        jQuery( 'body' ).append( $temp_input );
        $temp_input.val( data ).trigger( 'select' );

        $el.trigger( 'beforecopy' );
        try {
            document.execCommand( 'copy' );
            _aes_$(".alert").fadeIn("slow",function(){
            setTimeout(function(){
                _aes_$(".alert").fadeOut("slow");
            },2000);
    });
        } catch ( err ) {
            $el.trigger( 'aftercopyfailure' );
        }

        $temp_input.remove();
    }

    function ClearClipboard() {
        SetClipboard( '' );
    }

    _aes_$( document.body )
    .on( 'click', '.copy-key', function( evt ) {
        evt.preventDefault();
        if ( ! document.queryCommandSupported( 'copy' ) ) {
            _aes_$( '.copy-key' ).parent().find( 'input' ).trigger( 'focus' ).trigger( 'select' );
        } else {
            ClearClipboard();
            SetClipboard( _aes_$( this ).prev( 'input' ).val().trim(), _aes_$( '.copy-key' ) );
        }
    } );