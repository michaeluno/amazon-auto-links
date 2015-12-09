<?php
class AmazonAutoLinks_AdminPageFramework_AdminNotice___Script extends AmazonAutoLinks_AdminPageFramework_Factory___Script_Base {
    public function load() {
        wp_enqueue_script('jquery');
    }
    static public function getScript() {
        return <<<JAVASCRIPTS
( function( $ ) {
    jQuery( document ).ready( function() {         

        var _oAdminNotices = jQuery( '.amazon-auto-links-settings-notice-message' );
        if ( _oAdminNotices.length ) {
                    
            // Animation of the `slideDown()` method does not work well when the target element has a margin
            // so enclose the elemnet in a new container and apply new margins to it.
            var _oContainer     = jQuery( _oAdminNotices )
                .css( 'margin', '0' )   // prevents jumpy animation
                .wrap( "<div class='amazon-auto-links-admin-notice-animation-container'></div>" );
            _oContainer.css( 'margin-top', '1em' );
            _oContainer.css( 'margin-bottom', '1em' );
            
            // Now animate.
            jQuery( _oAdminNotices )
                .css( 'visibility', 'hidden' )
                .slideDown( 800 )
                .css( {opacity: 0, visibility: 'visible'})
                .animate( {opacity: 1}, 400 );
                
        }

    });              

}( jQuery ));
JAVASCRIPTS;
        
    }
}