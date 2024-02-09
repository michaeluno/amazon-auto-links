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
 * Adds the 'Web Page Dumper' form section to the 'Proxies' tab.
 * 
 * @since       4.5.0
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Admin_Section extends AmazonAutoLinks_AdminPage_Section_Base {

    public $sTabSlug = 'proxy';

    /**
     * @return array
     * @since  4.5.0
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'web_page_dumper',
            'tab_slug'      => $this->sTabSlug,
            'title'         => 'Web Page Dumper',
            'description'   => array(
                /* translators: 1: URL for the Web Page Dumper repository 2: a proper noun (Web Page Dumper) */
                sprintf( __( '<a href="%1$s" target="_blank">%2$s</a> is a type of proxy server that assists HTTP requests.', 'amazon-auto-links' ), esc_url( 'https://github.com/michaeluno/web-page-dumper' ), 'Web Page Dumper' )
            ),
        );
    }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _construct( $oFactory ) {
        add_filter(
            'field_definition_' . $oFactory->oProp->sClassName . '_' . $this->sSectionID . '_update_required',
            array( $this, 'replyToGetFieldDefinition_update_required' )
        );
    }

    /**
     * Adds form fields.
     * @since       4.5.0
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @param       string $sSectionID
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_oOption     = AmazonAutoLinks_Option::getInstance();
        $_bAdvanced   = $_oOption->isAdvancedWebPageDumperOptionSupported();
        $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'        => 'enable',
                'title'           => __( 'Enable', 'amazon-auto-links' ),
                /* translators: 1: a proper noun (Web Page Dumper) */
                'label'           => sprintf( __( 'Utilize %1$s to assist HTTP requests.', 'amazon-auto-links' ), 'Web Page Dumper' ),
                'type'            => 'checkbox',
                'description'     => array(
                    /* translators: 1: a proper noun (Web Page Dumper) */
                    sprintf( __( 'PA-API requests does not use %1$s.', 'amazon-auto-links' ), 'Web Page Dumper' ),
                ),
            ),
            array( 
                'field_id'        => 'list',
                'title'           => __( 'List', 'amazon-auto-links' ),
                'type'            => 'textarea',
                'attributes'      => array(
                    'style'     => 'height: 200px; width: 100%;',
                ),
                'class'           => array(
                    'input' => 'list-web-page-dumper',
                ),
                'description'     => array(
                    /* translators: 1: a proper noun (Web Page Dumper) */
                    sprintf( __( 'Enter addresses of %1$s one per line.', 'amazon-auto-links' ), 'Web Page Dumper' )
                    . ' e.g.<code>https://web-page-dumper.herokuapp.com/</code>',
                    sprintf(
                        /* translators: 1: a proper noun (Web Page Dumper) */
                        __( 'It is recommended to have your own %1$s for better performance.', 'amazon-auto-links' ), 'Web Page Dumper'
                    ) . ' '
                        /* translators: 1: A link to a setting page describing how to create a Web Page Dumper instance */
                    . sprintf( __( 'To create one, see <a href="%1$s">here</a>.', 'amazon-auto-links' ), esc_url( add_query_arg( array( 'tab' => 'web_page_dumper_help' ) ) . '#creating-own-web-page-dumper' ) ),
                ),
            ),
            array(
                'field_id'      => '_test',
                'save'          => false,
                'class'         => array(
                    'fieldrow' => 'test-web-page-dumper'
                ),
                'content'       => array(
                    array(
                        'field_id'     => '_test_web_page_dumper',
                        'save'         => false,
                        'type'         => 'url',
                        'before_input' => '<h4>' . __( 'Test', 'amazon-auto-links' ) . '</h4>',
                        'after_input'  => "<a id='web-page-dumper-action-test' class='button button-secondary button-small button-action action-test-web-page-dumper'>" . __( 'Check', 'amazon-auto-links' ) . "</a>"
                            . "<input id='web-page-dumper-action-add-to-list' disabled='disabled' type='button' class='button button-secondary button-small button-action action-add-to-list-web-page-dumper' value='" . esc_attr( __( 'Add to List', 'amazon-auto-links' ) ) . "'>",
                        'attributes'   => array(
                            'id'          => 'web-page-dumper-input-url',
                            'name'        => '',
                            /* translators: 1: a proper noun (Web Page Dumper) */
                            'placeholder' => sprintf( __( 'Type a URL of %1$s here to check if it is alive.', 'amazon-auto-links' ), 'Web Page Dumper' ),
                        ),
                    ),
                ),
            ),
            array(
                'field_id'        => 'update_required',
                'title'           => __( 'Update Required', 'amazon-auto-links' ),
                'if'              => ( boolean ) $_oToolOption->get( $this->sSectionID, 'enable' ),
                'hidden'          => true,
                'attributes'      => array(
                    'style'     => 'height: 100px; width: 100%;',
                    'readonly'  => 'readonly',
                ),
                'class'           => array(
                    'fieldrow'  => 'web-page-dumper-update-required-fieldrow',
                ),
                'content'         => "<div class='mb-1'><div class='web-page-dumper-update-required-table'></div></div>",
                'description'     => array(
                    "<div class='notice-warning'>"
                        . "<p class=''><span class='field-error'>"
                        . "<span class='dashicons dashicons-warning'></span>"
                        /* translators: 1: a proper noun (Web Page Dumper) */
                        . sprintf( __( 'Some of your %1$s instances are outdated and will not function properly. Please update them to the latest.', 'amazon-auto-links' ), 'Web Page Dumper' )
                        /* translators: 1: a required version of Web Page Dumper */
                        . ' ' . sprintf( __( 'The required version is <code>%1$s</code> or above.', 'amazon-auto-links' ), AmazonAutoLinks_Proxy_WebPageDumper_Loader::REQUIRED_VERSION )
                    . "</span></p>"
                    . "</div>",
                    sprintf(
                        /* translators: 1: a proper noun (Web Page Dumper) 2: a link to the usage description page */
                         __( 'To update a %1$s instance, please see <a href="%2$s">here</a>.', 'amazon-auto-links' ),
                        'Web Page Dumper',
                        esc_url( add_query_arg( array( 'tab' => 'web_page_dumper_help' ) ) . '#updating-web-page-dumper' )
                    ),
                ),
            ),
            array(
                'field_id'        => 'always',
                'title'           => __( 'Always', 'amazon-auto-links' ),
                'type'            => 'textarea',
                'attributes'      => array(
                    'style'     => 'height: 100px; width: 100%;',
                    'readonly'  => $_bAdvanced ? null : 'readonly',
                ),
                'class'           => array(
                ),
                'description'     => array(
                    ( $_bAdvanced
                        ? ''
                        : '<span class="warning">' . AmazonAutoLinks_Message::get( 'available_in_pro' ) . '</span> ' )
                            /* translators: 1: a proper noun (Web Page Dumper) */
                    . ' ' . sprintf( __( 'Always use %1$s for these URLs.', 'amazon-auto-links' ), 'Web Page Dumper' )
                            /* translators: 1: a proper noun (Web Page Dumper) */
                    . ' ' . sprintf( __( 'By default %1$s is used as a fallback when a regular HTTP request fails.', 'amazon-auto-links' ), 'Web Page Dumper' )
                            /* translators: 1: a proper noun (Web Page Dumper) */
                    . ' ' . sprintf( __( 'For HTTP requests of these URL patterns will use %1$s first.', 'amazon-auto-links' ), 'Web Page Dumper' )
                    . ' ' . __( 'Set URL patterns with a wildcard one per line.', 'amazon-auto-links' )
                            /* translators: 1: a link to the PHP manual page for fnmatch()  */
                    . ' ' . sprintf( __( 'The plugin uses <code><a href="%1$s" target="_blank">fnmatch()</a></code> for the matching mechanism.', 'amazon-auto-links' ), esc_url( 'https://www.php.net/manual/en/function.fnmatch.php' ) )
                    . ' ' . 'e.g.<code>https://some-domain.org/*</code>, <code>*?some-query=some-value</code>',
                ),
            ),
            array(
                'field_id'        => 'excludes',
                'title'           => __( 'Exclude', 'amazon-auto-links' ),
                'type'            => 'textarea',
                'attributes'      => array(
                    'style'     => 'height: 100px; width: 100%;',
                    'readonly'  => $_bAdvanced ? null : 'readonly',
                ),
                'description'     => array(
                    ( $_bAdvanced
                        ? ''
                        : '<span class="warning">' . AmazonAutoLinks_Message::get( 'available_in_pro' ) . '</span> ' )
                      /* translators: 1: a proper noun (Web Page Dumper) */
                    . sprintf( __( 'Do not use %1$s for these URLs.', 'amazon-auto-links' ), 'Web Page Dumper' )
                    . ' ' . __( 'The <code>Always</code> options takes precedence.', 'amazon-auto-links' )
                    . ' ' . __( 'Set URL patterns with a wildcard one per line.', 'amazon-auto-links' )
                            /* translators: 1: a link to the PHP manual page of fnmatch() */
                    . ' ' . sprintf( __( 'The plugin uses <code><a href="%1$s" target="_blank">fnmatch()</a></code> for the matching mechanism.', 'amazon-auto-links' ), esc_url( 'https://www.php.net/manual/en/function.fnmatch.php' ) )
                    . ' ' . 'e.g.<code>https://some-domain.org/*</code>, <code>*?some-query=some-value</code>',
                ),
            ),
            array()
        );

    }

    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        if ( $aInputs[ 'enable' ] && empty( $aInputs[ 'list' ] ) ) {
            $oAdminPage->setSettingNotice( __( 'The list is empty.', 'amazon-auto-links' ) );
            return $aOldInputs;

        }
        if ( $aInputs[ 'enable' ] && ! $this->getElement( $aOldInputs, array( 'enable' ) ) ) {
            $this->___renewAmazonCookies();
        }
        return $aInputs;
    }
        /**
         * @since 4.5.0
         */
        private function ___renewAmazonCookies() {

            $_sTransientPrefix     = AmazonAutoLinks_Registry::TRANSIENT_PREFIX;
            foreach( AmazonAutoLinks_Locales::getLocales() as $_sLocale ) {

                $_sTransientKey        = "_transient_{$_sTransientPrefix}_cookies_{$_sLocale}";
                $_sTransientKeyTimeout = "_transient_timeout_{$_sTransientPrefix}_cookies_{$_sLocale}";
                if ( ! $this->isExpired( get_option( $_sTransientKeyTimeout, 0 ) ) ) {
                    $this->scheduleTask( 'aal_action_renew_amazon_cookies', array( $_sLocale, '' ) );
                }

                // Delete them anyway
                delete_option( $_sTransientKey );
                delete_option( $_sTransientKeyTimeout );

                $_oVersatileCookies = new AmazonAutoLinks_VersatileFileManager_AmazonCookies( $_sLocale );
                $_oVersatileCookies->delete();

            }

        }

    public function replyToGetFieldDefinition_update_required( $aField ) {
        $_oToolOption   = AmazonAutoLinks_ToolOption::getInstance();
        $_aVersions     = $_oToolOption->get( $this->sSectionID, 'versions' );
        if ( empty( $_aVersions ) ) {
            return $aField;
        }
        $_sList         = $_oToolOption->get( $this->sSectionID, 'list' );
        $_aItems        = explode( PHP_EOL, $_sList );
        $_sRequired     =  AmazonAutoLinks_Proxy_WebPageDumper_Loader::REQUIRED_VERSION;
        $_aInsufficient = array();
        foreach( $_aVersions as $_sURL => $_aVersion ) {
            if ( ! in_array( $_sURL, $_aItems, true ) ) {
                continue;
            }
            $_sVersion = ( string ) $this->getElement( $_aVersion, 'version' );
            if ( version_compare( $_sVersion, $_sRequired, '>=' ) ) {
                continue;
            }
            $_aInsufficient[ $_sURL ] = $_sVersion ? $_sVersion : __( 'n/a', 'amazon-auto-links' );
        }
        if ( empty( $_aInsufficient ) ) {
            return $aField;
        }
        $aField[ 'hidden' ]  = false;
        $aField[ 'content' ] = "<div class='mb-1'>"
               . AmazonAutoLinks_Proxy_WebPageDumper_Utility::getWebPageDumperVersionTable( $_aInsufficient )
            . "</div>";
        return $aField;
    }

}