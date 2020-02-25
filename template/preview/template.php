<?php
/*
 * Available variables:
 * 
 * $aOptions   - the plugin options
 * $aProducts  - the fetched product links
 * $aArguments - the user defined unit arguments such as image size and count etc.
 */

?>
<?php if ( empty( $aProducts ) ) : ?>
    <div><p><?php _e( 'No products found.', 'amazon-auto-links' ); ?></p></div>
<?php endif; ?>    
<?php if ( isset( $aProducts[ 'Error' ][ 'Message' ], $aProducts[ 'Error' ][ 'Code' ] ) ) : ?>
    <div class="error">
        <p>
            <?php echo $aProducts[ 'Error' ][ 'Code' ] . ': '. $aProducts[ 'Error' ][ 'Message' ]; ?>
        </p>
    </div>
<?php return; ?>
<?php endif; ?>

<div class="products-container">
<?php foreach( $aProducts as $_aProduct ) : ?>
    
    <div class="product-container">
        <h4 class="product-title">
            <a href="<?php echo esc_url( $_aProduct[ 'product_url' ] ); ?>" title="<?php echo $_aProduct[ 'text_description' ]; ?>" target="_blank" rel="nofollow">
                <?php echo $_aProduct[ 'title' ]; ?>
            </a>
        </h4>
        <div class="product-thumbnail" style="width:<?php echo $aArguments['image_size']; ?>px;">
            <a href="<?php echo esc_url( $_aProduct[ 'product_url' ] ); ?>" title="<?php echo $_aProduct['text_description']; ?>" target="_blank" rel="nofollow">
                <img src="<?php echo $_aProduct[ 'thumbnail_url' ]; ?>" style="max-width:<?php echo $aArguments['image_size'];?>px;" alt="<?php echo $_aProduct[ 'text_description' ]; ?>" />
            </a>
        </div>
        <div class="product-description">
            <?php echo $_aProduct[ 'formatted_rating' ]; ?>
            <?php echo $_aProduct[ 'description' ]; ?>
        </div>
    </div>
<?php endforeach; ?>    
</div>
