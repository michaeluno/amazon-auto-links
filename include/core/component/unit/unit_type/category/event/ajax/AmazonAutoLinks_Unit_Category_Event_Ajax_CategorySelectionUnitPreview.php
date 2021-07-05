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
 * Responds with the data of category list unit preview for the category selection screen.
 *
 * @package      Amazon Auto Links
 * @since        4.2.0
 */
class AmazonAutoLinks_Unit_Category_Event_Ajax_CategorySelectionUnitPreview extends AmazonAutoLinks_Unit_Category_Event_Ajax_CategorySelection {

    protected $_sActionHookName = 'wp_ajax_aal_unit_preview';

    /**
     * The nonce key passed to the `wp_create_nonce()`
     * @var string
     */
    protected $_sNonceKey = 'aalNonceCategorySelection';

    /**
     * @param  array $aPost
     *
     * @return array
     * @throws Exception        Throws a string value of an error message.
     */
    protected function _getResponse( array $aPost ) {

        // Passing the unit options via transient as passing through JS results in escaped characters and causes errors.
        $_aUnitOptions      = $this->_getUnitOptions( $aPost );

        // Additional options for previews
        $_aUnitOptions = array(
            'template_path' => AmazonAutoLinks_Registry::$sDirPath . '/template/preview/template.php',
            'is_preview'    => true, // this disables the global ASIN blacklist.
            'show_errors'   => 1,
        ) + $_aUnitOptions;

        $_sUnitPreview = $this->___getUnitPreview( $aPost, $_aUnitOptions );

        // The JavaScript script receives this response array
        return array(
            'unit_preview' => apply_filters(
                'aal_filter_category_select_unit_preview', $_sUnitPreview, $aPost, $_aUnitOptions
            ),
        );

    }
        private function ___getUnitPreview( array $aPost, array $aUnitOptions ) {

            $_aAddedCategories     = $this->getElementAsArray( $aPost, array( 'urls_added' ) );
            $_aExcludingCategories = $this->getElementAsArray( $aPost, array( 'urls_excluded' ) );
            $aUnitOptions = array(
                'categories'         => $this->___getCategoriesFormatted( $_aAddedCategories ),
                'categories_exclude' => $this->___getCategoriesFormatted( $_aExcludingCategories ),
            ) + $aUnitOptions;

            $_oUnitPreview         = new AmazonAutoLinks_UnitOutput_category( $aUnitOptions );
            return $_oUnitPreview->get();

        }
            /**
             * @param array $aCategories
             *
             * @return array
             */
            private function ___getCategoriesFormatted( array $aCategories ) {
                $_aFormatted = array();
                foreach( $aCategories as $_iIndex => $_sURL ) {
                    $_aFormatted[ md5( $_sURL ) ] = array(
                        'breadcrumb'    => '', // no need to have a valid value just for preview outputs
                        'page_url'      => $_sURL,
                    );
                }
                return $_aFormatted;
            }

}