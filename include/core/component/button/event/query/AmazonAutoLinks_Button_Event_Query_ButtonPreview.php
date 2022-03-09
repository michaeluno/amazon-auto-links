<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Outputs the button preview.
 *
 * @since 4.3.0
 */
class AmazonAutoLinks_Button_Event_Query_ButtonPreview extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks and properties.
     */
    public function __construct() {

        if ( ! isset( $_GET[ 'aal-button-preview' ] ) ) {       // sanitization unnecessary as just checking
            return;
        }
        if ( ! $_GET[ 'aal-button-preview' ] ) {                // sanitization unnecessary as just checking
            return;
        }
        add_action( 'wp', array( $this, 'replyToPrintButtonPreview' ) );
        add_filter( 'wp_using_themes', '__return_true' );

    }

    /**
     * @see WP_Styles
     * @see wp_head()
     */
    public function replyToPrintButtonPreview() {

        wp_enqueue_script( 'jquery' );
        $_sButtonLabel   = $this->getHTTPQueryGET( 'button-label', 'Buy Now' );
        $_iButtonID      = ( integer ) $this->getHTTPQueryGET( 'button-id', 0 );
        $_oDOM           = new AmazonAutoLinks_DOM;
        $_oDoc           = $_oDOM->loadDOMFromHTML(
            "<html><head>" . $this->___getHTMLHeaderConstructed() . "</head><body></body></html>"
        );
        $this->___removeScriptTags(
            $_oDoc,
            array() // to allow jQuery, pass `array( 'jquery' )` to the second parameter
        );
        $_oXpath         = new DOMXPath( $_oDoc );
        $_oHeadTags      = $_oXpath->query( "/html/head" );
        $_oHeadTag       = $_oHeadTags->item( 0 );
        $_sMin           = $this->isDebugMode() ? '' : '.min';
        $_sStylesheetURL = $this->getSRCFromPath( AmazonAutoLinks_ButtonLoader::$sDirPath . "/asset/css/button-preview-framed-page{$_sMin}.css" );
        $_sScriptURL     = $this->getSRCFromPath( AmazonAutoLinks_ButtonLoader::$sDirPath . "/asset/js/button-preview-framed-page{$_sMin}.js" );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <?php echo "<head>"
       . $_oDOM->getInnerHTML( $_oHeadTag )
       . "<script id='button-preview-framed-js' src='" . esc_url( $_sScriptURL ) . "' type='text/javascript'></script>"
       . "<link rel='stylesheet' id='button-preview-framed-style' href='" . esc_url( $_sStylesheetURL ) . "' media='all'>"
    . "</head>"; ?>
    <body>
        <div id="preview-button">
            <?php echo wp_kses( $this->getButton( $_iButtonID, $_sButtonLabel, true, false ), 'post' ); ?>
        </div>
    </body>
</html>
        <?php
        exit;
    }

        /**
         * @return string
         */
        private function ___getHTMLHeaderConstructed() {
            $_sHeader = $this->getOutputBuffer( 'wp_head' );
            $_sHeader = force_balance_tags( $_sHeader );
            return str_replace( array( "\n", "\r\n", "\r" ), '', $_sHeader ); // prevents `&#13;` from being inserted
        }
        /**
         * Removes specified tags from the given dom node.
         * @param DOMDocument $oDom
         * @param array       $aIDPrefixesToAllow
         */
        private function ___removeScriptTags( DOMDocument $oDom, $aIDPrefixesToAllow=array() ) {
            $_oXpath    = new DOMXPath( $oDom );
            $_oNodeList = $_oXpath->query( "//*/script" );
            if ( false === $_oNodeList ) {
                return;
            }
            foreach( $_oNodeList as $_oNode ) {
                if ( $this->___isAllowedScript( $aIDPrefixesToAllow, $_oNode->getAttribute( 'id' ) ) ) {
                    continue;
                }
                /**
                 * @var DOMNode $_oNode
                 */
                $_oNode->parentNode->removeChild( $_oNode );
            }
        }
            private function ___isAllowedScript( array $aIDPrefixes, $sAttribute ) {
                foreach( $aIDPrefixes as $_sIDPrefix ) {
                    if ( $this->hasPrefix( $_sIDPrefix, $sAttribute ) ) {
                        return true;
                    }                    
                }
                return false;
            }

}