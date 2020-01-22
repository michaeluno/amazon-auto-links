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
 * Adds the 'Get Templates' tab to the 'Template' admin page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Template_GetNew extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        load_{page_slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        add_filter(
            'style_' . $oFactory->oProp->sClassName,
            array( $this, 'getCSS' )
        );
    }
        /**
         * @return      string
         */
        public function getCSS( $sCSS ) {
            $_oColumn = new AmazonAutoLinks_Column(
                array(), // data
                3,  // number of columns
                'amazon_auto_links_' // selector prefix
            );
            return $sCSS
                . $_oColumn->getCSS();
            
        }

    /**
     * 
     * @callback        do_{page_slug}_{tab slug}
     */
    public function replyToDoTab( $oFactory ) {
        
        $_oRSS = new AmazonAutoLinks_RSSClient(
            'http://feeds.feedburner.com/AmazonAutoLinksTemplates'
        );

        echo "<h3>" 
                . __( 'Templates', 'amazon-auto-links' ) 
            . "</h3>";
        echo "<p>" 
                . sprintf( 
                    __( 'Want your template to be listed here? Send the file to %1$s.', 'amazon-auto-links' ), 
                    'wpplugins@michaeluno.jp' 
                 ) 
            . "</p>";
        
        $_aItems = $_oRSS->get();
        if ( empty( $_aItems ) ) {
            echo "<p>" 
                    . __( 'No extension has been found.', 'amazon-auto-links' ) 
                . "</p>";
            return;
        }

        // Format the description element.
        foreach( $_aItems as &$_aItem ) {
            $_aItem = array(
                'description' => $this->_getFormattedDescription( $_aItem ),            
            ) + $_aItem;
        }
        
        // Get the column output.
        $_oColumn = new AmazonAutoLinks_Column(
            $_aItems, // data
            3,  // number of columns
            'amazon_auto_links_' // selector prefix
        );
        echo $_oColumn->get();
        
    }   

        /**
         * @return      string
         */
        private function _getFormattedDescription( $aItem ) {
            $_aAttributes = array(
                'href'      => $aItem[ 'link' ],
                'rel'       => 'nofollow',
                'class'     => 'button button-secondary',
                'target'    => '_blank',
                'title'     => esc_attr( __( 'Get it Now', 'amazon-auto-links' ) ),
            );
            return "<h4>" . $aItem[ 'title' ] . "</h4>"
                . $aItem[ 'description' ] 
                . "<div class='get-now'>"
                    . "<a " . AmazonAutoLinks_WPUtility::generateAttributes( $_aAttributes ) . ">"
                        . __( 'Get it Now', 'amazon-auto-links' )
                    . "</a>"
               . "</div>";
        }
        
}