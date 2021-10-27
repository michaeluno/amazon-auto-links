<?php


/**
 * The class existent check is needed to avoid multiple declarations of the same class.
 * It occurs upon installing a test version with a different plugin slug (directory name)
 * due to loading the template files of the old locations, which the plugin remembers with the templates options.
 */
if ( ! class_exists( 'AmazonAutoLinks_Template_Image', false ) ) {
    class AmazonAutoLinks_Template_Image {

        static public $sTemplateID;

        public function __construct() {
            if ( isset( self::$sTemplateID ) ) {
                return;
            }
            $_oTemplateOption  = AmazonAutoLinks_TemplateOption::getInstance();
            self::$sTemplateID = $_oTemplateOption->getTemplateID( dirname( __FILE__ ) );
            add_filter( 'aal_filter_template_default_item_format_' . self::$sTemplateID, array( $this, 'replyToGetDefaultItemFormat' ) );
        }

        public function replyToGetDefaultItemFormat( $sItemFormat ) {
            return '<a href="%href%" title="%title_text%: %description_text%" rel="nofollow noopener" target="_blank">'
                        . '%image%'
                    . '</a>';
        }

    }

}
new AmazonAutoLinks_Template_Image;