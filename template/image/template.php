<?php
/*
 * Available variables:
 * 
 * $aOptions   - the plugin options
 * $aProducts  - the fetched product links
 * $aArguments - the user defined arguments such as image size and count etc.
 */

new AmazonAutoLinks_TemplateUtility_NoOuterContainer;

foreach( $aProducts as $_aProduct ) : ?>
    <?php echo $_aProduct[ 'formatted_item' ]; ?>
<?php
endforeach;