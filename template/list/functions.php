<?php
/**
 * The class existent check is needed to avoid multiple declarations of the same class.
 * It occurs upon installing a test version with a different plugin slug (directory name)
 * due to loading the template files of the old locations, which the plugin remembers with the templates options.
 */
if ( ! class_exists( 'AmazonAutoLinks_Template_List_ItemFormatter', false ) ) {

    class AmazonAutoLinks_Template_List_ItemFormatter {

        static public $sTemplateID;

        public function __construct() {

            if ( isset( self::$sTemplateID ) ) {
                return;
            }
            $_oTemplateOption  = AmazonAutoLinks_TemplateOption::getInstance();
            self::$sTemplateID = $_oTemplateOption->getTemplateID( dirname( __FILE__ ) );
            add_filter( 'aal_filter_template_default_item_format_' . self::$sTemplateID, array( $this, 'replyToGetDefaultItemFormat' ), 10, 2 );

        }

        /**
         * @param string $sItemFormat
         * @param string $sLocale
         *
         * @return string|string[]
         */
        public function replyToGetDefaultItemFormat( $sItemFormat, $sLocale ) {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_sItemFormat = ( boolean ) $_oOption->getPAAPIStatus( $sLocale )
                ? $_oOption->getDefaultItemFormatConnected()
                : $_oOption->getDefaultItemFormatDisconnected();
            return str_replace( 'min-width: %image_size%px;', '', $_sItemFormat );
        }

    }

}
new AmazonAutoLinks_Template_List_ItemFormatter;