<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Outputs for plugin custom oEmbed iframe requests.
 *
 * This is necessary for Gutenberg embed blocks to render previews.
 *
 * @package      Amazon Auto Links
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Content_Iframe {

    /**
     *
     * @see WP_oEmbed::__construct()
     */
    public function __construct() {

        if ( ! isset( $_GET[ 'embed' ] ) ) {
            return;
        }
        if ( $_GET[ 'embed' ] !== 'amazon-auto-links' ) {
            return;
        }
        add_action( 'init', array( $this, 'replyToLoadFrame' ) );

    }

    /**
     * Prints the iFrame content and exits.
     */
    public function replyToLoadFrame() {


        $this->___printHeader();
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
        // removes wp-embed-templates.js as it causes clicks not working
        remove_action( 'embed_footer', 'print_embed_scripts' );
        add_action( 'embed_footer', array( $this, 'printEmbedScripts' ) );
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
        if ( ! isset( $_GET[ 'uri' ] ) ) {
            echo "<p>The URL is not passed.</p>";
            AmazonAutoLinks_Debug::dump( $_REQUEST );
            return;
        }

        $_oOption      = AmazonAutoLinks_Option::getInstance();
        $_aArguments   = array(
            'uri'         => urldecode( $_GET[ 'uri' ] ),
            'template_id' => $_oOption->get( array( 'custom_oembed', 'template_id' ) ),
        );
        AmazonAutoLinks( $_aArguments );

    }


    /**
     * Prints the JavaScript in the embed iframe footer.
     *
     * @see print_embed_scripts()
     */
    public function printEmbedScripts() {
        $_sTypeAttribute = current_theme_supports( 'html5', 'script' )
            ? ''
            : ' type="text/javascript"';
        ?>
        <script<?php echo $_sTypeAttribute; ?>>
        <?php
            $_sComponentDirPath = AmazonAutoLinks_CustomOEmbed_Loader::$sDirPath;
            readfile( $_sComponentDirPath . '/asset/js/wp-embed-template-lite.js' );
        ?>
        </script>
        <?php
    }

}