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
 * Handles filtering products in unit outputs.
 * 
 * @since       3
 */
class AmazonAutoLinks_ProductFilter extends AmazonAutoLinks_WPUtility {

    /**
     * Stores parsed ASINs for the global ASIN filter.
     */
    static public $aParsed = array();

    /* The structure of the product filter argument
        'black_list'     => array(
            'asin'        => '',
            'title'       => '',
            'description' => '',
        ),
        'white_list'        => array(
            'asin'        => '',
            'title'       => '',
            'description' => '',
        ),
        'case_sensitive' => 0,
        'no_duplicate'   => 0,    // in 2.0.5.1 changed to 0 from 1.
    */
    public $bCaseSensitive         = false;
    public $bNoDuplicate           = false;
    public $aBlackListASINs        = array();
    public $aBlackListTitles       = array();
    public $aBlackListDescriptions = array();
    public $aWhiteListASINs        = array();
    public $aWhiteListTitles       = array();
    public $aWhiteListDescriptions = array();
    
    // 3.2.1
    /**
     * Restrict results to certain ASINs.
     * 
     * The difference from the white list by ASIN is that an item of whilte list will be allowed even when it is in a black list.
     * On the other hand, items not listed here will not be allowed.
     *      
     * Used by the URL unit type.
     * @since       3.2.1
     */
    public $aAllowedASINs          = array();
    
    /**
     * Indicates whether allowed ASINs are set. 
     * If this value is true, prodcuts not listed here will not be returned.
     * @since       3.2.1
     */
    public $bASINRestrictced       = false;
    
    /**
     * Sets up properties.
     */
    public function __construct( array $aArguments=array() ) {
        
        $this->bCaseSensitive   = $this->getElement(
            $aArguments,    // subject array
            'case_sensitive',   // dimensional key(s)
            false  // default
        );
        
        $this->bNoDuplicate     = $this->getElement(
            $aArguments, // subject array
            'no_duplicate', // dimensional key(s)
            false   // default
        );
        
        $this->aBlackListASINs  = $this->_getCriteriaFromCommaDelimitedString(
            $aArguments,
            array( 'black_list', 'asin' )
        );
        $this->aBlackListTitles = $this->_getCriteriaFromCommaDelimitedString(
            $aArguments,
            array( 'black_list', 'title' )
        );
        $this->aBlackListDescriptions = $this->_getCriteriaFromCommaDelimitedString(
            $aArguments,
            array( 'black_list', 'description' )
        );
        $this->aWhiteListASINs        = $this->_getCriteriaFromCommaDelimitedString(
            $aArguments,
            array( 'white_list', 'asin' )
        );
        $this->aWhiteListTitles        = $this->_getCriteriaFromCommaDelimitedString(
            $aArguments,
            array( 'white_list', 'title' )
        );        
        $this->aWhiteListDescriptions  = $this->_getCriteriaFromCommaDelimitedString(
            $aArguments,
            array( 'white_list', 'description' )
        );                
        
        // 3.2.1
        $this->aAllowedASINs           = $this->getElementAsArray(
            $aArguments,
            '_allowed_ASINs'
        );
        $this->bASINRestrictced        = ! empty( $this->aAllowedASINs );
        $this->aAllowedASINs           = array_unique(
            array_merge(
                $this->aAllowedASINs,
                $this->aWhiteListASINs
            )
        );
        
    }
        /**
         * @return      array
         */
        private function _getCriteriaFromCommaDelimitedString( $aSubject, array $aDimensionalKeys ) {
           
            $_sList = $this->getElement(
                $aSubject,
                $aDimensionalKeys,
                '' 
            );
            $_sList = str_replace(
                PHP_EOL,
                ',',
                $_sList
            );
            $_aList = $this->getStringIntoArray( $_sList, ',' );

            return $_aList;
            
        }
        
    /**
     * Marks a given ASIN as a parsed item.
     * 
     * Used to prevent duplicate items in a page.
     */
    public function markParsed( $sASIN ) {
        if ( ! in_array( $sASIN, self::$aParsed ) ) {
            self::$aParsed[] = $sASIN;
        }      
        $this->aBlackListASINs[] = $sASIN;
    }
    // public function addToGlobalWhiteList( $sASIN ) {}
    
    /**
     * @return      boolean
     */
    public function isASINAllowed( $sASIN ) {
        if ( $this->bASINRestrictced ) {
            return in_array(
                $sASIN,
                $this->aAllowedASINs
            );
        }
        return in_array(
            $sASIN,
            $this->aWhiteListASINs
        );
    }
    public function isASINBlocked( $sASIN ) {
        if ( $this->bASINRestrictced ) {
            return ! in_array(
                $sASIN,
                $this->aAllowedASINs
            );
        }        
        if ( $this->bNoDuplicate ) {
            if ( in_array( $sASIN, self::$aParsed ) ) {
                return true;
            }
        }
        return in_array( $sASIN, $this->aBlackListASINs );
    }
    public function isTitleAllowed( $sTitle ) {
        return $this->_isListed( 
            $sTitle, 
            $this->aWhiteListTitles
        );
    }
    public function isTitleBlocked( $sTitle ) {
        return $this->_isListed( 
            $sTitle, 
            $this->aBlackListTitles
        );          
    }     
    public function isDescriptionAllowed( $sDescription ) {
        return $this->_isListed( 
            $sDescription, 
            $this->aWhiteListDescriptions
        );                  
    }
    public function isDescriptionBlocked( $sDescription ) {
        return $this->_isListed( 
            $sDescription, 
            $this->aBlackListDescriptions
        );                  
    }
        /**
         * Checks whether the given string is matches the sub-strings of the given list
         * 
         * @return      true
         */
        protected function _isListed( $sSubject, array $aList ) {
            
            $_sFunctionName = $this->bCaseSensitive 
                ? 'strpos' 
                : 'stripos';
            
            foreach( $aList as $_sNeedle ) {            
                $_biFoundPosition = call_user_func_array( 
                    $_sFunctionName, // strpos or stripos
                    array( $sSubject, $_sNeedle )
                );
                // If found, return true
                if ( false !== $_biFoundPosition ) {
                    return true;
                }
            }                   
            return false;
            
        }
        
}