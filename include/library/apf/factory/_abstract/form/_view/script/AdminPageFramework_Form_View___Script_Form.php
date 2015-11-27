<?php
class AmazonAutoLinks_AdminPageFramework_Form_View___Script_Form extends AmazonAutoLinks_AdminPageFramework_Form_View___Script_Base {
    static public function getScript() {
        return <<<JAVASCRIPTS
( function( $ ) {

    var _removeAmazonAutoLinks_AdminPageFrameworkLoadingOutputs = function() {

        jQuery( '.amazon-auto-links-form-loading' ).remove();
        jQuery( '.amazon-auto-links-form-js-on' )
            .hide()
            .css( 'visibility', 'visible' )
            .fadeIn( 200 )
            .removeClass( '.amazon-auto-links-form-js-on' )
            ;
    
    }
    
    /**
     * Renderisn forms is heavy and unformatted layouts will be hidden with a script embedded in the head tag.
     * Now when the document is ready, restore that visibility state so that the form will appear.
     */
    jQuery( document ).ready( function() {
        _removeAmazonAutoLinks_AdminPageFrameworkLoadingOutputs();
    });    

    /**
     * Gets triggered when a widget of the framework is saved.
     * @since    DEVVER
     */
    $( document ).bind( 'admin_page_framework_saved_widget', function( event, oWidget ){
        jQuery( '.amazon-auto-links-form-loading' ).remove();
    });    
    
}( jQuery ));
JAVASCRIPTS;
        
    }
    static private $_bLoadedTabEnablerScript = false;
}