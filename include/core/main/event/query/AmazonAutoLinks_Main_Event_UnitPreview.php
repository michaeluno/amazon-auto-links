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
        add_action( 'wp_enqueue_scripts', array( $this, 'replyToLoadResources' ), 1000 );
    }
    public function replyToLoadResources() {
        wp_dequeue_script( 'aal-iframe-height-adjuster' );
        // Somehow, enqueueing this script halts Gutenberg
        // wp_enqueue_script( 'aal-content-height-notifier' );
    }
    public function replyToRenderUnitPreview() {
        // do_action( 'template_redirect' );   // tells WordPress to use the theme // doesn't look well with some themes. And adds the navigation admin bar at the top.
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
    <head>
    <?php echo $this->getOutputBuffer( array( $this, '___replyToSimulateHTMLHeader' ) ); ?>
    <style type='text/css'><?php echo $this->___getHeaderCSSRules(); ?></style>
    </head>
    <body>
        <div id="aal-unit-preview">
            <?php echo $this->___getUnitPreview( $this->getHTTPQueryGET() ); ?>
        </div>
        <?php
            do_action( 'wp_footer' );
            $this->___printFooterScript();
        ?>
    </body>
</html>
        <?php
        exit;
    }
        /**
         * @return string
         * @since  5.1.0
         */
        private function ___getHeaderCSSRules(){
            return <<<CSSRULES
/* Give some space around the output */
#aal-unit-preview {
    padding: 1em 1.6em;
}
/* For the Twenty Fifteen theme which adds a left sidebar */
body:before {
    content: none;
}
CSSRULES;
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
         * Simulates a blog header output.
         * @since  5.1.0
         * @see    ABSPATH . WPINC . '/theme-compat/header.php'
         * @remark This is an alternative for `get_header()`, which produces the warning, "theme without header.php is deprecated”
         */
        public function ___replyToSimulateHTMLHeader() {
            ?>
<link rel="profile" href="https://gmpg.org/xfn/11" />
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />

<?php if ( file_exists( get_stylesheet_directory() . '/images/kubrickbgwide.jpg' ) ) { ?>
<style type="text/css" media="screen">
    #page { background: url("<?php bloginfo( 'stylesheet_directory' ); ?>/images/kubrickbgwide.jpg") repeat-y top; border: none; }
</style>
<?php } ?>
            <?php
                wp_head();
        }

        /**
         * @since 5.1.0
         */
        private function ___printFooterScript() {
            ?>
            <script<?php echo ( current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"' ); ?>>
            <?php
                $_sMin       = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
                $_sFilePath  = AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/js/wp-embed-template-lite{$_sMin}.js";
                readfile( $_sFilePath );
            ?>
            </script>
            <?php
        }

}