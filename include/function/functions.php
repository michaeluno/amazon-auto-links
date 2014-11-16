<?php
function AmazonAutoLinks( $arrArgs ) {
	
	$oUnits = new AmazonAutoLinks_Units( $arrArgs );
	return $oUnits->render();
	
}