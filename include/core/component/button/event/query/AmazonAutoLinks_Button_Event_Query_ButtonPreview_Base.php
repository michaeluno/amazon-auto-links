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
 * A base class for button preview classes.
 *
 * @since 5.2.0
 */
abstract class AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * @var   array Supported button types
     * @since 5.2.0
     */
    public $aButtonTypes = array();

    /**
     * @since 5.2.0
     * @var   string[]
     */
    protected $_aAllowedIDPrefixesJS     = array( 'jquery' );

    /**
     * @since 5.2.0
     * @var   string[]
     */
    protected $_aDisallowedIDPrefixesCSS = array();

    /**
     * Sets up hooks and properties.
     * @since 5.2.0
     */
    public function __construct() {

        if ( ! $this->_shouldProceed( $this->getHTTPQueryGET( 'aal-button-preview' ) ) ) {
            return;
        }
        add_action( 'wp', array( $this, 'replyToPrintButtonPreview' ) );
        add_filter( 'wp_using_themes', '__return_true' );

    }

    /**
     * @since  5.2.0
     * @param  string  $sButtonType
     * @return boolean
     */
    protected function _shouldProceed( $sButtonType ) {
        return ! empty( $sButtonType ) && in_array( $sButtonType, $this->aButtonTypes, true );
    }

    /**
     * @see WP_Styles
     * @see wp_head()
     */
    public function replyToPrintButtonPreview() {
        $_isButtonID       = $this->getHTTPQueryGET( 'button-id', 0 );
        $_sButtonLabelMeta = $_isButtonID && is_numeric( $_isButtonID )
            ? get_post_meta( $_isButtonID, 'button_label', true )
            : '';
        $_sButtonLabel     = $this->getHTTPQueryGET( 'button-label', $_sButtonLabelMeta ? $_sButtonLabelMeta : 'Buy Now' );
        $this->___printButtonPreviewContent( $_isButtonID, $_sButtonLabel );
        exit;
    }
        /**
         * @since 5.2.0
         */
        private function ___printButtonPreviewContent( $isButtonID, $sButtonLabel ) {
            ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <?php echo "<head>"
       . $this->_getHeadTagInnerHTML()
    . "</head>"; ?>
    <body>
        <?php echo wp_kses( $this->_getBodyTagInnerHTML( $isButtonID, $sButtonLabel ), 'post' ); ?>
    </body>
</html>
            <?php
        }

    /**
     * @since  5.2.0
     * @param  integer|string $isButtonID   An integer value of button post ID or string of `___button_id___`
     * @param  string         $sButtonLabel
     * @return string
     */
    protected function _getBodyTagInnerHTML( $isButtonID, $sButtonLabel ) {
        return "<div id='preview-button' data-button-id='" . esc_attr( $isButtonID ) . "'>"
                . $this->getButton( $isButtonID, $sButtonLabel, true, false )
            . "</div>";
    }
    /**
     * @since  5.2.0
     * @return string
     */
    protected function _getHeadTagInnerHTML() {
        wp_enqueue_script( 'jquery' );
        $_oDOM      = new AmazonAutoLinks_DOM;
        $_oDoc      = $_oDOM->loadDOMFromHTML(
            "<html><head>" . $this->___getHTMLHeaderConstructed() . "</head><body></body></html>"
        );
        /**
         * Stylesheets are all allowed except ones that with specified ID attribute prefixes.
         * JavaScripts are all disallowed except ones that with specified ID attribute prefixes.
         */
        $this->___removeStyleSheets( $_oDoc, $this->_aDisallowedIDPrefixesCSS );
        $this->___removeScriptTags( $_oDoc, $this->_aAllowedIDPrefixesJS );
        $_oXpath    = new DOMXPath( $_oDoc );
        $_oHeadTags = $_oXpath->query( "/html/head" );
        $_oHeadTag  = $_oHeadTags->item( 0 );
        return $this->_getExtraResourcesAddedToHead( $_oDOM->getInnerHTML( $_oHeadTag ) );
    }
        /**
         * @return string
         */
        private function ___getHTMLHeaderConstructed() {
            $_sHeader = $this->getOutputBuffer( 'wp_head' );
            $_sHeader = force_balance_tags( $_sHeader );
            return str_replace( array( "\n", "\r\n", "\r" ), '', $_sHeader ); // prevents `&#13;` from being inserted
        }
        private function ___removeStyleSheets( DOMDocument $oDom, $aIDPrefixesToRemove=array() ) {
            $_oXpath    = new DOMXPath( $oDom );
            $_oNodeList = $_oXpath->query( "//*/*[self::style or self::link]" ); // select `style` or `link` tags
            if ( false === $_oNodeList ) {
                return;
            }
            foreach( $_oNodeList as $_oNode ) {
                if ( $this->___hasPrefixInArray( $aIDPrefixesToRemove, $_oNode->getAttribute( 'id' ) ) ) {
                    /**
                     * @var DOMNode $_oNode
                     */
                    $_oNode->parentNode->removeChild( $_oNode );
                }
            }
        }
            private function ___hasPrefixInArray( array $aIDPrefixes, $sAttribute ) {
                foreach( $aIDPrefixes as $_sIDPrefix ) {
                    if ( $this->hasPrefix( $_sIDPrefix, $sAttribute ) ) {
                        return true;
                    }
                }
                return false;
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
                if ( $this->___hasPrefixInArray( $aIDPrefixesToAllow, $_oNode->getAttribute( 'id' ) ) ) {   // is allowed?
                    continue;
                }
                /**
                 * @var DOMNode $_oNode
                 */
                $_oNode->parentNode->removeChild( $_oNode );
            }
        }

    /**
     * @since  5.2.0
     * @param  string $sHeadTagInnerHTML
     * @return string
     */
    protected function _getExtraResourcesAddedToHead( $sHeadTagInnerHTML ) {
        $_sMin              = $this->isDebugMode() ? '' : '.min';
        $_sStylesheetURL    = $this->getSRCFromPath( AmazonAutoLinks_Button_Loader::$sDirPath . "/asset/css/button-preview-framed-page{$_sMin}.css" );
        $_sScriptURL        = $this->getSRCFromPath( AmazonAutoLinks_Button_Loader::$sDirPath . "/asset/js/button-preview-framed-page{$_sMin}.js" );
        $sHeadTagInnerHTML .= "<script id='button-preview-framed-js' src='" . esc_url( $_sScriptURL ) . "' type='text/javascript'></script>";
        $sHeadTagInnerHTML .= "<link rel='stylesheet' id='button-preview-framed-style' href='" . esc_url( $_sStylesheetURL ) . "' media='all'>";
        return $sHeadTagInnerHTML;
    }

}