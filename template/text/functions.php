<?php




/**
 * Instantiate this class in the template.
 * This removes the <div class="amazon-auto-links">...</div> wrapper from the output.
 *
 * ### Usage
 * Just instantiate the class.
 */
class AmazonAutoLinks_Template_Text_NoOuterContainer {
    public function __construct() {
        add_filter( 'aal_filter_output_is_without_outer_container', array( $this, 'replyToSetNoOuterContainer' ), 10, 2 );
    }
    /**
     * @callback    filter      aal_filter_output_is_without_outer_container
     */
    public function replyToSetNoOuterContainer() {
        remove_filter( 'aal_filter_output_is_without_outer_container', array( $this, 'replyToSetNoOuterContainer' ), 10 );
        return true;
    }
}

class AmazonAutoLinks_Template_Text {

    public function __construct() {
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $_sTemplateID     = $_oTemplateOption->getTemplateID( dirname( __FILE__ ) );
        add_filter( 'aal_filter_template_default_item_format_' . $_sTemplateID, array( $this, 'replyToGetDefaultItemFormat' ) );
    }

    public function replyToGetDefaultItemFormat( $sItemFormat ) {
        return  '<a href="%href%" title="%title_text%: %description_text%" rel="nofollow noopener" target="_blank">'
                    . '%title_text%'
                . '</a>';
    }

}
new AmazonAutoLinks_Template_Text;