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
 * @since       4.3.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Locale extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {

        $this->___setFormElements();
        $this->___setResources();

        // Setting low priority so that page meta box options are merged
        add_filter( 'validation_' . AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] . '_' . 'default', array( $this, 'replyToValidateTabInputs' ), 100, 4 );

    }

        private function ___setFormElements() {
            $this->addSettingSections(
                array(
                    'section_id'    => $this->_sSectionID,
                )
            );
            $this->addSettingFields( $this->_sSectionID );

            $_aClasses = array(
                'AmazonAutoLinks_FormFields_Unit_Locale',
            );
            foreach( $_aClasses as $_sClassName ) {
                $_oFields = new $_sClassName( $this );
                $_aFields = $_oFields->get();
                foreach( $_aFields as $_aField ) {
                    if ( $this->oUtil->getElement( $_aField, array( 'field_id' ) ) === 'country' ) {
                        continue;
                    }
                    $this->addSettingFields( $_aField );
                }
            }

            add_filter( 'fields_' . $this->oProp->sClassName, array( $this, 'replyToModifyFields' ) );

        }

        private function ___setResources() {

            wp_enqueue_script( 'jquery' );
            $_sPageSlug = $this->oProp->getCurrentPageSlug();
            $this->enqueueScript(
                $this->oUtil->isDebugMode()
                    ? AmazonAutoLinks_SettingsAdminPageLoader::$sDirPath . '/asset/js/locale-select.js'
                    : AmazonAutoLinks_SettingsAdminPageLoader::$sDirPath . '/asset/js/locale-select.min.js',
                $_sPageSlug,
                $this->oProp->getCurrentTabSlug( $_sPageSlug ),
                array(
                    'handle_id'     => 'aalLocaleSelect',
                    'dependencies'  => array( 'jquery' ),
                    'translation'   => array(
                        'ajaxURL'           => admin_url( 'admin-ajax.php' ),
                        'actionHookSuffix'  => 'aal_action_ajax_locale_select',
                        'nonce'             => wp_create_nonce( 'aal_action_ajax_locale_select' ),
                        'spinnerURL'        => admin_url( 'images/loading.gif' ),
                        'pluginName'        => AmazonAutoLinks_Registry::NAME,
                        'scriptName'        => 'Locale Select',
                        'debugMode'         => $this->oUtil->isDebugMode(),
                        'label'             => array(
                        ),
                    ),
                    'in_footer' => true,
                )
            );

        }

        public function replyToModifyFields( $aAllFields ) {

            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_sLocale = $_oOption->get( 'unit_default', 'country' );

            $_aFields = $this->oUtil->getElementAsArray( $aAllFields, array( 'unit_default' ) );
            foreach( $_aFields as $_sFieldID => $_aField ) {
                if ( 'language' === $_sFieldID ) {
                    $_aFields[ $_sFieldID ][ 'label'   ] = AmazonAutoLinks_PAAPI50___Locales::getLanguagesByLocale( $_sLocale );
                    $_aFields[ $_sFieldID ][ 'default' ] = AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale );
                    unset( $_aFields[ $_sFieldID ][ 'attributes' ] );
                    continue;
                }
                if ( 'preferred_currency' === $_sFieldID ) {
                    $_aFields[ $_sFieldID ][ 'label' ]   = AmazonAutoLinks_PAAPI50___Locales::getCurrenciesByLocale( $_sLocale );
                    $_aFields[ $_sFieldID ][ 'default' ] = AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale );
                    unset( $_aFields[ $_sFieldID ][ 'attributes' ] );
                    continue;
                }
            }
            $aAllFields[ 'unit_default' ] = $_aFields;
            return $aAllFields;

        }

    /**
     * Validates submitted form data.
     *
     * @param array $aInputs
     * @param array $aOriginal
     * @param AmazonAutoLinks_AdminPageFramework_MetaBox $oAdminPage
     * @param array $aSubmitInfo
     *
     * @return array
     */
    public function validate( $aInputs, $aOriginal, $oAdminPage, $aSubmitInfo ) {

        // When the Reset button is pressed,
        if (
            $this->oUtil->getElement( $aInputs, array( '_reset_defaults', 'reset' ) )
            && $this->oUtil->getElement( $aInputs, array( '_reset_defaults', 'confirm' ) )
        ) {
            add_action( 'shutdown', array( $this, 'replyToUnsetUnitDefaults' ) );
            $oAdminPage->setSettingNotice( __( 'The unit default options have been reset.', 'amazon-auto-links' ), 'update' );
        }

        return $aInputs;

    }
        /**
         * @since   4.3.0
         */
        public function replyToUnsetUnitDefaults() {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_oOption->update( array( 'unit_default' ), array() );
        }

    /**
     * @param array $aInputs
     * @param array  $aOldInputs
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @param array  $aSubmitInfo
     *
     * @return array
     * @since   4.3.0
     */
    public function replyToValidateTabInputs( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        if ( isset( $aInputs[ 'unit_default' ], $aOldInputs[ 'unit_default' ] ) ) {
            $aInputs[ 'unit_default' ] = $this->___getLanguageAndCurrency( $aInputs[ 'unit_default' ], $aOldInputs[ 'unit_default' ] );
        }
        return $aInputs;

    }
        /**
         * @since   4.3.0
         * @return array An updated form inputs array.
         */
        private function ___getLanguageAndCurrency( array $aInputs, array $aOldInputs ) {

            // If the user changes the locale and the language and currency is not properly set, apply the default values.
            if ( ! isset( $aInputs[ 'country' ], $aOldInputs[ 'country' ] ) ) {
                return $aInputs;
            }
            if (  $aInputs[ 'country' ] === $aOldInputs[ 'country' ] ) {
                return $aInputs;
            }

            // Apply the defaults only when the passed values are not accepted.
            // This is because the JavaScript script already allows the user dynamically select locale-based options.
            $_aLanguages = array_keys( AmazonAutoLinks_PAAPI50___Locales::getLanguagesByLocale( $aInputs[ 'country' ] ) );
            if ( ! in_array( $aInputs[ 'language' ], $_aLanguages, true ) ) {
                $aInputs[ 'language' ] = AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $aInputs[ 'country' ] );
            }
            $_aCurrencies = array_keys( AmazonAutoLinks_PAAPI50___Locales::getCurrenciesByLocale( $aInputs[ 'country' ] ) );
            if ( ! in_array( $aInputs[ 'preferred_currency' ], $_aCurrencies, true ) ) {
                $aInputs[ 'preferred_currency' ] = AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $aInputs[ 'country' ] );
            }
            return $aInputs;

        }

}
