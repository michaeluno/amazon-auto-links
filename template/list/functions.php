<?php
class AmazonAutoLinks_Template_List_ItemFormatter {

    public function __construct() {
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $_sTemplateID     = $_oTemplateOption->getTemplateID( dirname( __FILE__ ) );
        add_filter( 'aal_filter_template_default_item_format_' . $_sTemplateID, array( $this, 'replyToGetDefaultItemFormat' ) );
    }

    public function replyToGetDefaultItemFormat( $sItemFormat ) {
        $_oOption     = AmazonAutoLinks_Option::getInstance();
        $_sItemFormat = $_oOption->isAPIConnected()
            ? $_oOption->getDefaultItemFormatConnected()
            : $_oOption->getDefaultItemFormatDisconnected();
        return str_replace( 'min-width: %image_size%px;', '', $_sItemFormat );
    }

}
new AmazonAutoLinks_Template_List_ItemFormatter;