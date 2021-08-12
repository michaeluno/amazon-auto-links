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
 * Adds the 'Associates' admin setting section.
 * 
 * @since       4.5.0
 */
class AmazonAutoLinks_Main_AdminPage_Section_Associates extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @var AmazonAutoLinks_Option
     */
    public $oOption;

    /**
     * @return array
     * @since  4.5.0
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'associates',
            'title'         => __( 'Amazon Associates', 'amazon-auto-links' ),
            'description'   => __( 'Set Amazon Associates tags.', 'amazon-auto-links' )
                . ' ' . __( "If you don't have PA-API keys, leave them blank as they are optional.", 'amazon-auto-links' )
        );
    }

    /**
     * A user constructor.
     * 
     * @since       4.5.0
     * @return      void
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _construct( $oFactory ) {
        $this->oOption = AmazonAutoLinks_Option::getInstance();
    }

    /**
     * Adds form fields.
     * @since       4.5.0
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @param       string $sSectionID
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_aLocaleSlugs = AmazonAutoLinks_Locales::getLocales();
        $_aLocaleNames = $this->___getLocaleNames( $_aLocaleSlugs );
        asort( $_aLocaleNames );
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'locale',
                'title'             => __( 'Locale', 'amazon-auto-links' ),
                'type'              => 'revealer',
                'select_type'       => 'select',
                'default'           => $this->___getDefaultLocale(),
                'label'             => $_aLocaleNames,
                'selectors'         => $this->___getLocaleSelectors( $_aLocaleSlugs ),
                'description'       => __( 'The country of the marketplace.', 'amazon-auto-links' )
                    . ' ' . __( 'This selected locale serves as the main locale.', 'amazon-auto-links' ),
                'value'             => $this->getHTTPQueryGET( 'locale', null ),    // sanitization done
            )
        );
        foreach( $this->___getLocaleFieldSets( $_aLocaleSlugs ) as $_aFieldset ) {
            $oFactory->addSettingFields( $sSectionID, $_aFieldset );
        }
        $oFactory->addSettingFields(
            $sSectionID,
            array(
                'field_id'          => '_submit',
                'type'              => 'submit',
                'label_min_width'   => 0,
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),
                'save'              => false,
            )
        );
        
    }
        /**
         * @param  array $aLocaleSlugs
         * @return array
         * @since  4.5.0
         */
        private function ___getLocaleNames( array $aLocaleSlugs ) {
            $_aNames = array();
            foreach( $aLocaleSlugs as $_sLocale ) {
                $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
                $_aNames[ $_sLocale ] = $_oLocale->getName();
            }
            return $_aNames;
        }
        /**
         * @param  array $aLocaleSlugs
         * @return array
         * @since  4.5.0
         */
        private function ___getLocaleSelectors( array $aLocaleSlugs ) {
            $_aSelectors = array();
            foreach( $aLocaleSlugs as $_sLocale ) {
                $_aSelectors[ $_sLocale ] = '.locale-' . strtolower( $_sLocale );
            }
            return $_aSelectors;
        }
        /**
         * @param  array $aLocaleSlugs
         * @return array
         */
        private function ___getLocaleFieldSets( array $aLocaleSlugs ) {
            $_aFieldSets = array();
            foreach( $aLocaleSlugs as $_sLocale ) {
                $_oLocale       = new AmazonAutoLinks_Locale( $_sLocale );
                $_sCountryName  = $_oLocale->getName();
                $_aFieldSets[]  = array(
                    'field_id' => $_sLocale,
                    'title'    => "<span class='country-name'>" . $_oLocale->getName() . "</span>"
                        . "<div class='stay-right'>"
                            . "<div class='country-flag'><img src='" . $_oLocale->getFlagImg() . "' alt='" . esc_attr( $_sCountryName ) . "' title='" . esc_attr( $_sCountryName ) . "' /></div>"
                        . "</div>"
                        . "<div class='stay-right'>"
                            . "<span class='market-place-domain'>" . $_oLocale->getDomain() . "</span>"
                        . "</div>",
                    'class'    => array(
                        'fieldrow' => array( 'locale-' . strtolower( $_sLocale ), 'locale' ),
                        'fieldset' => 'locales',
                    ),
                    'content'  => $this->___getLocaleFields( $_sLocale ),
                    'attributes' => array(
                        'fieldrow' => array(
                            'data-locale' => $_sLocale,
                        ),
                    ),
                );
            }
            return $_aFieldSets;
        }
            private function ___getLocaleFields( $sLocale ) {
                $_bSupportPAAPI = AmazonAutoLinks_PAAPI50___Locales::exists( $sLocale );
                $_oPAAPILocale  = new AmazonAutoLinks_PAAPI50_Locale( $sLocale );
                $_aFields       = array(
                    array(
                        'field_id'          => 'associate_id',
                        'type'              => 'text',
                        'title'             => __( 'Associate ID', 'amazon-auto-links' ),
                        'default'           => $this->oOption->getAssociateID( $sLocale ),
                        'attributes'        => array(
                            'class' => 'associate-id'
                        ),

                    ),
                );
                if ( ! $_bSupportPAAPI ) {
                    return $_aFields;
                }

                $_aPAAPIFields  = array(
                    array(
                        'field_id'          => '_paapi_title',
                        'save'              => false,
                        'title'             => __( 'PA-API', 'amazon-auto-links' ),
                        'content'           => ''
                    ),
                    array(
                        'field_id'          => 'paapi',
                        'class'             => array(
                            'fieldset' => array( 'paapi-' . strtolower( $sLocale ) , 'paapi' ),
                        ),
                        'content'           => array(
                            array(
                                'field_id'          => 'access_key',
                                'type'              => 'text',
                                'title'             => __( 'Access Key', 'amazon-auto-links' ),
                                'default'           => $this->oOption->getPAAPIAccessKey( $sLocale ),
                                'attributes'        => array(
                                    'minlength' => 20,
                                    'maxlength' => 20,
                                    'size' => version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' )
                                        ? 24
                                        : 40,
                                    'class' => 'access-key',
                                ),
                            ),
                            array(
                                'field_id'          => 'secret_key',
                                'type'              => 'password',
                                'title'             => __( 'Secret Key', 'amazon-auto-links' ),
                                'default'           => $this->oOption->getPAAPISecretKey( $sLocale ),
                                'attributes'        => array(
                                    'minlength' => 40,
                                    'maxlength' => 40,
                                    'size' => version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' )
                                        ? 40
                                        : 60,
                                    'class' => 'secret-key'
                                ),
                            ),
                            array(
                                'title'             => __( 'Preferred Language' ),
                                'field_id'          => 'language',
                                'type'              => 'select',
                                'label'             => $_oPAAPILocale->getLanguages(),
                                'default'           => $_oPAAPILocale->getDefaultLanguage(),
                            ),
                            array(
                                'title'             => __( 'Preferred Currency' ),
                                'type'              => 'select',
                                'field_id'          => 'currency',
                                'label'             => $_oPAAPILocale->getCurrencies(),
                                'default'           => $_oPAAPILocale->getDefaultCurrency(),
                            ),
                            array(
                                'title'             => __( 'Status', 'amazon-auto-links' ),
                                'field_id'          => '_status',
                                'save'              => false,
                                'content'           => $this->___getPAAPIStatus( $sLocale ),
                                'class'             => array(
                                    'fieldset' => 'fieldset-connectivity'
                                ),
                            ),
                            array(
                                'title'             => __( 'Last Checked', 'amazon-auto-links' ),
                                'field_id'          => 'last_connected',
                                'content'           => $this->___getPAAPILastChecked( $sLocale )
                            ),
                            array(
                                // Stores 1 or 0
                                'field_id'          => 'status',
                                'type'              => 'hidden',
                                'hidden'            => true,
                                'class'             => array(
                                    'input' => 'status'
                                ),
                            ),
                            array(
                                // Stores a timestamp
                                'field_id'          => 'last_connected',
                                'class'             => array(
                                    'input' => 'last-checked'
                                ),
                                'type'              => 'hidden',
                                'hidden'            => true,
                            ),
                            array(
                                'field_id'          => '_test',
                                'save'              => false,
                                'content'           => "<div class='container-button'>"
                                        . "<a class='button-secondary action-check-paapi' title='" . esc_attr( __( 'Check PA-API connection.', 'amazon-auto-links' ) ) . "'>"
                                            . __( 'Check API', 'amazon-auto-links' )
                                        . "</a>"
                                    . "</div>"
                                    . "<div class='response-text-paapi-check'></div>",
                                'description'       => sprintf(
                                    __( 'If you get an error, try testing your keys with <a href="%1$s" target="_blank">Scratchpad</a>.', 'amazon-auto-links' ),
                                    esc_url( 'https://webservices.amazon.com/paapi5/scratchpad/index.html' )
                                ),
                            ),
                            array(
                                'field_id'          => '_disclaimer',
                                'save'              => false,
                                'title'             => __( 'Disclaimer', 'amazon-auto-links' ),
                                'content'           => ''
                                    . "<div class='container-disclaimer'><p class='notice disclaimer'>"
                                        . $_oPAAPILocale->getDisclaimer()
                                    . "</p></div>",
                                'description'       => array(
                                    sprintf(
                                        __( 'Please check the <a href="%1$s" target="_blank">PA-API license agreement</a> to be safe.', 'amazon-auto-links' ),
                                        esc_url( $_oPAAPILocale->getLicenseAgreementURL() )
                                    )
                                ),
                            ),
                        ),
                    ),
                );
                return array_merge( $_aFields, $_aPAAPIFields );
            }
                private function ___getPAAPILastChecked( $sLocale ) {
                    $_oOption      = AmazonAutoLinks_Option::getInstance();
                    $_iLastChecked = ( integer ) $_oOption->get( array( 'associates', $sLocale, 'paapi', 'last_connected' ) );
                    return "<span class='paapi-last-checked'>"
                            . $this->getSiteReadableDate( $_iLastChecked, get_option( 'date_format' ) . ' H:i:s', true )
                        . "</span>";
                }
                private function ___getPAAPIStatus( $sLocale ) {
                    $_bnConnected        = $this->oOption->getPAAPIStatus( $sLocale );
                    $_sConnectedClass    = true  === $_bnConnected ? '' : 'hidden';
                    $_sDisconnectedClass = false === $_bnConnected ? '' : 'hidden';
                    $_sUntestedClass     = null  === $_bnConnected ? '' : 'hidden';
                    return "<div class='status-connectivity connected {$_sConnectedClass}' title='" . esc_attr( __( 'Connected', 'amazon-auto-links' ) ) . "'>"
                            . "<span class='status-connected dashicons dashicons-yes-alt'></span>" . "<span>" . __( 'Connected', 'amazon-auto-links' ) . "</span>"
                        . "</div>"
                        . "<div class='status-connectivity disconnected {$_sDisconnectedClass}' title='" . esc_attr( __( 'Disconnected', 'amazon-auto-links' ) ) . "'>"
                            . "<span class='status-disconnected dashicons dashicons-warning'></span>" . "<span>" . __( 'Disconnected', 'amazon-auto-links' ) . "</span>"
                        . "</div>"
                        . "<div class='status-connectivity untested {$_sUntestedClass}' title='" . esc_attr( __( 'Untested', 'amazon-auto-links' ) ) . "'>"
                            . "<span class='status-disconnected dashicons dashicons-remove'></span>" . "<span>" . __( 'Untested', 'amazon-auto-links' ) . "</span>"
                        . "</div>"
                        ;
                }

        /**
         * @return string
         * @remark For backward compatibility below 4.5.0.
         * @since  4.5.0
         */
        private function ___getDefaultLocale() {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            return ( string ) $_oOption->get(
                array( 'authentication_keys', 'server_locale' ),
                $_oOption->get( array( 'unit_default', 'country' ), 'US' )
            );
        }

    /**
     * Validates the submitted form data.
     *
     * @param  array $aInputs
     * @param  array $aOldInputs
     * @param  AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @param  array $aSubmitInfo
     * @return array
     * @since  4.5.0
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        return $aInputs;
    }

}