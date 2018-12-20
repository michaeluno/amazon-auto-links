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
     * Returns the found ASINs from HTML documents.
     * @remark      Excludes ASINs of exclusion categories.
     * @since       3.8.2
     * @return      array
     */
    protected function _getFoundItems( $aHTMLs ) {

        $_aASINs        = parent::_getFoundItems( $aHTMLs );
        $_aExcludeASINs = $this->___getASINs( $this->_aExcludingPageURLs );
        $_aASINsDiff    = array_diff( $_aASINs, $_aExcludeASINs ); // the return value

        // Cover cases with insufficient number of products by checking the paged pages.
        $_iPage     = 2;    // starts from 2
        $_iSetCount = $this->oUnitOption->get( 'count' );
        $_aURLs     = array_keys( $aHTMLs );
        while ( count( $_aASINsDiff ) < $_iSetCount ) {
            $_aURLs             = $this->___getURLsPageIncremented( $_aURLs, $_iPage );
            $_aThisFoundASINs   = $this->___getASINs( $_aURLs );
            if ( empty( $_aThisFoundASINs ) ) {
                break;
            }
            $_aASINs            = array_unique( array_merge( $_aASINs, $_aThisFoundASINs ) );
            $_aExcludeURLs      = $this->___getURLsPageIncremented( $this->_aExcludingPageURLs, $_iPage );
            $_aThisExcludeASINs = $this->___getASINs( $_aExcludeURLs );
            $_aExcludeASINs     = array_unique( array_merge( $_aExcludeASINs, $_aThisExcludeASINs ) );
            $_aASINsDiff        = array_diff( $_aASINs, $_aExcludeASINs ); // the return value
            if ( $_iPage > 10 ) {
                break;
            }
            $_iPage++;
        }
        return $_aASINsDiff;

    }
        private function ___getURLsPageIncremented( array $aURLs, $iPage ) {
            $_aURLs = array();
            foreach( $aURLs as $_iIndex => $_sURL ) {
                $_aURLs[ $_iIndex ] = add_query_arg(
                    array(
                       'pg' => $iPage,
                    ),
                    $_sURL
                );
            }
            return $_aURLs;
        }
        /**
         * @return array
         * @since   3.8.2
         */
        private function ___getASINs( array $aURLs = array() ) {
            if ( empty( $aURLs ) ) {
                return array();
            }
            $_aHTMLs = parent::_getHTMLBodies( $aURLs );
            $_aASINs = parent::_getFoundItems( $_aHTMLs );
            return $_aASINs;
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