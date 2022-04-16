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
 * Provides the definitions of form fields for the category type unit.
 *
 * @remark The admin page and meta box access it.
 * @since  5.2.0
 */
class AmazonAutoLinks_FormFields_AdWidgetSearchUnit_Advanced extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns a field definition array.
     *
     * @since  5.2.0
     * @param  string $sFieldIDPrefix A prefix for field IDs. Pass an empty string to the parameter for meta box options.
     * @return array
     */    
    public function get( $sFieldIDPrefix='' ) {
        $_sLocale    = $this->oFactory->getValue( 'country' );
        $_sLocale    = is_string( $_sLocale ) ? $_sLocale : '';    // in admin-ajax.php, an empty array gets returned
        $_aFieldsets = array(
            array(
                'field_id'      => $sFieldIDPrefix . 'SearchIndex',
                'type'          => 'select',
                'title'         => __( 'Category', 'amazon-auto-links' ),
                'label'         => $_sLocale ? $this->___getSearchIndex( $_sLocale ) : array(),
                'default'       => 'All',
                'tip'           => __( 'Select the category to limit the searching area.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'BrowseNode',
                'type'          => 'number',
                'title'         => __( 'Browse Node', 'amazon-auto-links' ),
                'default'       => '',
                'tip'           => __( 'If you know the browser node ID, enter it here. Otherwise, leave it blank.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'min' => 0,
                ),
            ),
        );
        return $_aFieldsets;
    }
        /**
         * @since  5.2.0
         * @param  string $sLocale
         * @return array
         */
        private function ___getSearchIndex( $sLocale ) {
            $_oLocale = new AmazonAutoLinks_PAAPI50_Locale( $sLocale );
            return $_oLocale->getSearchIndex();
        }


}