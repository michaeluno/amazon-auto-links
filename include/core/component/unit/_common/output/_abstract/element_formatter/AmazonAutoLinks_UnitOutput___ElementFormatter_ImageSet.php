<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * A class that provides methods to format image set outputs.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return      string
     * @throws      Exception
     * @since       3.5.0
     */
    public function get() {

        // 3.8.11 For search-type units, this value is already set with API response.
        // 4.0.0 Also the feed units have the value already
        if ( isset( $this->_aProduct[ 'image_set' ] ) ) {
            return $this->_aProduct[ 'image_set' ];
        }

        $_anImages = $this->_getCell( 'images' );
        if ( null === $_anImages ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving an image set.', 'amazon-auto-links' ),
                $this->_sLocale
            );
        }
        return AmazonAutoLinks_Unit_Utility::getSubImages(
            $this->getAsArray( $_anImages ), // at this point it is an array
            $this->_aProduct[ 'product_url' ],
            $this->_aProduct[ 'formatted_title' ],
            $this->_oUnitOption->get( 'subimage_size' ),
            $this->_oUnitOption->get( 'subimage_max_count' )
        );

    }

}