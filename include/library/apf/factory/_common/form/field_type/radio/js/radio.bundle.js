/*! Admin Page Framework - Checkbox Field Type 0.0.2 */
(function($){

  var apfMain  = AmazonAutoLinks_AdminPageFrameworkScriptFormMain;
  var apfRadio = AmazonAutoLinks_AdminPageFrameworkFieldTypeRadio;

  $( document ).ready( function(){
    if ( 'undefined' === apfRadio ) {
      return;
    }
    debugLog( apfRadio );

    $().registerAmazonAutoLinks_AdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
          oCloned.find( 'input[type=radio]' )
            .off( 'change' )
            .on( 'change', function( e ) {
          
            // Uncheck the other radio buttons
            // prop( 'checked', ... ) does not seem to take effect so use .attr( 'checked' ) also.
            // removeAttr( 'checked' ) causes JQMIGRATE warnings for its deprecation.
            $( this ).closest( '.amazon-auto-links-field' ).find( 'input[type=radio]' )
              .prop( 'checked', false )
              .attr( 'checked', false );

            // Make sure the clicked item is checked
            $( this )
              .prop( 'checked', true )
              .attr( 'checked', 'checked' );
          });                      
        },
      },
      apfRadio.fieldTypeSlugs
    );

  });

  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF (radio)', ...msg );
  }

}(jQuery));