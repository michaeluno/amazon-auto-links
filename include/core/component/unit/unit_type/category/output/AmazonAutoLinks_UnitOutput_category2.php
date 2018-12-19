<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Creates Amazon product links by category.
 *
 * As Amazon deprecated bestseller feeds, this is an alternative method to retrieve best seller products by category.
 *
 * @package     Amazon Auto Links
 * @sicne  3.8.1
 */
class AmazonAutoLinks_UnitOutput_category2 extends AmazonAutoLinks_UnitOutput_url {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'category';

    /**
     * The alternative for the `$_aRSSURLs` property.
     * @since   3.8.1
     * @var array
     */
    protected $_aPageURLs = array();

    /**
     * The alternative for the `$_aExcludingRSSURLs` property.
     * @var array
     * @since   3.8.1
     */
    protected $_aExcludingPageURLs = array();


    /**
     * Fetches and returns the associative array containing the output of product links.
     *
     * If the first parameter is not given,
     * it will determine the RSS urls by the post IDs from the given arguments set in the constructor.
     * @return array
     * @since  3.8.1    Rewritten for the deprecation change on the Amazon stores.
     */
//    public function fetch( $aURLs=array() ) {
//return array();
//        $_oFetch new AmazonAutoLinks_UnitOutput__Fetch_category(
//            $aURLs,
//            $this->_aPageURLs,
//            $this->_aExcludingPageURLs,
//            $this->oUnitOption
//        );
//        return $_oFetch->get();
//    }

    /**
     * Performs API requests and get responses.
     *
     * @since       3.8.1
     * @return      array
     */
    protected function _getResponses( array $aURLs=array() ) {

        $this->___setASINsToExclude();
        return parent::_getResponses( $aURLs );

    }
        /**
         * @since   3.8.1
         */
        private function ___setASINsToExclude() {

            if ( empty( $this->_aExcludingPageURLs ) ) {
                return;
            }
            // First find out ASINs to exclude
            $_aHTMLs          = $this->_getHTMLBodies( $this->_aExcludingPageURLs );
            $_aASINsToExclude = $this->_getFoundItems( $_aHTMLs );
            // Merge with the black list ASINs array
            $this->oUnitProductFilter->aBlackListASINs = array_merge(
                $this->oUnitProductFilter->aBlackListASINs,
                $_aASINsToExclude
            );

        }

    /**
     * Returns the subject urls for this unit.
     * @scope   protected   the category2 unit output class extends this method to set own URLs.
     * @param   $asURLs
     * @since   3.8.1
     * @return  array
     */
    protected function _getURLs( $asURLs ) {
        $_aURLs    = parent::_getURLs( $asURLs );
        $_aAllURLs = array();
        foreach( $_aURLs  as $_sURL ) {
            foreach ( $this->oUnitOption->get( 'feed_type' ) as $_sSlug => $_bEnabled ) {
                if ( ! $_bEnabled ) {
                    continue;
                }
                $_aAllURLs[] = str_replace(
                    array( '/gp/bestsellers/', '/gp/top-sellers/' ),
                    "/gp/{$_sSlug}/",
                    $_sURL
                );
            }
        }
        $_aURLs = array_unique(
            array_merge( $_aAllURLs, $this->_aPageURLs )
        );
        return $_aURLs;

    }

    /**
     * Sets up properties. Called at the end of the constructor.
     *
     * @remark      The 'tag' unit type will override this method.
     */
    protected function _setProperties() {

        $this->_aPageURLs          = wp_list_pluck( $this->oUnitOption->get( array( 'categories' ), array() ), 'page_url' );
        $this->_aExcludingPageURLs = wp_list_pluck( $this->oUnitOption->get( array( 'categories_exclude' ), array() ), 'page_url' );

        // The `url` unit type (which this `category` unit type extends) uses this key, `_sort`, not `sort`.
        $this->oUnitOption->set( '_sort', $this->oUnitOption->get( 'sort' ) );
        parent::_setProperties();


    }

}