/**
 * @name Rating Prompt
 * @version 1.0.0
 */
(function($){

    $( document ).ready( function(){
        
        if ( 'undefined' === typeof aalRatingPrompt ) {
            return;
        }

        $( '#aal-rating-prompt-dismissal a' ).on( 'click', function(){

            $( this ).closest( '.aal-rating-prompt' ).hide();
            $( '.aal-footer-left-original' ).unwrap().fadeIn();

            $.ajax( {
                type: "post",
                dataType: 'json',
                url: aalRatingPrompt.ajaxURL,
                data: {
                    action: aalRatingPrompt.actionHookSuffix,   // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalRatingPrompt.nonce,   // the nonce value set in template.php
                },
                success: function ( response ) {},
                error: function( response ) {},
                complete: function() {}
            } ); // ajax            
            
        } );
    } );

}(jQuery));