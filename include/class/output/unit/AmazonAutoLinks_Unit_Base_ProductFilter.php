<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * A one of the base classes for unit classes.
 * 
 * Provides shared methods and properties relating product filters (blacklist and whilte list)
 * @since       3
 */
abstract class AmazonAutoLinks_Unit_Base_ProductFilter extends AmazonAutoLinks_Unit_Base_CustomDBTable {
    
    /**
     * @return      boolean
     */
    protected function isASINBlocked( $sASIN ) {            
        if ( ! trim( $sASIN ) ) {
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
     */
    protected function isTitleBlocked( $sTitle ) {
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

}