<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
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
     * $_sOutput = AmazonAutoLinks_Output::getInstance()->render();
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
    public function __construct( $aArguments ) {
        $this->aArguments = $this->___getArgumentsFormatted( $aArguments );
    }
        /**
         * @since       3.4.9
         * @return      array
         */
        private function ___getArgumentsFormatted( $aArguments ) {
            return $this->getAsArray( $aArguments ); 
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
        return "<div class='amazon-auto-links'>"
                . $this->___getOutput()
            . "</div>";
    }
        /**
         * @since       3.5.0
         * @return      string
         */
        private function ___getOutput() {

            $_aIDs    = $this->_getUnitIDs();

            // For cases without a unit
            if ( empty( $_aIDs ) ) {
                return $this->_getOutputByDirectArguments( $this->aArguments );
            }

            // If called by unit,
            $_sOutput = '';
            foreach( $_aIDs as $_iID ) {
                $_sOutput .= $this->_getOutputByID( $_iID );
            }
            return trim( $_sOutput );

        }
        /**
         * @deprecated      3
         */
        public function getOutput() {
            return $this->get();
        }
        
        /**
         * @return      array
         * @since       3.4.7
         */
        private function _getUnitIDs() {
            
            $_aIDs = array();
            
            // The id parameter - the id parameter can accept comma delimited ids.
            if ( isset( $this->aArguments[ 'id' ] ) ) {
                $_aIDs = $this->___getIDsFormatted( $this->aArguments[ 'id' ] );
            }
                
            // The label parameter.
            if ( isset( $this->aArguments[ 'label' ] ) ) {
                
                $this->aArguments[ '_labels' ] = $this->getStringIntoArray( 
                    $this->aArguments['label'], 
                    "," 
                );
                $_aIDs = array_merge(
                    $this->_getPostIDsByLabel( 
                        $this->aArguments[ '_labels' ], 
                        $this->getElement( $this->aArguments, 'operator' )
                    ), 
                    $_aIDs 
                );
                
            }
            return array_unique( $_aIDs );
            
        }        
            /**
             * Formates the `id` argument.
             * @since       3.4.9
             * @return      array
             */
            private function ___getIDsFormatted( $aisIDs ) {                    
                if ( is_scalar( $aisIDs ) ) {
                    return $this->getStringIntoArray( $aisIDs, ',' );
                } 
                // The Auto-insert feature passes ids with an array.
                if ( is_array( $aisIDs ) ) {
                    return $aisIDs;
                }
                return $this->getAsArray( $aisIDs );
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
             * @remark      When the arguments are passed via shortcodes, the keys get all converted to lower-cases by the WordPress core.
             */
            private function _getUnitTypeFromArguments( $aArguments ) {
                return isset( $aArguments[ 'unit_type' ] )
                    ? $aArguments[ 'unit_type' ]
                    : apply_filters( 'aal_filter_detected_unit_type_by_arguments', 'unknown', $aArguments );
            }
            
            /**
             * 
             * @return      string      The unit output
             */
            private function _getOutputByUnitType( $sUnitType, $_aUnitOptions ) {

                $_aRegisteredUnitTypes = $this->getAsArray( apply_filters( 'aal_filter_registered_unit_types', array() ) );
                if ( in_array( $sUnitType, $_aRegisteredUnitTypes ) ) {
                    return apply_filters( 'aal_filter_unit_output_' . $sUnitType, '', $_aUnitOptions );
                }

                $_oOption  = AmazonAutoLinks_Option::getInstance();
                $_sMessage = AmazonAutoLinks_Registry::NAME . ': ' . __( 'Could not identify the unit type. Please make sure to update the auto-insert definition if you have deleted the unit.', 'amazon-auto-links' );
                return $_oOption->isDebug()
                    ? "<p>" . __( 'Debug', 'amazon-auto-links' ) . ': ' . $_sMessage . "</p>"
                    : "<!-- "  . $_sMessage . " -->";

            }
            
        /**
         * Retrieves the post (unit) IDs by the given unit label.
         * @return      array
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
             * @return      array
             */
            private function _getPostIDsByTag( $aTermSlugs, $sFieldType='slug', $sOperator='AND' ) {

                if ( empty( $aTermSlugs ) ) { 
                    return array(); 
                }
                    
                $_aPostIDs = get_posts( 
                    array(
                        'post_type'         => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],    
                        'posts_per_page'    => -1, // ALL posts
                        'fields'            => 'ids',
                        'tax_query'         => array(
                            array(
                                'taxonomy'  => AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ],
                                'field'     => $this->_sanitizeFieldKey( $sFieldType ),    // id or slug
                                'terms'     => $aTermSlugs, // the array of term slugs
                                'operator'  => $this->_sanitizeOperator( $sOperator ),    // 'IN', 'NOT IN', 'AND. If the item is only one, use AND.
                            )
                        )
                    )
                );
                return $_aPostIDs;
                
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
