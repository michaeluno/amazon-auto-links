(function($){
    $( document ).ready( function() {

        // The input that switches certain inputs        
        $( 'input[data-switch][type="radio"]' )
            .change( function() {
                
                var _sSubjectSelector = this.value;
                var _sSwitchSelector  = $( this ).attr( 'data-switch' );
                
                if ( $( this ).is( ':checked' ) ) {
                    // Disable unchecked items
                    $( _sSwitchSelector )
                        .find( 'input[data-property], select[data-property]' )
                        .each( function(){
                            $( this ).attr( 'disabled', 'disabled' );
                            // console.log( 'disabled: ' + $( this ).attr( 'name' ) );
                        } ); 
                        
                    // Enable checked items
                    $( _sSubjectSelector )
                        .find( 'input[data-property], select[data-property]' )
                        .each( function(){
                            $( this ).removeAttr( 'disabled' );                   
                            // console.log( 'enabled: ' + $( this ).attr( 'name' ) );
                        } );
                        
                } 
                
            } );    
    
        // Monitor changes on input fields
        setInterval( function(){
                        
            // The inputs that change the preview button style
            $( '#post' )
                .find( 'input[data-property][type="text"], input[data-property][type="number"], select[data-property]' )
                .each( function(){ 

                    // If the data-new attribute exists,
                    if ( ! $( this ).is( "[data-new]" ) ) {
                        $( this ).attr( "data-old", "" );
                        $( this ).attr( "data-new", $( this ).val() );
                        return true;
                    } else {
                        $( this ).attr( "data-old", $( this ).attr( "data-new" ) );
                        $( this ).attr( "data-new", $( this ).val() );
                    }
                    if ( $( this ).attr( "data-old" ) === $( this ).attr( "data-new" ) ) {
                        return true; // continue;
                    }

                    // At this point, the value has changed.
                    oPreviewButton.updateStyles();
                    
                });
            
            // For radio buttons - unlike other input types, the value of a radio button does not change even when the user check a different item.
            $( '#post' )
                .find( 'input[data-property][type="radio"], input[data-switch][type="radio"]' )
                .each( function(){ 
                                        
                    // If the data-new attribute exists,
                    if ( ! $( this ).is( "[data-new]" ) ) {
                        $( this ).attr( "data-new", $( this ).is( ':checked' ) );
                        $( this ).attr( "data-old", $( this ).is( ':checked' ) );
                        return true; // continue;
                    } 
                    // otherwise,
                    else {
                        $( this ).attr( "data-old", $( this ).attr( "data-new" ) );
                        $( this ).attr( "data-new", $( this ).is( ':checked' ) );
                    }
                                        
                    // If the value is the same, skip.
                    if ( $( this ).attr( "data-old" ) === $( this ).attr( "data-new" ) ) {
                        return true; // continue;
                    }
                    
                    oPreviewButton.updateStyles();

                }); 
            
        }, 500 );   

        // Need an initial set-up for the switch radio buttons.
        $( 'input[data-switch][type="radio"]:checked' )
            .trigger( "change" );
        
    });
    
}(jQuery));