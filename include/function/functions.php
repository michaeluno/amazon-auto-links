<?php
function AmazonAutoLinks( $aArgs ) {
    
    return AmazonAutoLinks_Units::getInstance( $aArgs )->render();
    // $_oUnits = new AmazonAutoLinks_Units( $aArgs );
    // return $_oUnits->render();
    
}