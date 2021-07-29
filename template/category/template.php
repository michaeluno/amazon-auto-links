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
 *
 * @var array $aOptions the plugin options
 * @var array $aProducts the fetched product links
 * @var array $aArguments the user defined unit arguments such as image size and count etc.
 */

$_aStructure_Product = array(
    'product_url'       => '',
    'title'             => '',
    'text_description'  => '',
    'description'       => '',
    'image_size'        => '',
    'thumbnail_url'     => '',    
);
  
$sClassAttributes_ProductsContainer = 'amazon-products-container' . ' amazon-unit-' . $aArguments[ 'id' ];
$sClassAttributes_ProductsContainer .= empty( $aArguments['_labels'] ) ? '' : ' amazon-label-' . implode( ' amazon-label-', $aArguments[ '_labels' ] );
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
<?php if ( empty( $aProducts ) ) : ?>
    <div><p><?php _e( 'No products found.', 'amazon-auto-links' ); ?></p></div>  
    <?php return true; ?>
<?php endif; ?>    

<?php if ( isset( $aProducts[ 'Error' ][ 'Message' ], $aProducts[ 'Error' ][ 'Code' ] ) ) : ?>
    <div class="error">
        <p>
            <?php echo AmazonAutoLinks_Registry::NAME . ': ' . $aProducts[ 'Error' ][ 'Code' ] . ': '. $aProducts[ 'Error' ][ 'Message' ]; ?>
        </p>
    </div>
<?php return true; ?>
<?php endif; ?>
    
<div class="<?php echo $sClassAttributes_ProductsContainer; ?>" style="<?php echo $_sInlineStyle; ?>">
<?php foreach( $aProducts as $_aProduct ) : ?>
    <?php $_aProduct = $_aProduct + $_aStructure_Product; ?>
    <div class="amazon-product-container">
        <?php echo $_aProduct[ 'formatted_item' ]; ?>
    </div>
<?php endforeach; ?>    
</div>
