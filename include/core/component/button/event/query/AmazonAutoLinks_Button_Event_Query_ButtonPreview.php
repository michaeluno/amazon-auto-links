<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */


/**
 * Outputs the button preview.
 *
 * @package    Amazon Auto Links
 * @since      4.3.0
 */
class AmazonAutoLinks_Button_Event_Query_ButtonPreview extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks and properties.
     */
    public function __construct() {

        if ( ! isset( $_GET[ 'aal-button-preview' ] ) ) {
            return;
        }
        if ( ! $_GET[ 'aal-button-preview' ] ) {
            return;
        }
        add_action( 'wp', array( $this, 'replyToPrintButtonPreview' ) );

    }

    /**
     * @see WP_Styles
     */
    public function replyToPrintButtonPreview() {
        wp_enqueue_script( 'jquery' );
        $_sButtonLabel = $this->getElement( $_GET, array( 'button-label' ), 'Buy Now' );
        $_iButtonID    = $this->getElement( $_GET, array( 'button-id' ), 0 );
        $_sHeader      = $this->getOutputBuffer( 'get_header' );
        $_sHeader      = force_balance_tags( $_sHeader );
        $_sHeader      = str_replace( array( "\n", "\r\n", "\r" ), '', $_sHeader ); // prevents `&#13;` from being inserted
        $_oDOM         = new AmazonAutoLinks_DOM;
        $_oDoc         = $_oDOM->loadDOMFromHTML( $_sHeader );
        $_oDOM->removeTags( $_oDoc, array( 'script' ) );    // in order to use jQuery, comment out this line.
        $_oXpath       = new DOMXPath( $_oDoc );
        $_oTags        = $_oXpath->query( "/html/head" );
        $_oTag         = $_oTags->item( 0 );
        $_sScript      = <<<SCRIPT
<script type="text/javascript">

    function aalCallSetButtonPreviewIframeStyle() {
        var _oButton = document.getElementById( "preview-button" );
        var _iWidth  = 0;
        var _iHeight = 0;
        if ( 'undefined' !== typeof _oButton && null !== _oButton  ) {
            _iWidth  = _oButton.offsetWidth;
            _iHeight = _oButton.offsetHeight;
        }         
        if ( 'undefined' !== typeof parent.aalSetButtonPreviewIframeStyle ) {
            window.parent.aalSetButtonPreviewIframeStyle( _iWidth, _iHeight );            
        }        
    }
    window.addEventListener( 'DOMContentLoaded', function( e ) {
        aalCallSetButtonPreviewIframeStyle(); 
    } );
</script>
SCRIPT;
        $_sHead        = "<head>" . $_oDOM->getInnerHTML( $_oTag ) . $_sScript . "</head>";

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
    <?php echo $_sHead; ?>
    <body>
        <div id="preview-button" style="display: inline-block;"><?php //inline-block to fit the width to the child element. ?>
            <?php echo $this->getButton( $_iButtonID, $_sButtonLabel, true, false ); ?>
        </div>
    </body>
</html>
        <?php
        exit;
    }

}