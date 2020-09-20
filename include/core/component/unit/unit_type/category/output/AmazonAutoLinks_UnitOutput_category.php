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
 * Creates Amazon product links by category.
 * 
 * @package     Amazon Auto Links
 * @filter      apply       aal_filter_description_node
 *  first parameter:    the description node
 *  second parameter:   the AmazonAutoLinks_Core object
 * 
 * @since       unknown
 * @since       3           Changed the name from `AmazonAutoLinks_UnitOutput_Category`.
 * @since       3.8.1       deprecated
 * @since       3.9.0       Serves as a base class for `AmazonAutoLinks_UnitOutput_category3`
 */
class AmazonAutoLinks_UnitOutput_category extends AmazonAutoLinks_UnitOutput_Base_ElementFormat {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'category';

    public static $aStructure_Product = array(
        'thumbnail_url'         => null,
        'ASIN'                  => null,
        'product_url'           => null,
        'raw_title'             => null,
        'title'                 => null,
        'description'           => null,    // the formatted feed item description - some elements are removed 
        'text_description'      => null,    // the non-html description
            
        // 3+
        'formatted_price'       => null, // 4.0.0+ (string|null) HTML formatted price. Changed from the name, `price` to be compatible with merged database table column key names.
        'review'                => null,
        'formatted_rating'      => null, // 4.0.0+ Changed from `rating` to distinguish from the database table column key name
        'image_set'             => null,
        'button'                => null,

        // 3.8.11+
        'proper_price'          => null,

        // used for disclaimer
        'updated_date'          => null,    // the date posted - usually it's the updated time of the feed at Amazon so it's useless
        
        // 3.3.0
        'content'               => null,
        'meta'                  => null,
        'similar_products'      => null,

        // 3.8.0
        'category'              => null,
        'feature'               => null,
        'sales_rank'            => null,

        // 3.9.0
        'is_prime'              => null,

        // 4.1.0
        'author'                => null,
    );
    
    /**
     * Stores rss urls to fetch.
     */
    protected $_aRSSURLs = array();
    
    /**
     * Stores rss urls to fetch and exclude the items from the result.
     */
    protected $_aExcludingRSSURLs = array();

    public function get( $aURLs=array(), $sTemplatePath=null ) {
        return parent::get( $aURLs );
    }

    /**
     * Sets up properties.
     * @param array|AmazonAutoLinks_UnitOption_Base $aoUnitOptions
     */
    public function __construct( $aoUnitOptions=array() ) {

        $this->___setProperties();
        parent::__construct( $aoUnitOptions );

    }
        /**
         * Called before any properties are set.
         */
        private function ___setProperties() {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            if ( $_oOption->isAPIConnected() ) {
                $this->_aItemFormatDatabaseVariables[] = '%description%'; // this might be wrong and should be removed -> not wrong as it is updated in `replyToFormatProductWithDBRow()`.
                $this->_aItemFormatDatabaseVariables[] = '%content%';
                $this->_aItemFormatDatabaseVariables[] = '%feature%';   // 3.8.0
                $this->_aItemFormatDatabaseVariables[] = '%category%';  // 3.8.0
                $this->_aItemFormatDatabaseVariables[] = '%rank%';      // 3.8.0
                $this->_aItemFormatDatabaseVariables[] = '%prime%';     // 3.9.0
            }
        }

    /**
     * Sets up properties. Called at the end of the constructor.
     *
     */
    protected function _setProperties() {

        $this->_aRSSURLs          = $this->___getRSSURLsFromArguments(
            $this->oUnitOption->get( array( 'categories' ), array() )
        );

        $this->_aExcludingRSSURLs = $this->___getRSSURLsFromArguments(
            $this->oUnitOption->get( array( 'categories_exclude' ), array() )
        );

    }
        /**
         * @param   array $aCategories
         * @return  array
         */
        private function ___getRSSURLsFromArguments( array $aCategories ) {
            $_aRSSURLs = array();
            foreach( $aCategories as $_aCategory ) {
                $_aRSSURLs[] = $_aCategory[ 'feed_url' ];
            }
            return $_aRSSURLs;                
        }

