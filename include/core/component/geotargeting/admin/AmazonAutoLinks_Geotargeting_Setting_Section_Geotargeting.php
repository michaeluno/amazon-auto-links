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
 * Adds a form section to an in-page tab.
 *
 * @since       4.6.0
 */
class AmazonAutoLinks_Geotargeting_Setting_Section_Geotargeting extends AmazonAutoLinks_AdminPage_Section_Base {

    protected function _getArguments() {
        return array(
            'section_id'    => 'geotargeting',
            'tab_slug'      => $this->sTabSlug,
            'title'         => __( 'Geotargeting', 'amazon-auto-links' ),
            'description'   => array(
                 __( 'By enabling the Geotargeting option, Amazon product affiliate links will be transformed into the ones of the locale where the visitor resides, presumed by IP address.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * A user constructor.
     *
     * @since       4.0.0
     * @return      void
     */
    protected function _construct( $oFactory ) {}

    /**
     * Adds form fields.
     * @since       4.0.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'              => 'enable',
                'type'                  => 'radio',
                'title'                 => __( 'Enable', 'amazon-auto-links' ),
                'label'                 => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
            ),
            array(
                'field_id'              => 'non_plugin_links',
                'type'                  => 'checkbox',
                'title'                 => __( 'Non-plugin Links', 'amazon-auto-links' ),
                'label'                 => __( 'Attempt to convert Amazon product links that are not generated by Amazon Auto Links.', 'amazon-auto-links' ),
                'default'               => true,
            ),
            array(
                'field_id'              => 'api_providers',
                'type'                  => 'checkbox',
                'title'                 => __( 'API Providers', 'amazon-auto-links' ),
                'description'           => __( 'Check API providers you\'d like to use.', 'amazon-auto-links' ),
                'label'                 => array(
                    'cloudflare'     => '<a href="https://www.cloudflare.com/" target="_blacnk">cloudflare</a>',
                    'db-ip.com'      => '<a href="https://db-ip.com" target="_blacnk">db-ip.com</a>',
                    'geoiplookup.io' => '<a href="https://geoiplookup.io" target="_blacnk">geoiplookup.io</a>',
                    'geoplugin.net'  => '<a href="https://www.geoplugin.net" target="_blacnk">geoplugin.net</a>',
                ),
            ),
            array()
        );

    }

    /**
      * Called upon form validation.
      *
      * @callback        filter      'validation_{class name}_{section id}'
      */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        return $aInputs;
    }

}