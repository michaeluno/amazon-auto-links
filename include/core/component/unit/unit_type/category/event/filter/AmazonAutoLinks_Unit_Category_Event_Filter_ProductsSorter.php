<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Sort products array.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_Category_Event_Filter_ProductsSorter extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base {

    public $sUnitType = 'category';

    public $iHookPriority = 100;        // must be later than `AmazonAutoLinks_Unit_Category_Event_Filter_ProductsFetcher`.

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {
        $_oSorter     = new AmazonAutoLinks_Unit_Output_Sort(
            $aProducts,
            $this->___getSortOrder( $this->oUnitOutput->oUnitOption->get( 'sort' ) )
        );
        return $_oSorter->get();
    }
        /**
         * Gets the sort type.
         *
         * ### Accepted Values
         * 'title'             => __( 'Title', 'amazon-auto-links' ),
         * 'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
         * 'random'            => __( 'Random', 'amazon-auto-links' ),
         * 'raw'               => __( 'Raw', 'amazon-auto-links' ),
         *
         * @since  3
         * @since  3.9.3  Changed the visibility to `protected`.
         * @since  4.3.4  Changed the visibility to `private` as unused except by this class.
         * @since  5.0.0  Changed the parameter to `$sSortType` from `$oUnitOutput`.
         * @return string
         */
        private function ___getSortOrder( $sSortType ) {
            switch( $sSortType ) {
                case 'raw':
                    return 'raw';
                case 'date':
                    return 'date_descending';
                case 'title':
                    return 'title_ascending';
                case 'title_descending':
                case 'random':
                default:
                    return 'random';
            }
        }

}