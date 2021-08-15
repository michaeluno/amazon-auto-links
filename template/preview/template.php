<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Available variables.
 * @var AmazonAutoLinks_Option $oOption
 * @var array $aOptions the plugin options @deprecated use $oOption
 * @var array $aProducts the fetched product links
 * @var array $aArguments the user defined unit arguments such as image size and count etc.
 */
$_aUsingHTMLTags = $oOption->getAllowedHTMLTags();
?>
<div class="products-container">
<?php foreach( $aProducts as $_aProduct ) : ?>
    <div class="product-container">
        <h4 class="product-title">
            <a href="<?php echo esc_url( $_aProduct[ 'product_url' ] ); ?>" title="<?php echo esc_attr( $_aProduct[ 'text_description' ] ); ?>" target="_blank" rel="nofollow">
                <?php echo wp_kses( $_aProduct[ 'title' ], $_aUsingHTMLTags ); ?>
            </a>
        </h4>
        <div class="product-thumbnail" style="width:<?php echo esc_attr( $aArguments[ 'image_size' ] ); ?>px;">
            <a href="<?php echo esc_url( $_aProduct[ 'product_url' ] ); ?>" title="<?php echo esc_attr( $_aProduct[ 'text_description' ] ); ?>" target="_blank" rel="nofollow">
                <img src="<?php echo esc_url( $_aProduct[ 'thumbnail_url' ] ); ?>" style="max-width:<?php echo esc_attr( $aArguments[ 'image_size' ] );?>px;" alt="<?php echo esc_attr( $_aProduct[ 'text_description' ] ); ?>" />
            </a>
        </div>
        <div class="product-description">
            <?php echo wp_kses( $_aProduct[ 'formatted_rating' ], $_aUsingHTMLTags ); ?>
            <?php echo wp_kses( $_aProduct[ 'description' ], $_aUsingHTMLTags ); ?>
        </div>
    </div>
<?php endforeach; ?>    
</div>
