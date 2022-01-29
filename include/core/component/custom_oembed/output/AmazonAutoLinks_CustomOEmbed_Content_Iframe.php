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
 * Outputs for plugin custom oEmbed iframe requests.
 *
 * This is necessary for Gutenberg embed blocks to render previews.
 *
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Content_Iframe {

    /**
     *
     * @see WP_oEmbed::__construct()
     */
    public function __construct() {

        if ( ! isset( $_GET[ 'embed' ] ) ) {    // sanitization unnecessary as just checking
            return;
        }
        if ( $_GET[ 'embed' ] !== 'amazon-auto-links' ) {   // sanitization unnecessary as just checking
            return;
        }
        add_action( 'init', array( $this, 'replyToLoadFrame' ) );

    }

    /**
     * Prints the iFrame content and exits.
     */
    public function replyToLoadFrame() {

        $this->___printHeader();
        wp_enqueue_script( 'aal-content-height-notifier' ); // this is registered in the main component
        $this->___printContent();
        $this->___printFooter();
        exit();

    }

    /**
     * @see /wp-includes/theme-compat/header-embed.php
     */
    private function ___printHeader() {

        if ( ! headers_sent() ) {
            header( 'X-WP-embed: true' );

            // @todo if external site option is enabled add this
            // header('Access-Control-Allow-Origin: *' );
        }

        add_action( 'embed_head', 'wp_custom_css_cb', 101 );    // The custom CSS rules set via Customize -> Additional CSS

        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?> class="no-js">
        <head>
            <title><?php echo wp_get_document_title(); ?></title>
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <?php
            /**
             * Prints scripts or data in the embed template <head> tag.
             */
            do_action( 'embed_head' );
            ?>

            <!--<base target="_blank">-->
        </head>
        <body <?php // body_class(); ?>>
        <?php
    }

    /**
     * @see /wp-includes/theme-compat/embed-content.php
     */
    private function ___printContent() {
    ?>
    <div <?php post_class( 'wp-embed' ); ?>>

        <div class="wp-embed-excerpt"><?php $this->___printProductOutput(); ?></div>

        <?php
        /**
         * Prints additional content after the embed excerpt.
         *
         */
        do_action( 'embed_content' );
        ?>
    </div>
<?php
    }

    /**
     * @see /wp-includes/theme-compat/footer-embed.php
     */
    private function ___printFooter() {

        /**
         * Prints scripts or data before the closing body tag in the embed template.
         */
        // Remove wp-embed-templates.js as it causes clicks not working
        remove_action( 'embed_footer', 'print_embed_scripts' );
        do_action( 'embed_footer' );
        ?>
        </body>
        </html>
        <?php
    }

    /**
     * Prints the product output.
     */
    private function ___printProductOutput() {

        if ( ! isset( $_GET[ 'uri' ] ) ) {  // sanitization unnecessary as just checking
            echo "<p>The URL is not passed.</p>";
            return;
        }

        $_oOption      = AmazonAutoLinks_Option::getInstance();
        $_aArguments   = array(
            'uri'         => urldecode( AmazonAutoLinks_PluginUtility::getURLSanitized( $_GET[ 'uri' ] ) ), // sanitization done
            'template_id' => $_oOption->get( array( 'custom_oembed', 'template_id' ) ),
        );
        AmazonAutoLinks( $_aArguments );

    }

}