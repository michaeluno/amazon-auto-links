<?php
/**
 * Provides the definitions of form fields for the category type unit.
 *
 * @remark The admin page and meta box access it.
 * @since  5.0.0
 */
class AmazonAutoLinks_FormFields_AdWidgetSearchUnit_Main extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='' ) {
        $_aSupportedLocales = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport();
        $_aLabels           = $this->___getLocaleLabels( $_aSupportedLocales );
        return array(
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'value'         => 'ad_widget_search',
                'hidden'        => true,
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                'type'          => 'text',
                'description'   => 'e.g. <code>My Search Unit</code>',
                'value'         => '',    // a previous value should not appear
                'class'         => array(
                    'input' => 'width-full',
                    'field' => 'width-half',
                ),
            ),
            array(
                'field_id'      => 'country',
                'title'         => __( 'Country', 'amazon-auto-links' ),
                'type'          => 'select2',
                'label'         => $_aLabels,
                'icon'          => $this->getLocaleIcons( array_keys( $_aLabels ) ),
                'description'   => AmazonAutoLinks_Message::getLocaleFieldGuide() . ' ' . AmazonAutoLinks_Message::get( 'locale_field_tip_paapi' ),
                'default'       => 'US',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'Keywords',
                'type'          => 'text',
                'title'         => __( 'Search Keyword', 'amazon-auto-links' ),
                'attributes'    => array(
                    'size'          => version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' )
                        ? 40
                        : 60,
                ),
                'tip'           => array(
                    __( 'Enter the keyword to search.', 'amazon-auto-links' ),
                    __( 'For multiple items, separate them by commas.', 'amazon-auto-links' )
                        . ' e.g. <code>WordPress, PHP</code>',
                ),
                'class'         => array(
                    'input' => 'width-full',
                    'field' => 'width-two-third',
                ),
            ),
            array(
                'field_id'      => 'sort',
                'title'         => __( 'Sort Order', 'amazon-auto-links' ),
                'type'          => 'select',
                'label'         => array(
                    'raw'               => __( 'Raw', 'amazon-auto-links' ),
                    'title'             => __( 'Title', 'amazon-auto-links' ),
                    'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
                    'random'            => __( 'Random', 'amazon-auto-links' ),
                ),
                'default'       => 'raw',
            ),
        );
    }
        /**
         * @return string[]
         * @since  5.0.0
         */
        private function ___getLocaleLabels( array $aSupportedLocales ) {
            return array_intersect_key(
                $this->getLocaleFieldLabels(),
                array_combine( $aSupportedLocales, $aSupportedLocales )
            );
        }

}