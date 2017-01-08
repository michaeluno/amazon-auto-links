<?php
function AmazonAutoLinks( $aArgs ) {
    return AmazonAutoLinks_Units::getInstance( $aArgs )->render();    
}