<?php
class AmazonAutoLinks_Template_List_ItemFormatter {

    public function __construct() {
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $_sTemplateID     = $_oTemplateOption->getTemplateID( dirname( __FILE__ ) );
        add_filter( 'aal_filter_template_default_item_format_' . $_sTemplateID, array( $this, 'replyToGetDefaultItemFormat' ), 10, 2 );
    }

    /**
     * @param  string $sItemFormat
     * @param  string $sLocale
     * @return string|string[]
     */
    public function replyToGetDefaultItemFormat( $sItemFormat, $sLocale ) {
        $_oOption     = AmazonAutoLinks_Option::getInstance();
        $_sItemFormat = ( boolean ) $_oOption->getPAAPIStatus( $sLocale )
            ? $_oOption->getDefaultItemFormatConnected()
            : $_oOption->getDefaultItemFormatDisconnected();
        return str_replace( 'min-width: %image_size%px;', '', $_sItemFormat );
    }

}
new AmazonAutoLinks_Template_List_ItemFormatter;