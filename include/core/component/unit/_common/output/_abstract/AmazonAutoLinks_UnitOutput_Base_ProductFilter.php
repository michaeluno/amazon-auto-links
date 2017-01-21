<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * One of the base classes for unit classes.
 * 
 * Provides shared methods and properties relating product filters (blacklist and whilte list)
 * @since       3
 */
abstract class AmazonAutoLinks_UnitOutput_Base_ProductFilter extends AmazonAutoLinks_UnitOutput_Base {
    
    /**
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

    protected function isDescriptionBlocked( $sDescription ) {
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

    protected function setParsedASIN( $sASIN ) {
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