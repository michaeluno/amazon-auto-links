/*! Admin Page Framework - Import Field Type 0.0.1 */
/** global: AmazonAutoLinks_AdminPageFrameworkScriptFormMain */
var apfMain  = AmazonAutoLinks_AdminPageFrameworkScriptFormMain;
/** global: AmazonAutoLinks_AdminPageFrameworkImportFieldType */
var apfImport = AmazonAutoLinks_AdminPageFrameworkImportFieldType;
(function ( $ ) {
  
  debugLog( '0.0.1', apfImport );

  $( document ).ready( function () {
    $( '.amazon-auto-links-field-import input[type=submit]' ).on( 'click', function ( event ) {
      var _iFiles = $( this ).closest( '.amazon-auto-links-field-import' ).find( 'input[type=file]' ).get( 0 ).files.length;
      if ( 0 === _iFiles ) {
        alert( apfImport.label.noFile );
        return false;
      }
      return true;
    } );
  } ); // document ready  
  
  function debugLog( ...msg ) {
    if ( ! parseInt( apfMain.debugMode ) ) {
      return;
    }
    console.log( 'APF Import Field Type', ...msg );
  }

}( jQuery ));