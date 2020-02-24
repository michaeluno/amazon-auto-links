<?php




class AmazonAutoLinks_Template_Image {

    public function __construct() {
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $_sTemplateID     = $_oTemplateOption->getTemplateID( dirname( __FILE__ ) );
        add_filter( 'aal_filter_template_default_item_format_' . $_sTemplateID, array( $this, 'replyToGetDefaultItemFormat' ) );
    }

    public function replyToGetDefaultItemFormat( $sItemFormat ) {
        return  '<a href="%href%" title="%title_text%: %description_text%" rel="nofollow noopener" target="_blank">'
                    . '%image%'
                . '</a>';
    }

}
new AmazonAutoLinks_Template_Image;