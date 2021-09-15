<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the `Tools` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_ToolAdminPage_Tool extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ],
            'title'     => __( 'Tools', 'amazon-auto-links' ),
            'order'     => 900,
            'style'     => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',  // not sure why but the debug table seems to be fine without this
            'script'    => array(
                array(
                    'src'           => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/accordion.js',
                    'dependencies'  => array( 'jquery', 'jquery-ui-accordion', ),
                    'in_footer'     => true,
                ),
            ),
        );
    }

    /**
     * 
     * @callback        action      load_{page slug}
     */    
    public function replyToLoadPage( $oFactory ) {
        $this->___doPageSettings( $oFactory );
    }
        /**
         * Page styling
         * @since       3
         * @return      void
         */
        private function ___doPageSettings( $oFactory ) {
            $oFactory->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $oFactory->setInPageTabTag( 'h2' );
        }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @since 4.7.5
     */
    protected function _doPage( $oFactory ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug( 'back_end' ) ) {
            return;
        }
        $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
        echo "<div class='aal-accordion'>"
            . "<h4>Debug - Tool Options</h4>"
                . "<div>"
                    . $this->getTableOfArray(
                        $_oToolOption->get(),
                        array(
                            'table' => array(
                                'class' => 'widefat striped fixed',
                            ),
                            'td'    => array(
                                array( 'class' => 'width-one-fifth', ),  // first td
                            )
                        )
                    )
                . "</div>"
            . "</div>";

    }
        
}