    /**
     * Called when the unit has access to the plugin custom database table.
     *
     * Sets the 'content' and 'description' elements in the product (item) array which require plugin custom database table.
     *
     * @param array $aProduct
     * @param array $aDBRow
     * @param array $aScheduleIdentifier
     * @return      array
     * @callback    add_filter      aal_filter_unit_each_product_with_database_row
     * @since       3.3.0
     */
    public function replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier=array() ) {

        remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFormatProductWithDBRow' ), 10 );

        if ( empty( $aProduct ) ) {
            return array();
        }

        $aProduct[ 'content' ]      = $this->_getContents( $aProduct, $aDBRow, $aScheduleIdentifier );
        $_sDescriptionExtracted     = $this->_getDescriptionSanitized(
            $aProduct[ 'content' ],
            $this->oUnitOption->get( 'description_length' ),
            $this->_getReadMoreText( $aProduct[ 'product_url' ] )
        );

        $_sDescriptionExtracted     = $_sDescriptionExtracted
            ? "<div class='amazon-product-description'>"
                . $_sDescriptionExtracted
            . "</div>"
            : '';
        $_sDescription              = ( $aProduct[ 'description' ] || $_sDescriptionExtracted )
            ? trim( $aProduct[ 'description' ] . " " . $_sDescriptionExtracted ) // only the meta is added by default
            : ''; // 3.10.0 If there is no description, do not even add the div element, which cause an extra margin as a block element.
        $aProduct[ 'description' ]  = $_sDescription;
        return $aProduct;
    
    }
        
    /**
     * @return      string
     * @since       3.3.0
     * @param       array $aProduct
     */
    protected function _getContents( $aProduct /*, $aDBRow, $aScheduleIdentifier */ ) {
        
        $_aParams            = func_get_args();
        $aProduct            = $_aParams[ 0 ];
        $aDBRow              = $_aParams[ 1 ];
        $aScheduleIdentifier = $_aParams[ 2 ];

        $_oRow = new AmazonAutoLinks_UnitOutput___Database_Product(
            $aScheduleIdentifier[ 'asin' ],
            $aScheduleIdentifier[ 'locale' ],
            $aScheduleIdentifier[ 'associate_id' ],
            $aDBRow,
            $this->oUnitOption
        );

        $_ansReviews = $_oRow->getCell( 'editorial_reviews', array() );
        if ( $this->___hasEditorialReviews( $_ansReviews ) ) {
            $_oContentFormatter = new AmazonAutoLinks_UnitOutput__Format_content(
                $_ansReviews,
                $this->oDOM,
                $this->oUnitOption
            );
            $_sContents = $_oContentFormatter->get();
            return "<div class='amazon-product-content'>"
                    . $_sContents
                . "</div>";
        }
        $_snFeatures = $_oRow->getCell( 'features', '' );
        return $_snFeatures
            ? "<div class='amazon-product-content'>"
                . $_snFeatures
            . "</div>"
            : '';

    }
        /**
         * For backward compatibility of a case that still the editorial reviews are stored in the cache.
         * @param  $anReviews
         * @return bool
         * @since  3.10.0
         */
        private function ___hasEditorialReviews( $anReviews ) {
            // if null, the product data is not inserted in the plugin's database table.
            if ( is_null( $anReviews ) ) {
                return false;
            }

            if ( is_string( $anReviews ) && $anReviews ) {
                return true;
            }
            return is_array( $anReviews );
        }

        /**
         * Converts the sort order for the RSS client property.
         * @since       3
         * @return      string
         * @since       3.9.3       Changed the scope to `protected`.
         */
        protected function _getSortOrder() {
            
            // random', // date, title, title_descending    
            $_sSortOrder = $this->oUnitOption->get( 'sort' );
            switch( $_sSortOrder ) {
                case 'raw':
                    return 'raw';
                case 'date':
                    return 'date_descending';
                case 'title':
                    return 'title_ascending';
                case 'title_descending':
                case 'random':
                    return $_sSortOrder;
                default:
                    return 'random';
            }

        }

}