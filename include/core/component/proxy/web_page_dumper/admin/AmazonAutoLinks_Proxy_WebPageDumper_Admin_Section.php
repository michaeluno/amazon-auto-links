<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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
                sprintf( __( '<a href="%1$s" target="_blank">%2$s</a> is a type of proxy server that assists HTTP requests.', 'amazon-auto-links' ), esc_url( 'https://github.com/michaeluno/web-page-dumper' ), 'Web Page Dumper' )
            ),
        );
    }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _construct( $oFactory ) {
    }

    /**
     * Adds form fields.
     * @since       4.5.0
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @param       string $sSectionID
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'        => 'enable',
                'title'           => __( 'Enable', 'amazon-auto-links' ),
                'label'           => sprintf( __( 'Utilize %1$s to assist HTTP requests.', 'amazon-auto-links' ), 'Web Page Dumper' ),
                'type'            => 'checkbox',
                'description'     => array(
                    sprintf( __( 'API requests will not use %1$s', 'amazon-auto-links' ), 'Web Page Dumper' ),
                ),
            ),
            array( 
                'field_id'        => 'list',
                'title'           => __( 'List', 'amazon-auto-links' ),
                'type'            => 'textarea',
                'attributes'      => array(
                    'style'     => 'height: 200px; width: 100%;',
                    // 'class'     => 'proxy-list',
                ),
                'class'           => array(
                    'input' => 'list-web-page-dumper',
                ),
                'default'         => 'https://web-page-dumper.herokuapp.com/',
                'description'     => array(
                    sprintf( __( 'Enter addresses of %1$s one per line.', 'amazon-auto-links' ), 'Web Page Dumper' )
                    . ' e.g.<code>https://web-page-dumper.herokuapp.com/</code>',
                    sprintf( __( 'It is recommended to create your own %1$s for better performance.', 'amazon-auto-links' ), 'Web Page Dumper' ) . ' ' . sprintf( __( 'To create one, see <a href="%1$s">here</a>.', 'amazon-auto-links' ), esc_url( add_query_arg( array( 'tab' => 'web_page_dumper_help' ) ) ) ),
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
                            'placeholder' => __( 'Type a URL of Web Page Dumper here to check if it is alive.', 'amazon-auto-links' ),
                        ),
                    ),
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

}