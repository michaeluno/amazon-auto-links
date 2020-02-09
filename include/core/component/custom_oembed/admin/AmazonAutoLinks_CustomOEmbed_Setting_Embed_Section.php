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
 * Adds a form section to an in-page tab.
 *
 * @since       4.0.0
 */
class AmazonAutoLinks_CustomOEmbed_Setting_Embed_Section extends AmazonAutoLinks_AdminPage_Section_Base {

    protected function _getArguments() {
        return array(
            'section_id'    => 'custom_oembed',
            'tab_slug'      => $this->sTabSlug,
            'title'         => __( 'oEmbed', 'amazon-auto-links' ),
            'description'   => array(
                __( 'oEmbed is an open format to provide embedded contents for web pages, which WordPress also natively supports.', 'amazon-auto-links' ),
                __( 'By enabling this feature, simply pasting a product URL starting with <code>http(s)://www.amazon...</code> in the post editor generates Amazon product link outputs.', 'amazon-auto-links' ),
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
                'field_id'              => 'enabled',
                'type'                  => 'radio',
                'title'                 => __( 'Enable', 'amazon-auto-links' ),
//                'select_type'           => 'radio',
                // 'show_title_column'     => false,
                'label'                 => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'selectors'         => array(
                    1   => '#fieldrow-convert_links_where,#fieldrow-convert_links_custom_filter_hooks',
                ),
            ),
            array(
                'field_id'              => 'use_iframe',
                'type'                  => 'revealer',
                'title'                 => __( 'Use iframe', 'amazon-auto-links' ),
                'description'           => __( 'When enabling this option, product links will be displayed in iframe. Otherwise, they will be directly rendered.', 'amazon-auto-links' ),
                'select_type'           => 'checkbox',
                'label'                 => __( 'Show product links in iframe', 'amazon-auto-links' ),
            ),
            array(
                'field_id'       => 'prioritize_associates_id_of_url',
                'type'           => 'checkbox',
                'title'          => __( 'Prioritize Associates ID Used in URL', 'amazon-auto-links' ),
                'label'          => __( 'Respect the one set in the pasted URL in the editor.', 'amazon-auto-links' ),
                'description'    => __( 'For example, if <code>https://amazon.com/dp/B07PCMWTSG?tag=myasscoaiteid-21</code> is pasted in the post editor, the ID, <code>myassociateid-21</code> will be used. To use a preset ID, set it in the below option.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'       => 'associates_ids',
                'type'           => 'text',
                'title'          => __( 'Associates IDs', 'amazon-auto-links' ),
                'label'          => $this->___getCountryLabels(),
                'description'    => __( 'When an Associates ID is not found in the pasted URL in the editor, the value set in this option will be used unless the above option is enabled.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'              => 'external_provider',
                'type'                  => 'url',
                'title'                 => __( 'External oEmbed Provider' ),
                'description'           => array(
                   __( "Have another WordPress site with this plugin's oEmbed option enabled and specify the URL here so that the embedded contents will be rendered in that site and it saves load on this site.", 'amazon-auto-links' ),
                   __( 'Leave it blank to make the site itself as the provider.', 'amazon-auto-links' ),
                ),
                'attributes'            => array(
                    'style' => 'min-width: 400px;'
                ),
            ),
            array()
        );

    }
        private function ___getCountryLabels() {
            $_aLabels = AmazonAutoLinks_Property::$aStoreDomains;
            foreach( $_aLabels as $_sLocale => $_sDomain ) {
                $_sFlagImage = AmazonAutoLinks_Property::$aCountryFlags[ $_sLocale ];
                $_aLabels[ $_sLocale ] = "<img class='country-flag' src='{$_sFlagImage}' /><span class='store-domain'>" . $_sDomain . "</span>";
            }
            return $_aLabels;
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