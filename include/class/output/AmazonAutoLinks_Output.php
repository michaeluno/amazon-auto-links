<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Handles unit outputs.
 * 
 * @package     Amazon Auto Links
 * @since       2
 * @since       3       Changed the name from `AmazonAutoLinks_Units`
*/
class AmazonAutoLinks_Output extends AmazonAutoLinks_WPUtility {
    
    /**
     * Stores unit arguments.
     */
    public $aArguments = array();
    
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
    static public function getInstance( $aArguments ) {
        return new self( $aArguments );
    }

    /**
     * Sets up properties.
     * 
     * @since       2.0.0
     */
    function __construct( $aArguments ) {
        $this->aArguments = $aArguments;
    }

    /**
     * Renders the output.
     * @return      void
     */
    public function render() {
        echo $this->get();
    }
    
    /**
     * Retrieves the output.
     * @since       2
     * @since       3       Changed the name from `getOutput()`.
     * @return      string
     */
    public function get() {

        // Retrieve IDs 
        $_aIDs = array();

        // The id parameter - the id parameter can accept comma delimited ids.
        if ( isset( $this->aArguments['id'] ) ) {
            if ( is_string( $this->aArguments['id'] ) || is_integer( $this->aArguments['id'] ) ) {
                $_aIDs = array_merge( 
                    $this->convertStringToArray( 
                        $this->aArguments[ 'id' ], 
                        "," 
                    ), 
                    $_aIDs 
                );
            } else if ( is_array( $this->aArguments['id'] ) ) {
                // The Auto-insert feature passes the id as array.
                $_aIDs = $this->aArguments[ 'id' ];
            }
        }
            
        // The label parameter.
        if ( isset( $this->aArguments[ 'label' ] ) ) {
            
            $this->aArguments[ '_labels' ] = $this->convertStringToArray( 
                $this->aArguments['label'], 
                "," 
            );
            $_aIDs = array_merge(
                $this->_getPostIDsByLabel( 
                    $this->aArguments[ '_labels' ], 
                    isset( $this->aArguments[ 'operator' ] ) 
                        ? $this->aArguments[ 'operator' ] 
                        : null 
                ), 
                $_aIDs 
            );
            
        }

        $_aOutputs  = array();
        $_aIDs      = array_unique( $_aIDs );

        // If called by unit,
        if ( ! empty( $_aIDs ) ) {
            foreach( $_aIDs as $_iID ) {            
                $_aOutputs[] = $this->_getOutputByID( $_iID );
            }
        } 
        // there are cases called without a unit 
        else {
            $_aOutputs[] = $this->_getOutputByDirectArguments( $this->aArguments );            
        }
    
        return trim( implode( '', $_aOutputs ) );

    }

        /**
         * 
         * @deprecated      3
         */
        public function getOutput() {
            return $this->get();
        }
        /**
         * Returns the unit output by post (unit) ID.
         */
        private function _getOutputByID( $iPostID ) {

            /**
             * The auto-insert sets the 'id' as array storing multiple ids. But this method is called per ID so the ID should be discarded.
             * if the unit gets deleted, auto-insert causes an error for not finding the options.
             */            
            $_aUnitOptions = array(
                    'id' => $iPostID,
                )
                + $this->aArguments
                + $this->getPostMeta( $iPostID )
                + array( 
                    'unit_type' => null,
                );    

            return $this->_getOutputByUnitType( 
                $_aUnitOptions[ 'unit_type' ],
                $_aUnitOptions 
            );
   
        }

        /**
         * 
         * @since       3
         * @return      string
         */
        private function _getOutputByDirectArguments( $aArguments ) {
            return $this->_getOutputByUnitType( 
                $this->_getUnitTypeFromArguments( $aArguments ), 
                $aArguments 
            );
        }
            /**
             * Determines the unit type from the given argument array.
             * @since       3
             * @return      string      The unit type slug.
             */
            private function _getUnitTypeFromArguments( $aArguments ) {
                
                if ( isset( $aArguments[ 'Operation' ] ) ) {
                        
                    if ( 'ItemSearch' === $aArguments[ 'Operation' ] ) {
                        return 'search';
                    }
                    if ( 'ItemLookup' === $aArguments[ 'Operation' ] ) {
                        return 'item_lookup';
                    }
                    if ( 'SimilarityLookup' === $aArguments[ 'Operation' ] ) {
                        return 'similarity_lookup';
                    }
                }
                if ( isset( $aArguments[ 'categories' ] ) ) {
                    return 'category';
                }                
                if ( isset( $aArguments[ 'tags' ] ) ) {
                    return 'tag';
                }
                if ( isset( $aArguments[ 'urls' ] ) ) {
                    
                }
                return 'unknown';
                
            }
            
            /**
             * 
             * @return      string      The unit output
             */
            private function _getOutputByUnitType( $sUnitType, $_aUnitOptions ) {
                switch ( $sUnitType ) {
                    case 'category':
                    case 'tag':
                    case 'search':
                    case 'item_lookup':
                    case 'similarity_lookup':        
                    case 'url':        
                        $_sClassName = "AmazonAutoLinks_Unit_" . strtolower( $sUnitType );
                        $_oUnit      = new $_sClassName( $_aUnitOptions );
                        return $_oUnit->getOutput();
                    default:
                        $_oOption  = AmazonAutoLinks_Option::getInstance();
                        $_sMessage = AmazonAutoLinks_Registry::NAME . ': ' . __( 'Could not identify the unit type. Please make sure to update the auto-insert definition if you have deleted the unit.', 'amazon-auto-links' );
                        return $_oOption->isDebug()
                            ? "<p>" . __( 'Debug', 'amazon-auto-links' ) . ': ' . $_sMessage . "</p>"
                            : "<!-- "  . $_sMessage . " -->";
                }                        
            }            
            
        /**
         * Retrieves the post (unit) IDs by the given unit label.
         */
        private function _getPostIDsByLabel( $aLabels, $sOperator ) {
            
            // Retrieve the taxonomy slugs of the given taxonomy names.
            $_aTermSlugs = array();
            foreach( ( array ) $aLabels as $_sTermName ) {                
                $_aTerm         = get_term_by( 
                    'name', 
                    $_sTermName, 
                    AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ], 
                    ARRAY_A 
                );
                $_aTermSlugs[]  = $_aTerm[ 'slug' ];
                
            }
            return $this->_getPostIDsByTag( $_aTermSlugs, 'slug', $sOperator );
            
        }
            /**
             * Retrieves post (unit) IDs by the plugin tag taxonomy slug.
             */
            private function _getPostIDsByTag( $aTermSlugs, $sFieldType='slug', $sOperator='AND' ) {

                if ( empty( $aTermSlugs ) ) { 
                    return array(); 
                }
                    
                $_aPostObjects = get_posts( 
                    array(
                        'post_type'         => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],    
                        'posts_per_page'    => -1, // ALL posts
                        'tax_query'         => array(
                            array(
                                'taxonomy'  => AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ],
    // @todo it should be possible to set ID here so that the result only contains post IDs                            
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