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
 * Renders unit preview outputs by respecting the theme style.
 * @since 5.1.0
 */
class AmazonAutoLinks_Main_Event_UnitPreview extends AmazonAutoLinks_Utility {

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        if ( ! isset( $_GET[ 'aal-unit-preview' ] ) ) {
            return;
        }
        if ( ! $_GET[ 'aal-unit-preview' ] ) {
            return;
        }
        add_action( 'wp', array( $this, 'replyToRenderUnitPreview' ) );
    }
    public function replyToRenderUnitPreview() {

        $_sHeadTag   = "<head>"
              . $this->___getHeadTagExtracted()
           . "</head>";

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
    <?php echo $_sHeadTag ; ?>
    <body>
        <div id="aal-unit-preview" style="padding: 1em;">
            <?php echo $this->___getUnitPreview( $this->getHTTPQueryGET() ); ?>
        </div>
        <?php do_action( 'wp_footer' ) ?>
    </body>
</html>
        <?php
        exit;
    }

        /**
         * @return string
         * @sinec  5.1.0
         */
        private function ___getUnitPreview( array $aArguments ) {
            $_iID = $this->getElement( $aArguments, array( 'id' ) );
            if ( ! $_iID ) {
                return "<p>" . __( 'Select a unit.', 'amazon-auto-links' ) . "</p>";
            }
            return AmazonAutoLinks( $aArguments, false );
        }

        /**
         * @return string
         * @since  5.1.0
         */
        private function ___getHeadTagExtracted() {
            $_sHeader = $this->getOutputBuffer( 'get_header' );
            $_sHeader = force_balance_tags( $_sHeader );
            $_sHeader = str_replace( array( "\n", "\r\n", "\r" ), '', $_sHeader ); // prevents `&#13;` from being inserted
            $_oDOM    = new AmazonAutoLinks_DOM;
            $_oDoc    = $_oDOM->loadDOMFromHTML( $_sHeader );
            $_oXpath  = new DOMXPath( $_oDoc );
            $_oTags   = $_oXpath->query( "/html/head" );
            $_oTag    = $_oTags->item( 0 );
            return $_oDOM->getInnerHTML( $_oTag );
        }

}
