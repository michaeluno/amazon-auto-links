<?php
/*
 * Available variables:
 * 
 * $arrOptions - the plugin options
 * $arrProducts - the fetched product links
 * $arrArgs - the user defined arguments such as image size and count etc.
 */

// echo AmazonAutoLinks_Debug::get( $aArguments );
// echo AmazonAutoLinks_Debug::get( $aOptions );
// echo AmazonAutoLinks_Debug::get( $aProducts );
echo "<div class='amazon-auto-links-debug'>";
echo "<h3>Arguments</h3>";
var_dump( $aArguments );
echo "<h3>Products</h3>";
var_dump( $aProducts );
echo "</div>";