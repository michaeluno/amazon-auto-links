<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno; Licensed GPLv2
 *
 */

/**
 * Provides the definitions of form fields for the main section of the 'contextual' unit type.
 *
 * @since  3.5.0
 * @since  4.5.0    Changed the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_ContextualUnit_Main extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     *
     * Pass an empty string to the parameter for meta box options.
     *
     * @return      array
     */
    public function get( $sFieldIDPrefix='' ) {

        $_aFields = array(
            array(
                'field_id'      => $sFieldIDPrefix . 'criteria',
                'title'         => __( 'Additional Criteria', 'amazon-auto-links' ),
                'type'          => 'checkbox',
                'label'         => array(
                    'post_title'        => __( 'Post Title', 'amazon-auto-links' ),
                    'taxonomy_terms'    => __( 'Taxonomy Terms', 'amazon-auto-links' ),
                    'breadcrumb'        => __( 'Breadcrumb', 'amazon-auto-links' ),
                ),
                'default'       => array(
                    'post_title'        => true,
                    'taxonomy_terms'    => true,
                    'breadcrumb'        => false,
                ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'additional_keywords',
                'title'         => __( 'Additional Keywords', 'amazon-auto-links' ),
                'type'          => 'text',
                'attributes'    => array(
                    'class' => 'width-full',
                ),
                'tip'           => array(
                    __( 'Add additional search keywords, separated by commas.', 'amazon-auto-links' ),
                    ' e.g. <code>' . __( 'laptop, desktop', 'amazon-auto-links' ) . '</code>',
                ),
            ),
            array( // 3.12.0
                'field_id'      => $sFieldIDPrefix . 'excluding_keywords',
                'title'         => __( 'Keywords to Exclude', 'amazon-auto-links' ),
                'type'          => 'text',
                'attributes'    => array(
                    'class' => 'width-full',
                ),
                'tip'           => array(
                    __( 'Specify keywords to exclude from search keywords, separated by commas.', 'amazon-auto-links' ),
                    ' e.g. <code>' . __( 'test, demo', 'amazon-auto-links' ) . '</code>',
                ),
            ),
            $this->___getCountryField( $sFieldIDPrefix ),
        );
        return $_aFields;

    }
        /**
         * @param  string $sFieldIDPrefix
         * @return array
         * @since  4.7.4
         */
        private function ___getCountryField( $sFieldIDPrefix ) {

            $_aLabels = $this->___getLocaleLabels();
            $_aBase   = array(
                'field_id'          => $sFieldIDPrefix . 'country',
                'type'              => 'select',
                'title'             => __( 'Country', 'amazon-auto-links' ),
                'label'             => $_aLabels,
                'default'           => AmazonAutoLinks_Option::getInstance()->getMainLocale(),
            );
            // In the widget page in WordPress 5.8 or above, the select2 field type does not load
            if ( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] !== $this->getHTTPQueryGET( 'post_type' ) ) {
                return $_aBase;
            }
            return array(
                'type'              => 'select2',
                'icon'              => $this->getLocaleIcons( array_keys( $_aLabels ) ),
                'description'       => sprintf(
                    __( 'If the country is not listed, set PA-API keys in the <a href="%1$s">Associates</a> section.', 'amazon-auto-links' ),
                    $this->getAPIAuthenticationPageURL()
                ),
            ) + $_aBase;
        }
            /**
             * @return string[]
             * @since  5.0.0
             */
            private function ___getLocaleLabels() {
                $_aAdWidgetLocales = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport( true );
                $_aLocaleLabels    = array();
                foreach( $_aAdWidgetLocales as $_oLocale ) {
                    $_aLocaleLabels[ $_oLocale->sSlug ] = $_oLocale->getName();
                }
                return $_aLocaleLabels + $this->getPAAPILocaleFieldLabels();
            }

}