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
 * Available variables.
 * @var AmazonAutoLinks_Option $oOption
 * @var array $aOptions the plugin options @deprecated use $oOption
 * @var array $aProducts the fetched product links
 * @var array $aArguments the user defined unit arguments such as image size and count etc.
 */

$sClassAttributes_ProductsContainer = 'amazon-products-container-search' . ' amazon-unit-' . $aArguments[ 'id' ];
$sClassAttributes_ProductsContainer .= empty( $aArguments[ '_labels' ] ) ? '' : ' amazon-label-' . implode( ' amazon-label-', $aArguments[ '_labels' ] );
$sClassAttributes_ProductsContainer .= empty( $aArguments[ 'unit_type' ] ) ? '' : ' unit-type-' . $aArguments[ 'unit_type' ];

$_sWidth  = AmazonAutoLinks_PluginUtility::getDegree( 'width', $aArguments );
$_sWidth  = $_sWidth
    ? "width: {$_sWidth};"
    : '';
$_sHeight = AmazonAutoLinks_PluginUtility::getDegree( 'height', $aArguments );
$_sHeight = $_sHeight
    ? "height: {$_sHeight};"
    : '';
$_sInlineStyle = $_sWidth . $_sHeight;

?>

<div class="<?php echo esc_attr( $sClassAttributes_ProductsContainer ); ?>" style="<?php echo esc_attr( $_sInlineStyle ); ?>">
<?php foreach( $aProducts as $_aProduct ) : ?>
    <div class="amazon-product-container">
        <?php echo wp_kses( $_aProduct[ 'formatted_item' ], $oOption->getAllowedHTMLTags() ); ?>
    </div>
<?php endforeach; ?>
</div>