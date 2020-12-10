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
 * Adds the 'Site Debug Log' admin page tab.
 * 
 * @since 4.4.4
 */
class AmazonAutoLinks_AdminPage_Tab_SiteDebugLog extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'         => 'site_debug_log',
            'title'            => __( 'Site Debug Log', 'amazon-auto-links' ),
            'order'            => 100,
            'show_in_page_tab' => $this->isDebugMode(),
            'style'            => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/site-debug-log.css',
        );
    }

    protected function _construct( $oAdminPage ) {}

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _loadTab( $oAdminPage ) {
        $oAdminPage->addSettingSection(
            array(
                'section_id'    => 'site_debug_log',
                'page_slug'     => $this->sPageSlug,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Site Debug Log', 'amazon-auto-links' ),
            )
        );
        $oAdminPage->addSettingFields(
            'site_debug_log',
            array(
                'page_slug' => $this->sPageSlug,
                'tab_slug'  => $this->sTabSlug,
                'field_id'  => '_textarea',
                'show_title_column' => false,
                'save'      => false,
                'type'      => 'textarea',
                'attributes' => array(
                    'readonly' => 'readonly'
                ),
                'value'     => file_exists( WP_CONTENT_DIR . '/debug.log' )
                    ? $this->___getTrailingFileContents( WP_CONTENT_DIR . '/debug.log', 1000 )
                    : 'No Log Found.',
            )
        );
    }
        /**
         * @param  string  $sFilePath
         * @param  integer $iLines
         * @return string
         */
        private function ___getTrailingFileContents( $sFilePath, $iLines ) {

            $_oSplFile = new SplFileObject( $sFilePath, 'r' );
            $_oSplFile->seek( PHP_INT_MAX );
            $_iLastLine = $_oSplFile->key();
            $_iOffset   = $_iLastLine - $iLines;
            if ( $_iOffset <= 0 ) {
                return file_get_contents( $sFilePath );
            }
            $_oIterator = new LimitIterator( $_oSplFile, $_iOffset, $_iLastLine );
            return implode( '', iterator_to_array( $_oIterator ) );

        }        

        // @deprecated 4.4.4
        // private function ___getHeadingFileContents( $sFilePath, $iLines ) {
        //     $_sOutput = '';
        //     $_rFile = fopen( $sFilePath, 'r' );
        //     for ( $i = 0; $i < $iLines; $i++ ) {
        //         if ( feof( $_rFile ) ) {
        //             break; // 'EOF reached';
        //         }
        //         $_sOutput .= fgets( $_rFile );
        //     }
        //     fclose( $_rFile );
        //     return $_sOutput;
        // }

}