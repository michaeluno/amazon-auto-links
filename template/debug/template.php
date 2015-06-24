<?php
/*
 * Available variables:
 * 
 * $arrOptions - the plugin options
 * $arrProducts - the fetched product links
 * $arrArgs - the user defined arguments such as image size and count etc.
 */

// echo AmazonAutoLinks_Debug::get( $arrArgs );
// echo AmazonAutoLinks_Debug::get( $arrOptions );
// echo AmazonAutoLinks_Debug::get( $arrProducts );
echo "<div class='amazon-auto-links-debug'>";
var_dump( $arrArgs );
var_dump( $arrProducts );
echo "</div>";