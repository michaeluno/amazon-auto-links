<?php
/*
 * Available variables:
 * 
 * $arrOptions - the plugin options
 * $arrProducts - the fetched product links
 * $arrArgs - the user defined arguments such as image size and count etc.
 */

$_aStructure_Product = array(
	'product_url' => '',
	'title' => '',
	'text_description' => '',
	'content' => '',
	'description' => '',
	'image_size' => '',
	'product_url' => '',
	'thumbnail_url' => '',	
	'author' => '',
	'ASIN' => '',
	'date' => '',
	'is_adult_product' 	=> '',
	'price' => '',
	'lowest_new_price' => '', 
	'lowest_used_price' => '',
); 

$sClassAttributes_ProductsContainer = 'amazon-products-container-search' . ' amazon-unit-' . $arrArgs['id'];
$sClassAttributes_ProductsContainer .= empty( $arrArgs['_labels'] ) ? '' : ' amazon-label-' . implode( ' amazon-label-', $arrArgs['_labels'] );
		
?>
<?php if ( ! isset( $arrProducts ) || empty( $arrProducts ) ) : ?>
	<div><p><?php _e( 'No products found.', 'amazon-auto-links' ); ?></p></div>
	<?php return; ?>
<?php endif; ?>	
<?php if ( isset( $arrProducts['Error']['Message'], $arrProducts['Error']['Code'] ) ) : ?>	
	<div class="error">
		<p>
			<?php echo $arrProducts['Error']['Code'] . ': '. $arrProducts['Error']['Message']; ?>
		</p>
	</div>
<?php return; ?>
<?php endif; ?>

<div class="<?php echo $sClassAttributes_ProductsContainer; ?>">
<?php foreach( $arrProducts as $_aProduct ) : ?>
	<?php $_aProduct = $_aProduct + $_aStructure_Product; ?>
	<div class="amazon-product-container">
		<?php echo $_aProduct['formatted_item']; ?>
	</div>
<?php endforeach; ?>	
</div>