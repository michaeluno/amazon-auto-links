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
 * Adds the 'Proxies' form section to the 'Proxies' tab.
 * 
 * @since       4.2.0
 */
class AmazonAutoLinks_ToolAdminPage_Proxy_Tab_Section extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since  4.5.0
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'proxies',
            'tab_slug'      => $this->sTabSlug,
            'title'         => __( 'HTTP Proxies', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       4.2.0
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @return      void
     */
    protected function _construct( $oFactory ) {}

    /**
     * Adds form fields.
     * @since       4.2.0
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @param       string $sSectionID
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_oOption           = AmazonAutoLinks_Option::getInstance();
        $_bAdvanced         = $_oOption->isAdvancedProxyOptionSupported();
        $_aAttributesForPro = $_bAdvanced
            ? array()
            : array(
                'disabled' => 'disabled',
            );
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'        => 'enable',
                'title'           => __( 'Enable', 'amazon-auto-links' ),
                'type'            => 'checkbox',
                'label'           => __( 'Enable proxies for regular HTTP requests that this plugin performs. API requests will not use proxies.', 'amazon-auto-links' ),
            ),
            array( 
                'field_id'        => 'proxy_list',
                'title'           => __( 'Proxy List', 'amazon-auto-links' ),
                'type'            => 'textarea',
                'attributes'      => array(
                    'style'     => 'height: 400px; width: 100%;',
                    'class'     => 'proxy-list',
                ),
                'description'     => array(
                    __( 'Enter proxy addresses one per line. Accepts up to 10000 items at a maximum.', 'amazon-auto-links' ),
                    sprintf( __( 'The format: <code>%1$s</code>', 'amazon-auto-links' ), '{scheme}://{username}:{password}@{ip address}:{port number}' ),
                ),
            ),
            array(
                'field_id'        => '_load',
                'type'            => 'submit',
                'value'           => __( 'Load', 'amazon-auto-links' ),
                'save'            => false,
                'attributes'      => array(
                    'class' => 'button secondary button_load_proxies',
                ),
            ),
            array(
                'field_id'        => 'unusable',
                'title'           => __( 'Unusable', 'amazon-auto-links' ),
                'type'            => 'textarea',
                'attributes'      => array(
                    'style'     => 'height: 300px; width: 100%;',
                    'class'     => 'unusable-list',
                ),
                'description'     => array(
                    __( 'Unusable items will be listed here and updated automatically when they are found defunct.', 'amazon-auto-links' ),
                    __( 'To clear this unusable proxy list, just remove the input and save.', 'amazon-auto-links' ),
                ),
            ),
            array(
                'field_id'        => '_clear',
                'type'            => 'submit',
                'value'           => __( 'Clear', 'amazon-auto-links' ),
                'save'            => false,
                'attributes'      => array(
                    'class' => 'button secondary button_clear_unusable',
                ),
            ),
            array(
                'field_id'        => 'automatic_updates',
                'title'           => __( 'Automatic Updates', 'amazon-auto-links' ),
                'type'            => 'checkbox',
                'label'           => __( 'Enable automatic updates for the proxy list.', 'amazon-auto-links' ),
                'attributes'      => array(
                ) + $_aAttributesForPro,
                'description'     => array(
                    $_bAdvanced
                        ? ''
                        : '<span class="warning">' . __( 'This is available in Pro.', 'amazon-auto-links' ) . '</span>'
                ),
            ),
            array(
                'field_id'        => 'proxy_update_interval',
                'title'           => __( 'Update Interval', 'amazon-auto-links' ),
                'type'            => 'size',
                'units'             => array(
                    86400    => __( 'day(s)', 'amazon-auto-links' ),
                    604800   => __( 'week(s)', 'amazon-auto-links' ),
                ),
                'attributes'        => array(
                    'size'      => array(
                        'step' => 0.1
                    ),
                ) + $_aAttributesForPro,
                'after_fieldset'  => $this->___getUpdateScheduleText(),
            ),
            array(
                'field_id'        => '_save',
                'type'            => 'submit',
                'value'           => __( 'Save', 'amazon-auto-links' ),
                'save'            => false,
            ),
            array()
        );

    }
        /**
         * @return  string
         * @since   4.2.0
         */
        private function ___getUpdateScheduleText() {
            return $this->___getIntervalScheduleInfo(
                wp_next_scheduled( 'aal_action_proxy_update', array() ),
                AmazonAutoLinks_ToolOption::getInstance()->get( array( 'proxies', 'update_last_run_time' ) )
            );
        }
            /**
             * Returns a schedule information of an interval option.
             * @param   integer The `time()` result value in seconds.
             * @param   integer|boolean
             * @since   4.2.0
             * @since   4.2.1   Removed the warning message for the cache option field and separated the part into another method.
             * @since   4.3.0   Moved from `AmazonAutoLinks_PluginUtility`.
             * @return  string
             */
            private function ___getIntervalScheduleInfo( $biNextScheduledCheck, $iLastRunTime ) {
                $_sLastRunTime  = __( 'Last Run', 'amazon-auto-links' ) . ': ';
                $_sLastRunTime .= $iLastRunTime
                    ? self::getSiteReadableDate( $iLastRunTime , get_option( 'date_format' ) . ' g:i a', true )
                    : __( 'n/a', 'amazon-auto-links' );
                return "<div>"
                            . "<p>"
                                . sprintf(
                                    __( 'Next scheduled at %1$s.', 'amazon-auto-links' ),
                                    self::getSiteReadableDate( $biNextScheduledCheck , get_option( 'date_format' ) . ' g:i a', true )
                                )
                            . "</p>"
                            . "<p>" . $_sLastRunTime . "</p>"
                        . "</div>";
            }


    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {

        // If the Clear button is pressed,
        if ( '_clear' === $this->getElement( $aSubmitInfo, 'field_id' ) ) {
            $aInputs[ 'unusable' ] = '';
        }

        // Sanitize the proxy list.
        $aInputs = $this->___getProxiesSanitized( $aInputs );

        // If the automatic update is not enabled, unschedule the event.
        if ( ! $this->getElement( $aInputs, array( 'automatic_updates' ), false ) ) {
            $_biTimeStamp = wp_next_scheduled( 'aal_action_proxy_update', array() );
            $_iTimeStamp  = ( integer ) $_biTimeStamp;
            wp_unschedule_event( $_iTimeStamp, 'aal_action_proxy_update', array() );
        }

        // Return the saving data.
        return $aInputs;

    }

        private function ___getProxiesSanitized( array $aInputs ) {

            $_sProxies   = ( string ) $this->getElement( $aInputs, 'proxy_list' );
            $_aProxies   = preg_split( "/\s+/", trim( ( string ) $_sProxies ), 0, PREG_SPLIT_NO_EMPTY );
            $_aSanitized = array();
            foreach( $_aProxies as $_iIndex => $_sAddress ) {
                if ( ! filter_var( $_sAddress, FILTER_VALIDATE_URL ) ){
                    continue;
                }
                $_aSanitized[] = $_sAddress;
            }
            $_aSanitized = array_unique( $_aSanitized );

            // Keep up to 10000
            $_aSanitized = $this->getTopMostItems( $_aSanitized, 10000 );

            $_sProxies   = implode( PHP_EOL, $_aSanitized );
            $aInputs[ 'proxy_list' ] = $_sProxies;
            return $aInputs;

        }

}