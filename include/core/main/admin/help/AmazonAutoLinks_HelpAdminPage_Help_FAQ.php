<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Adds an in-page tab of `FAQ` to the `Help` admin page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_ReadMeBase
 */
class AmazonAutoLinks_HelpAdminPage_Help_FAQ extends AmazonAutoLinks_AdminPage_Tab_ReadMeBase {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'faq',
            'title'     => __( 'FAQ', 'amazon-auto-links' ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oAdminPage ) {
            
        $_aItems     = $this->getContentsByHeader( $this->_getReadmeContentsBySection(), 4 );
        $_iLastIndex = count( $_aItems ) - 1;
        foreach( $_aItems as $_iIndex => $_aContent ) {

            $_oParser   = new AmazonAutoLinks_AdminPageFramework_WPReadmeParser( $_aContent[ 1 ] );
            $_sContent  = $_oParser->get();
            $oAdminPage->addSettingSections(    
                $this->sPageSlug, // the target page slug  
                array(
                    'section_id'        => 'tips_' . $_iIndex,
                    'title'             => $_aContent[ 0 ],
                    'collapsible'       => array(
                        'toggle_all_button' => $_iLastIndex === $_iIndex 
                            ? array( 'bottom-right' )
                            : ( 0 === $_iIndex
                                ? array( 'top-right' )
                                : false
                            ),
                    ),
                    'content'           => $_sContent,
                            
                )
            );              
            
        }        

    }
        /**
         * @return      string
         */
        protected function _getReadmeContentsBySection()  {
            return $this->_getReadmeContents( 
                AmazonAutoLinks_Registry::$sDirPath . '/readme.txt',    // source path
                '', // TOC title
                array( 'Frequently asked questions' )  // sections
            );
        }               
            
}
