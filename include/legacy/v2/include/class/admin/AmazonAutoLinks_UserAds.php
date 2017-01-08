<?php
/**
 * Creates links for the user.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 */

if ( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['support']['ads'] ) :
    final class AmazonAutoLinks_UserAds extends AmazonAutoLinks_UserAds_ {}
else :
    final class AmazonAutoLinks_UserAds {
        public function __call( $strMethodName, $arrArgs ) {}
        public function __get( $strName ) {}
    }
endif;