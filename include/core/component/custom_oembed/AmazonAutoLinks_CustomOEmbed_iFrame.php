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
 *
 * @package      Amazon Auto Links
 * @since        4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_iFrame {

    /**
     *
     * @see WP_oEmbed::__construct()
     */
    public function __construct() {


        if ( ! isset( $_GET[ 'oembed' ], $_GET[ 'frame' ] ) ) {
            return;
        }
        if ( $_GET[ 'oembed' ] !== 'amazon-auto-links' ) {
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
		<?php
		$thumbnail_id = 0;

		if ( $thumbnail_id ) {

			$aspect_ratio = 1;
			$measurements = array( 1, 1 );
			$image_size   = 'full'; // Fallback.

			$meta = wp_get_attachment_metadata( $thumbnail_id );
			if ( ! empty( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $size => $data ) {
					if ( $data['height'] > 0 && $data['width'] / $data['height'] > $aspect_ratio ) {
						$aspect_ratio = $data['width'] / $data['height'];
						$measurements = array( $data['width'], $data['height'] );
						$image_size   = $size;
					}
				}
			}

			$image_size = apply_filters( 'embed_thumbnail_image_size', $image_size, $thumbnail_id );
			$shape = $measurements[0] / $measurements[1] >= 1.75 ? 'rectangular' : 'square';


		}

		if ( $thumbnail_id && 'rectangular' === $shape ) :
			?>
			<div class="wp-embed-featured-image rectangular">
				<a href="<?php the_permalink(); ?>" target="_top">
					<?php echo wp_get_attachment_image( $thumbnail_id, $image_size ); ?>
				</a>
			</div>
		<?php endif; ?>

		<p class="wp-embed-heading">
			<a href="<?php echo 'https://www.google.com'; ?>" target="_top">
				<?php echo "TEST TITLE"; ?>
			</a>
		</p>

		<?php if ( $thumbnail_id && 'square' === $shape ) : ?>
			<div class="wp-embed-featured-image square">
				<a href="<?php the_permalink(); ?>" target="_top">
					<?php echo wp_get_attachment_image( $thumbnail_id, $image_size ); ?>
				</a>
			</div>
		<?php endif; ?>

		<div class="wp-embed-excerpt"><?php $this->___printProductOutput(); ?></div>

		<?php
		/**
		 * Prints additional content after the embed excerpt.
		 *
		 */
//		do_action( 'embed_content' );
		?>

		<div class="wp-embed-footer">
			<?php the_embed_site_title(); ?>

			<div class="wp-embed-meta">
				<?php
				/**
				 * Prints additional meta content in the embed template.
				 */
//				do_action( 'embed_content_meta' );
				?>
			</div>
		</div>
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
        $_aArguments = $this->___getArgumentsFromURL( urldecode( $_GET[ 'uri' ] ) );
        AmazonAutoLinks_Debug::dump( $_aArguments );
        AmazonAutoLinks( $_aArguments );

    }
        /**
         * @param string $sURL
         * @return  array
         */
        private function ___getArgumentsFromURL( $sURL ) {
            return array(
                'country' => $this->___getLocaleFromURL( $sURL ),
                'asin'    => $this->___getASINFromURL( $sURL ),
            );
        }

            /**
             * @param $sURL
             *
             * @return string
             * @todo    this class is across the component so the method should be moved to a common component.
             */
            private function ___getASINFromURL( $sURL ) {
                return AmazonAutoLinks_Unit_Utility::getASINFromURL( $sURL );
            }
            private function ___getLocaleFromURL( $sURL ) {
                $_sDomain = parse_url( $sURL, PHP_URL_HOST );
                $_bisKey  = array_search( $_sDomain, AmazonAutoLinks_Property::$aStoreDomains );
                $_oOption = AmazonAutoLinks_Option::getInstance();
                $_sDefaultLocale = $_oOption->get( array( 'unit_default', 'country' ), 'US' );
                return false === $_bisKey
                    ? $_sDefaultLocale
                    : $_bisKey;
            }

}