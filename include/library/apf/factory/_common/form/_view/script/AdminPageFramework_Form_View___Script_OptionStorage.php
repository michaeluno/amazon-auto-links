<?php 
/**
	Admin Page Framework v3.8.23b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2020, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
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
    