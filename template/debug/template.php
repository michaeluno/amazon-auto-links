<?php
/*
 * Available variables:
 * 
 * $aOptions - the plugin options
 * $aProducts - the fetched product links
 * $aArgs - the user defined arguments such as image size and count etc.
 */

$_aScopeVariables = array();
$_aToDump         = array();
foreach( get_defined_vars() as $_sKey => $_v ) {
    // Drop variables used in this file.
    if ( in_array( $_sKey, array( '_aScopeVariables', '_aToDump' ) ) ) {
        continue;
    }
    // Drop variables kept for backward-compatibility starting with $arr.
    if ( 'arr' === substr( $_sKey, 0, 3 ) ) {
        continue;
    }
    $_aScopeVariables[ '$' . $_sKey ] = gettype( $_v );
    $_aToDump[ '$' . $_sKey ] = $_v;
}
echo "<h3>Available Variables</h3>";
AmazonAutoLinks_Debug::dump( $_aScopeVariables );
echo "<div class='amazon-auto-links-debug'>";
    foreach( $_aToDump as $_sVariableName => $_v ) {
        echo "<h3>{$_sVariableName}</h3>";
        AmazonAutoLinks_Debug::dump( $_v );
    }
echo "</div>";