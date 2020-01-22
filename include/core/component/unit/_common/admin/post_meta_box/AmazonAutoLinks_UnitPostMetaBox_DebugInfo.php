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
        return $_oOption->isDebug();
        
    }
    
    public function content( $sOutput ) {        
        return $sOutput 
            . "<h4>" . __( 'Unit Options', 'amazon-auto-links' ) . "</h4>"
            . $this->oDebug->get(
                AmazonAutoLinks_WPUtility::getPostMeta( $GLOBALS['post']->ID )
            );
    }
    
}