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
 * Adds the 'Get Templates' tab to the 'Template' admin page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Template_GetNew extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @since  4.6.17
     * @return array
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'tab_slug'  => 'get',
            'title'     => __( 'Get New', 'amazon-auto-links' ),
            'style'     => AmazonAutoLinks_TemplateLoader::$sDirPath . '/asset/css/get-new.css',
            'script'    => array(
                array(
                    'src'           => AmazonAutoLinks_TemplateLoader::$sDirPath . '/asset/js/get-new-templates.js',
                    'dependencies'  => array( 'jquery', 'wp-pointer' ),
                    'in_footer'     => true,
                    'handle_id'     => 'aalNewTemplates',
                    'translation'   => array(
                        'ajaxURL'          => admin_url( 'admin-ajax.php' ),
                        'actionHookSuffix' => 'aal_action_get_new_templates',
                        'nonce'            => wp_create_nonce( 'aal_action_get_new_templates' ),
                        'spinnerURL'       => admin_url( 'images/loading.gif' ),
                        'pluginName'       => AmazonAutoLinks_Registry::NAME,
                        'debugMode'        => $_oOption->isDebug() || $this->isDebugMode(),
                        'labels'           => array(
                            'error' => __( 'Something went wrong.', 'amazon-auto-links' ),
                        ),
                    ),
                ),
            ),
        );
    }

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

        $_bAllowed = ( boolean ) get_user_meta( get_current_user_id(), 'aal_load_new_templates', true );
        echo "<h3>" 
                . __( 'Templates', 'amazon-auto-links' ) 
            . "</h3>";
        echo "<p>" 
                . sprintf( 
                    __( 'Want your template to be listed here? Send the file to %1$s.', 'amazon-auto-links' ), 
                    'wpplugins@michaeluno.jp' 
                 ) 
            . "</p>";
        $_sClassLoad   = $_bAllowed ? 'load-button hidden' : 'load-button button button-hero';
        echo "<div class='template-list'>"
                . "<div class='button-container do-not-load " . ( $_bAllowed ? '' : 'hidden' ) . "'>"
                    . "<span class='do-not-load-button button button-small'>"
                        . __( 'Do not load automatically', 'amazon-auto-links' )
                    . "</span>"
                . "</div>"
                . "<div class='align-center button-container has-tooltip load'>"
                    . "<span class='{$_sClassLoad}' data-allowed='" . esc_attr( $_bAllowed ) . "'>"
                        . "<strong>"
                            . __( 'Load', 'amazon-auto-links' )
                        . "</strong>"
                    . "</span>"
                    . "<p class='tooltip-content'>"
                        . "<span>" . __( 'Click the button to load available templates!', 'amazon-auto-links' ) . "</span>"
                        . " <span>" . sprintf( __( 'This will access <code>%1$s</code> to retrieve data.', 'amazon-auto-links' ), 'feeds.feedburner.com' ). "</span>"
                    . "</p>"
                . "</div>"
            . "</div>";
        echo "<div>"

            ."</div>";

    }   

}