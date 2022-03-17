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
                                  /* translators: 1: a proper noun (Web Page Dumper) */
            'title'            => sprintf( __( '%1$s Help', 'amazon-auto-links' ), 'Web Page Dumper' ),
            'order'            => 30,
            'parent_tab_slug'  => 'proxy',
            'show_in_page_tab' => false,
        );
    }

    protected function _construct( $oFactory ) {
        if ( $oFactory->oProp->getCurrentTabSlug() !== $this->sTabSlug ) {
            return;
        }
        add_action( "do_{$this->sPageSlug}", array( $this, 'replyToDoTabEarly' ), 5 );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    public function replyToDoTabEarly( $oFactory ) {
        echo $this->___getGoBackButton();
        $this->___printCreateWebPageDumper();
        $this->___printUpdateWebPageDumper();
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
            echo "<span id='creating-own-web-page-dumper'></span>";
                          /* translators: 1: a proper noun (Web Page Dumper) */
            echo "<h3>" . esc_html( sprintf( __( 'Creating Your Own %1$s', 'amazon-auto-links' ), 'Web Page Dumper' ) ) . "</h3>";
            $_sURLImageCreateNewApp   = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/create/create-new-app.png' ) );
            $_sURLImageOpenApp        = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/create/open-app.png' ) );
            $_sURLImageManageApp      = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/create/manage-app.png' ) );
            $_sURLImageCopyAppAddress = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/create/copy-app-address.png' ) );
                                             /* translators: 1: a proper noun (Web Page Dumper) */
            echo "<p class='description'>" . sprintf( __( 'To create your own %1$s server, follow the below steps.', 'amazon-auto-links' ), 'Web Page Dumper' ) . "</p>"
                . "<ol>"
                    . "<li><p>" . sprintf( __( 'If you don\'t have a Heroku account, create one from <a href="%1$s" target="_blank">here</a>.', 'amazon-auto-links' ), esc_url( 'https://signup.heroku.com' ) ) . "</p></li>"
                    . "<li><p>" . sprintf( __( 'Log in to <a href="%1$s" target="_blank">Heroku</a>.', 'amazon-auto-links' ), esc_url( 'https://id.heroku.com/login' ) ) . "</p></li>"
                    . "<li><p>"
                          /* translators: 1: a link to the Web Page Dumper deployment page on Heroku */
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
                                  /* translators: 1: a fixed UI label (Manage app) 2:  a fixed UI label (Open app) 3: a proper noun (Web Page Dumper) */
                        . "<p>" . sprintf( __( 'Click on the <code>%1$s</code> button and from the <code>%2$s</code> link, go to the front page of %3$s.', 'amazon-auto-links' ), 'Manage app', 'Open app', 'Web Page Dumper' ). "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageManageApp}' alt='Manage App' /></div>"
                        . "<div class='screenshot'><img src='{$_sURLImageOpenApp}' alt='Open App' /></div>"
                    . "</li>"
                    . "<li>"
                        . "<p>" . __( 'Copy the app address and enter it in the option.', 'amazon-auto-links' ) . "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageCopyAppAddress}' alt='Copy App Address' /></div>"
                    . "</li>"
                . "</ol>";
        }
        private function ___printUpdateWebPageDumper() {
            echo "<span id='updating-web-page-dumper'></span>";
                                    /* translators: 1: a proper noun (Web Page Dumper) */
            echo "<h3>" . esc_html( sprintf( __( 'Updating %1$s', 'amazon-auto-links' ), 'Web Page Dumper' ) ) . "</h3>";
            $_sURLImageForkRepo     = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/update/github-fork-button.jpg' ) );
            $_sURLImageFetchStream  = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/update/github-update-forked-repository.jpg' ) );
            $_sURLImageHerokuDeploy = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/update/heroku-sync-repository.jpg' ) );
            $_sURLImageAutoDeploy   = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/update/heroku-enable-automatic-deploys.jpg' ) );
            $_sURLImageDeployBranch = esc_url( $this->getResolvedSRC( AmazonAutoLinks_Proxy_WebPageDumper_Loader::$sDirPath . '/asset/image/instruction/update/heroku-manual-deploy.jpg' ) );
                                             /* translators: 1: a proper noun (Web Page Dumper) */
            echo "<p class='description'>" . sprintf( __( 'To update your Web Page Dumper instance, follow the below steps.', 'amazon-auto-links' ), 'Web Page Dumper' ) . "</p>"
                . "<ol>"
                    . "<li><p>" . sprintf( __( 'Have a <a href="%1$s" target="_blank">GitHub account</a> if you don\'t have yet.', 'amazon-auto-links' ), esc_url( 'https://github.com/join' ) ) . "</p></li>"
                    . "<li>"
                                  /* translators: 1: A link to the Web Page Dumper repository 2: a proper noun (Web Page Dumper) 3: a fixed UI label (Fork) */
                        . "<p>" . sprintf( __( 'Go to the <a href="%1$s" target="_blank">repository page</a> and fork %2$s by pressing <code>%3$s</code>.', 'amazon-auto-links' ), esc_url( 'https://github.com/michaeluno/web-page-dumper' ), 'Web Page Dumper', 'Fork' ) . "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageForkRepo}' alt='Fork Repository' /></div>"
                    . "</li>"
                    . "<li>"
                        . "<p>" . sprintf( __( 'If you already have a forked repository, click on <code>%1$s</code> and <code>%2$s</code>.', 'amazon-auto-links' ), 'Fetch upstream', 'Fetch and merge' ) . "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageFetchStream}' alt='Update Repository' /></div>"
                    . "</li>"
                    . "<li><p>" . sprintf( __( 'Log in to <a href="%1$s" target="_blank">Heroku</a>.', 'amazon-auto-links' ), esc_url( 'https://id.heroku.com/login' ) ) . "</p></li>"
                    . "<li>"
                                  /* translators: 1: : a proper noun (Web Page Dumper) 2: a fixed UI label (Deploy)  3: a fixed UI label (Deployment method) */
                        . "<p>" . sprintf( __( 'Select your %1$s instance and from the <code>%2$s</code> tab, click on <strong>GitHub</strong> in the <code>%3$s</code>.', 'amazon-auto-links' ), 'Web Page Dumper', 'Deploy', 'Deployment method' ) . "</p>"
                                  /* translators: 1: : a repository slug, web page dumper, for Web Page Dumper 2: a fixed UI label (Search)  3: a fixed UI label (Connect) */
                        . "<p>" . sprintf( __( 'Search the forked repository by typing <code>%1$s</code> and press <code>%2$s</code>. Then press <code>%3$s</code>.', 'amazon-auto-links' ), 'web page dumper', 'Search', 'Connect' ) . "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageHerokuDeploy}' alt='Deploy Repository' /></div>"
                    . "</li>"
                    . "<li>"
                        . "<p>" . sprintf( __( 'Press <code>%1$s</code>.', 'amazon-auto-links' ), 'Enable Automatic Deploys' ) . "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageAutoDeploy}' alt='Enable Automatic Deploy' /></div>"
                    . "</li>"
                    . "<li>"
                        . "<p>" . sprintf( __( 'For the first time of doing this, you need to press <code>%1$s</code>.', 'amazon-auto-links' ), 'Deploy Branch' ) . "</p>"
                        . "<div class='screenshot'><img src='{$_sURLImageDeployBranch}' alt='Manual Deploy' /></div>"
                    . "</li>"

                . "</ol>";
        }
}