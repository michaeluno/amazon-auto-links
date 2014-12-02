<?php
function AmazonAutoLinks( $aArgs ) {
    
    $_oUnits = new AmazonAutoLinks_Units( $aArgs );
    return $_oUnits->render();
    
}