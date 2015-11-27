<?php
class AmazonAutoLinks_AdminPageFramework_Form_View___Script_OptionStorage extends AmazonAutoLinks_AdminPageFramework_Form_View___Script_Base {
    static public function getScript() {
        return <<<JAVASCRIPTS
(function ( $ ) {
            
    $.fn.aAmazonAutoLinks_AdminPageFrameworkInputOptions = {}; 
                            
    $.fn.storeAmazonAutoLinks_AdminPageFrameworkInputOptions = function( sID, vOptions ) {
        var sID = sID.replace( /__\d+_/, '___' );	// remove the section index. The g modifier is not used so it will replace only the first occurrence.
        $.fn.aAmazonAutoLinks_AdminPageFrameworkInputOptions[ sID ] = vOptions;
    };	
    $.fn.getAmazonAutoLinks_AdminPageFrameworkInputOptions = function( sID ) {
        var sID = sID.replace( /__\d+_/, '___' ); // remove the section index
        return ( 'undefined' === typeof $.fn.aAmazonAutoLinks_AdminPageFrameworkInputOptions[ sID ] )
            ? null
            : $.fn.aAmazonAutoLinks_AdminPageFrameworkInputOptions[ sID ];
    }

}( jQuery ));
JAVASCRIPTS;
        
    }
}