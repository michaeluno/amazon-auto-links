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
 * Displays the stored unit option values.
 */
class AmazonAutoLinks_UnitPostMetaBox_DebugInfo extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Checks whether the meta box should be registered or not in the loading page.
     */
    protected function _isInThePage() {

        if ( ! parent::_isInThePage() ) {
            return false;
        }
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return $_oOption->isDebug( 'back_end' );
        
    }
    
    public function content( $sOutput ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return $sOutput 
            . "<h4>Unit Options</h4>"
            . $this->oUtil->getTableOfArray(
                $this->oUtil->getAsArray( AmazonAutoLinks_WPUtility::getPostMeta( $GLOBALS[ 'post' ]->ID, '', $_oOption->get( 'unit_default' ) ) ),
                array(
                    'table' => array(
                        'class' => 'widefat striped fixed product-details',
                    ),
                    'td'    => array(
                        array( 'class' => 'width-one-fifth', ),  // first td
                    )
                )
            );
    }
    
}