(function($){
    
    $( document ).ready( function() {
        
        var _setPreviewButton = function( iButtonID, oThis ) {
        
            if ( 'undefined' === typeof aal_button_preview_labels ) {
                return false;
            }
        
            oThis.closest( 'fieldset' )
                .find( '.amazon-auto-links-button' )
                .each( function(){
                    
                    $( this ).attr( 
                        'class', // the subject attribute name
                        'amazon-auto-links-button amazon-auto-links-button-' + iButtonID // value
                    );   
                    if( 'undefined' !== typeof aal_button_preview_labels[ iButtonID ] ) {
                        $( this ).text( aal_button_preview_labels[ iButtonID ] );
                    }
                    
                    // Make sure the button container is visible. By default it is hidden for widget forms.
                    $( this ).parent().show();
                    
                } );
            
        }
        
        // Initially set the preview.
        $( '.button-select-row' )
            .find( 'select' ) // the select tag
            .each( function() { // hook the change
                _setPreviewButton( 
                    $( this ).val(), // button id
                    $( this ) // caller element 
                );
            } );        
        
        // Hook the button select change.
        $( '.button-select-row' )
            .find( 'select' ) // the select tag
            .change( function() { // hook the change
                _setPreviewButton( 
                    $( this ).val(), // button id
                    $( this ) // caller element
                );
            } );
        
    } ); // document ready

}( jQuery ));