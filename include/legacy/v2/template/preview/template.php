<?php
/*
 * Available variables:
 * 
 * $arrOptions - the plugin options
 * $arrProducts - the fetched product links
 * $arrArgs - the user defined arguments such as image size and count etc.
 */
?>
<?php if ( empty( $arrProducts ) ) : ?>
	<div><p><?php _e( 'No products found.', 'amazon-auto-links' ); ?></p></div>
<?php endif; ?>	
<?php if ( isset( $arrProducts['Error']['Message'], $arrProducts['Error']['Code'] ) ) : ?>	
	<div class="error">
		<p>
			<?php echo $arrProducts['Error']['Code'] . ': '. $arrProducts['Error']['Message']; ?>
		</p>
	</div>
<?php return; ?>
<?php endif; ?>

<div class="products-container">
<?php foreach( $arrProducts as $arrProduct ) : ?>
	
	<div class="product-container">
		<h4 class="product-title">
			<a href="<?php echo $arrProduct['product_url']; ?>" title="<?php echo $arrProduct['text_description']; ?>" target="_blank" rel="nofollow">
				<?php echo $arrProduct['title']; ?>
			</a>
		</h4>
		<div class="product-thumbnail" style="width:<?php echo $arrArgs['image_size']; ?>px;">
			<a href="<?php echo $arrProduct['product_url']; ?>" title="<?php echo $arrProduct['text_description']; ?>" target="_blank" rel="nofollow">
				<img src="<?php echo $arrProduct['thumbnail_url']; ?>" style="max-width:<?php echo $arrArgs['image_size'];?>px;" alt="<?php echo $arrProduct['text_description']; ?>" />
			</a>
		</div>
		<div class="product-description">
			<?php echo $arrProduct['description']; ?>
		</div>
	</div>
<?php endforeach; ?>	
</div>
