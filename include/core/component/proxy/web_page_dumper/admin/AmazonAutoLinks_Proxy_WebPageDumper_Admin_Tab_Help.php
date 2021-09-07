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
 * Adds an in-page tab to a setting page.
 * 
 * @since       4.5.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Admin_Tab_Help extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return  array
     * @since   4.2.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'         => 'web_page_dumper_help',
            'title'            => __( 'Web Page Dumper Help', 'amazon-auto-links' ),
            'order'            => 30,
            'parent_tab_slug'  => 'proxy',
            'show_in_page_tab' => false,
        );
    }

    protected function _construct( $oFactory ) {
        if ( $oFactory->oProp->getCurrentTab() !== $this->sTabSlug ) {
            return;
        }
        add_action(
            "do_{$this->sPageSlug}",
            array( $this, 'replyToDoTabEarly' ),
            5
        );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    public function replyToDoTabEarly( $oFactory ) {
        echo $this->___getGoBackButton();
        $this->___printCreateWebPageDumper();
    }
        private function ___getGoBackButton() {
            return "<div class='go-back'>"
                    . "<span class='dashicons dashicons-arrow-left-alt small-icon'></span>"
                    . "<a href='" . esc_url( add_query_arg( array( 'tab' => 'proxy' ) ) ) . "'>"
                        . esc_html__( 'Go Back', 'amazon-auto-links' )
                    . "</a>"
                . "</div>";
        }
        private function ___printCreateWebPageDumper() {

            echo "<span id='create-own-web-page-dumper'></span>";
            echo "<h3>" . esc_html__( 'Create Your Own Web Page Dumper', 'amazon-auto-links' ) . "</h3>";

            $_sURLImageCreateNewApp   = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/create-new-app.png' ) );
            $_sURLImageOpenApp        = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/open-app.png' ) );
            $_sURLImageManageApp      = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/manage-app.png' ) );
            $_sURLImageCopyAppAddress = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/copy-app-address.png' ) );
            echo "<p class='description'>" . __( 'To create your own Web Page Dumper server, follow the below steps.', 'amazon-auto-links' ) . "</p>"
                . "<ol>"
                    . "<li><p>" . sprintf( __( 'If you don\'t have a Heroku account, create one from <a href="%1$s" target="_blank">here</a>.', 'amazon-auto-links' ), esc_url( 'https://signup.heroku.com' ) ) . "</p></li>"
                    . "<li><p>" . sprintf( __( 'Log in to <a href="%1$s" target="_blank">Heroku</a>.', 'amazon-auto-links' ), esc_url( 'https://id.heroku.com/login' ) ) . "</p></li>"
                    . "<li><p>"
                        . sprintf(
                            __( 'Go to %1$s.', 'amazon-auto-links' ),
                            '<a href="' . esc_url( 'https://www.heroku.com/deploy/?template=https://github.com/michaeluno/web-page-dumper' ) . '" target="_blank"><strong>Deploy</strong></a>'
                        )
                    . "</p></li>"
                    . "<li>"
                        . "<p>" .sprintf( __( 'You will be prompted to deploy the app. Enter your desired app name, for example, <code>%1$s</code>, and press <code>%2$s</code>.', 'amazon-auto-links' ), 'web-page-dumper-789', 'Deploy app' ) . "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageCreateNewApp}' alt='Create New App' /></div>"
                    . "</li>"
                    . "<li>"
                        . "<p>" . sprintf( __( 'Click on the <code>%1$s</code> button and from the <code>%2$s</code> link, go to the front page of Web Page Dumper.', 'amazon-auto-links' ), 'Manage app', 'Open app' ). "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageManageApp}' alt='Manage App' /></div>"
                        . "<div class='screenshot'><img src='{$_sURLImageOpenApp}' alt='Open App' /></div>"
                    . "</li>"
                    . "<li>"
                        . "<p>" . __( 'Copy the app address and enter it in the option.', 'amazon-auto-links' ) . "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageCopyAppAddress}' alt='Copy App Address' /></div>"
                    . "</li>"
                . "</ol>";
        }
}
