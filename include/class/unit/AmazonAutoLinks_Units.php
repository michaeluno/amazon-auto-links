<?php
/**
 * Handles unit outputs.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
*/
class AmazonAutoLinks_Units {
    
    /**
     * Instantiates the class and returns the object.
     * 
     * This is to enable a technique to call a method in one line like
     * <code>
     * $_sOutput = AmazonAutoLinks_Units::getInstance()->render();
     * </code>
     * 
     * @sicne       2.1.1
     */
    static public function getInstance( $aArgs ) {
        return new self( $aArgs );
    }

    /**
     * Sets up properties.
     * 
     * @since       2.0.0
     */
    function __construct( $aArgs ) {
        $this->aArgs = $aArgs;
    }

    public function render() {
        echo $this->getOutput();
    }
    
    public function getOutput() {
            
        // Retrieve IDs 
        $_aIDs = array();

        // The id parameter - the id parameter can accept comma delimited ids.
        if ( isset( $this->aArgs['id'] ) ) {
            if ( is_string( $this->aArgs['id'] ) || is_integer( $this->aArgs['id'] ) ) {
                $_aIDs = array_merge( AmazonAutoLinks_Utilities::convertStringToArray( $this->aArgs['id'], "," ), $_aIDs );
            } else if ( is_array( $this->aArgs['id'] ) ) {
                $_aIDs = $this->aArgs['id'];    // The Auto-insert feature passes the id as array.
            }
        }
            
        // The label parameter.
        if ( isset( $this->aArgs['label'] ) ) {
            
            $this->aArgs['_labels'] = AmazonAutoLinks_Utilities::convertStringToArray( $this->aArgs['label'], "," );
            $_aIDs = array_merge( $this->_getPostIDsByLabel( $this->aArgs['_labels'], isset( $arrArgs['operator'] ) ? $arrArgs['operator'] : null ), $_aIDs );
            
        }
            
        $_aOutputs  = array();
        $_aIDs      = array_unique( $_aIDs );
        foreach( $_aIDs as $_iID ) {            
            $_aOutputs[] = $this->_getOutputByID( $_iID );
        }
        
        return implode( '', $_aOutputs );

    }
        
        /**
         * Returns the unit output by post (unit) ID.
         */
        private function _getOutputByID( $iPostID ) {

            $_aUnitOptions = AmazonAutoLinks_Option::getUnitOptionsByPostID( $iPostID );

            /**
             * The auto-insert sets the 'id' as array storing multiple ids. But this method is called per ID so the ID should be discarded.
             */
            $_aSetArgs = $this->aArgs;
            unset( $_aSetArgs['id'] );
            
            $_aUnitOptions = $_aSetArgs + $_aUnitOptions + array( 
                'unit_type' => null,
                'id'        => $iPostID,
            );    // if the unit gets deleted, auto-insert causes an error for not finding the options
    
            switch ( $_aUnitOptions['unit_type'] ) {
                case 'category':
                    $_oAALCat = new AmazonAutoLinks_Unit_Category( $_aUnitOptions );
                    return $_oAALCat->getOutput();
                case 'tag':
                    $_oAALTag = new AmazonAutoLinks_Unit_Tag( $_aUnitOptions );
                    return $_oAALTag->getOutput();
                case 'search':
                    $_oAALSearch = new AmazonAutoLinks_Unit_Search( $_aUnitOptions );
                    return $_oAALSearch->getOutput();
                case 'item_lookup':
                    $_oAALSearch = new AmazonAutoLinks_Unit_Search_ItemLookup( $_aUnitOptions );                
                    return $_oAALSearch->getOutput();
                case 'similarity_lookup':
                    $_oAALSearch = new AmazonAutoLinks_Unit_Search_SimilarityLookup( $_aUnitOptions );                
                    return $_oAALSearch->getOutput();                
                default:
                    return "<!-- " . AmazonAutoLinks_Commons::$strPluginName . ': ' . __( 'Could not identify the unit type. Please make sure to update the auto-insert definition if you have deleted the unit.', 'amazon-auto-links' ) . " -->";
            }        
        }
        
        /**
         * Retrieves the post (unit) IDs by the given unit label.
         */
        private function _getPostIDsByLabel( $aLabels, $sOperator ) {
            
            // Retrieve the taxonomy slugs of the given taxonomy names.
            $_aTermSlugs = array();
            foreach( ( array ) $aLabels as $_sTermName ) {
                
                $_aTerm         = get_term_by( 'name', $_sTermName, AmazonAutoLinks_Commons::TagSlug, ARRAY_A );
                $_aTermSlugs[]  = $_aTerm['slug'];
                
            }

            return $this->_getPostIDsByTag( $_aTermSlugs, 'slug', $sOperator );
            
        }

        /**
         * Rerieves post (unit) IDs by the plugin tag taxonomy slug.
         */
        private function _getPostIDsByTag( $aTermSlugs, $sFieldType='slug', $sOperator='AND' ) {

            if ( empty( $aTermSlugs ) ) { 
                return array(); 
            }
                
            $_aPostObjects = get_posts( 
                array(
                    'post_type'         => AmazonAutoLinks_Commons::PostTypeSlug,    
                    'posts_per_page'    => -1, // ALL posts
                    'tax_query'         => array(
                        array(
                            'taxonomy'  => AmazonAutoLinks_Commons::TagSlug,    
                            'field'     => $this->_sanitizeFieldKey( $sFieldType ),    // id or slug
                            'terms'     => $aTermSlugs, // the array of term slugs
                            'operator'  => $this->_sanitizeOperator( $sOperator ),    // 'IN', 'NOT IN', 'AND. If the item is only one, use AND.
                        )
                    )
                )
            );
            $_aIDs = array();
            foreach( $_aPostObjects as $oPost ) {
                $_aIDs[] = $oPost->ID;
            }
            return array_unique( $_aIDs );
            
        }
            private function _sanitizeFieldKey( $sField ) {
                switch( strtolower( trim( $sField ) ) ) {
                    case 'id':
                        return 'id';
                    default:
                    case 'slug':
                        return 'slug';
                }        
            }
            private function _sanitizeOperator( $sOperator ) {
                switch( strtoupper( trim( $sOperator ) ) ) {
                    case 'NOT IN':
                        return 'NOT IN';
                    case 'IN':
                        return 'IN';
                    default:
                    case 'AND':
                        return 'AND';
                }
            }        
    
}