<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * One of the base classes for unit classes.
 * 
 * Provides shared methods and properties relating product filters (blacklist and white list)
 * @since       3
 */
abstract class AmazonAutoLinks_UnitOutput_Base_ProductFilter extends AmazonAutoLinks_UnitOutput_Base {

    /**
     * @param  string  $sASIN
     * @return boolean
     * @since  4.4.0   Added in order to be used with `array_filter()`.
     */
    public function isASINAllowed( $sASIN ) {
        return ! $this->isASINBlocked( $sASIN );
    }

    /**
     * @param  string $sASIN
     * @param  string $sTitle
     * @param  string $sDescription
     * @return boolean
     * @sine   4.6.14
     */
    public function isWhiteListed( $sASIN, $sTitle, $sDescription ) {
        if ( $this->isASINWhiteListed( $sASIN ) ) {
            return true;
        }
        if ( $this->isTitleWhiteListed( $sTitle ) ) {
            return true;
        }
        if ( $this->isDescriptionWhiteListed( $sDescription ) ) {
            return true;
        }
        return false;
    }

    /**
     * @param  string $sDescription
     * @return boolean
     * @since  4.6.14
     */
    public function isDescriptionWhiteListed( $sDescription ) {
        if ( $this->oUnitProductFilter->isDescriptionAllowed( $sDescription ) ) {
            return true;
        }
        if ( $this->oGlobalProductFilter->isDescriptionAllowed( $sDescription ) ) {
            return true;
        }
        return false;        
    }    

    /**
     * @param  string $sTitle
     * @return boolean
     * @since  4.6.14
     */
    public function isTitleWhiteListed( $sTitle ) {
        if ( $this->oUnitProductFilter->isTitleAllowed( $sTitle ) ) {
            return true;
        }
        if ( $this->oGlobalProductFilter->isTitleAllowed( $sTitle ) ) {
            return true;
        }
        return false;        
    }
    
    /**
     * @remark The difference against isASINAllowed() is this only check wither the ASIN is white listed
     * as opposed to isASINAllowed() which return true if the item is neither whitelisted nor blacklisted.
     * @param  string  $sASIN
     * @return boolean
     * @since  4.6.14
     */
    public function isASINWhiteListed( $sASIN ) {
        if ( $this->oUnitProductFilter->isASINAllowed( $sASIN ) ) {
            return true;
        }
        if ( $this->oGlobalProductFilter->isASINAllowed( $sASIN ) ) {
            return true;
        }
        return false;
    }

    /**
     * @param       string  $sASIN
     * @return      boolean
     * @since       3
     * @since       3.5.0       Changed the scope from protected.
     * @remark      Accessed outside from the similarity products formatter class.
     */
    public function isASINBlocked( $sASIN ) {
        $sASIN = trim( $sASIN );
        if ( ! $sASIN ) {
            return true;
        }
        if ( $this->oUnitProductFilter->isASINAllowed( $sASIN ) ) {
            return false;
        }
        if ( $this->oGlobalProductFilter->isASINAllowed( $sASIN ) ) {
            return false;
        }            
        if ( $this->oUnitProductFilter->isASINBlocked( $sASIN ) ) {
            return true;
        }
        if ( $this->oGlobalProductFilter->isASINBlocked( $sASIN ) ) {
            return true;
        }
        return false;
    }    
    /**
     * @param       string      $sTitle
     * @return      boolean
     * @since       3
     * @since       3.5.0       Changed the scope from protected.
     * @remark      Accessed outside from the similarity products formatter class.
     */
    public function isTitleBlocked( $sTitle ) {
        if ( $this->oUnitProductFilter->isTitleAllowed( $sTitle ) ) {
            return false;
        }
        if ( $this->oGlobalProductFilter->isTitleAllowed( $sTitle ) ) {
            return false;
        }            
        if ( $this->oUnitProductFilter->isTitleBlocked( $sTitle ) ) {
            return true;
        }
        if ( $this->oGlobalProductFilter->isTitleBlocked( $sTitle ) ) {
            return true;
        }
        return false;        
    }

    /**
     * @param  string $sDescription
     * @return boolean
     * @since  ?
     * @since  5.0.0   Changed the visibility scope to public from protected as the products-formatter classes access this.
     */
    public function isDescriptionBlocked( $sDescription ) {
        if ( $this->oUnitProductFilter->isDescriptionAllowed( $sDescription ) ) {
            return false;
        }
        if ( $this->oGlobalProductFilter->isDescriptionAllowed( $sDescription ) ) {
            return false;
        }            
        if ( $this->oUnitProductFilter->isDescriptionBlocked( $sDescription ) ) {
            return true;
        }
        if ( $this->oGlobalProductFilter->isDescriptionBlocked( $sDescription ) ) {
            return true;
        }
        return false;                
    }

    /**
     * @param string $sASIN
     * @since ?
     * @since 5.0.0 Changed the visibility scope to public from protected as formetter classes access this.
     */
    public function setParsedASIN( $sASIN ) {
        $this->oUnitProductFilter->markParsed( $sASIN );
        $this->oGlobalProductFilter->markParsed( $sASIN );        
    }

    /**
     * 
     * @since       3.1.0
     * @since       3.5.0       Renamed from `_isNoImageAllowed()`.
     * @since       3.5.0       Changed the visibility scope to public.
     * @remark      the similarity product formatter class access it.
     * @return      boolean     True if the passed image is okay to display; otherwise, false.
     * @param       string      $sImageURL      The image url to check.
     */
    public function isImageAllowed( $sImageURL ) {
        
        if ( ! $this->oUnitOption->get( 'product_filters', 'skip_no_image' ) ) {
            return true;
        }
        
        // At this point, the user wants to skip no image items.
        return ! $this->___isNoImage( $sImageURL );
                
    }
        /**
         * Checks if the given image is an alternative image for no-image.
         *
         * @since       3.1.0
         * @since       3.5.0       Changed the visibility scope from protected.
         * @param       string      $sImageURL
         * @return      boolean
         * @todo research what else image urls represent no-image.
         */
        private function ___isNoImage( $sImageURL ) {

            if ( empty( $sImageURL ) ) {
                return true;
            }
            if ( false !== strpos( $sImageURL, '/no-img' ) ) {
               return true;
            }
            return false;

        }

}