<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
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
     * Stores the raw arguments.
     * @remark      Used for JavaScript loading.
     * @var         array
     * @sicne       3.6.0
     */
    private $___aRawArguments = array();

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
        $this->___aRawArguments = $aArguments; // 3.6.0+
        $_oFormatter            = new AmazonAutoLinks_Output___ArgumentFormatter( $aArguments );
        $this->aArguments       = $_oFormatter->get();
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

        // 3.6.0+
        $_oAjaxOutput = new AmazonAutoLinks_Output___Ajax( $this->aArguments, $this->___aRawArguments );
        $_sOutputForAjax = $_oAjaxOutput->get();
        if ( $_sOutputForAjax ) {
            return $_sOutputForAjax;
        }

        $_sOutput = $this->___getOutput();
        $_bNoOuterContainer = $this->getElement( $this->aArguments, array( '_no_outer_container' ) );
        $_bNoOuterContainer = apply_filters( 'aal_filter_output_is_without_outer_container', $_bNoOuterContainer, $this->aArguments );
        return $_bNoOuterContainer
            ? $_sOutput
            : "<div class='amazon-auto-links'>" . $_sOutput . "</div>";
    }

        /**
         * @since       3.5.0
         * @return      string
         */
        private function ___getOutput() {

            $_aIDs    = $this->getAsArray( $this->aArguments[ '_unit_ids' ] );

            // For cases without a unit
            if ( empty( $_aIDs ) ) {
                // By direct arguments
                return $this->___getOutputByUnitType(
                    $this->___getUnitTypeFromArguments( $this->aArguments ),
                    $this->aArguments
                );
            }

            // If called by unit IDs,
            $_sOutput = '';
            foreach( $_aIDs as $_iID ) {
                $_sOutput .= $this->___getOutputByID( $_iID );
            }
            return $_sOutput;

        }

        /**
         * Returns the unit output by post (unit) ID.
         * @return      string
         */
        private function ___getOutputByID( $iPostID ) {

            /**
             * The auto-insert sets the 'id' as array storing multiple ids.
             * But this method is called per ID so the ID should be discarded.
             * If the unit gets deleted, auto-insert causes an error for not finding the options.
             */            
            $_aUnitOptions = array(
                    'id' => $iPostID,
                )
                + $this->aArguments
                + $this->getPostMeta( $iPostID )
                + array( 
                    'unit_type' => null,
                );    

            return $this->___getOutputByUnitType(
                $_aUnitOptions[ 'unit_type' ],
                $_aUnitOptions
            );
   
        }

            /**
             * Determines the unit type from the given argument array.
             * @since       3
             * @return      string      The unit type slug.
             * @remark      When the arguments are passed via shortcodes, the keys get all converted to lower-cases by the WordPress core.
             */
            private function ___getUnitTypeFromArguments( $aArguments ) {
                return isset( $aArguments[ 'unit_type' ] )
                    ? $aArguments[ 'unit_type' ]
                    : apply_filters( 'aal_filter_detected_unit_type_by_arguments', 'unknown', $aArguments );
            }
            
            /**
             * Generates product outputs by the given unit type.
             *
             * @remark      All the outputs go through this method.
             * @return      string      The unit output
             */
            private function ___getOutputByUnitType( $sUnitType, $_aUnitOptions ) {

                $_aRegisteredUnitTypes = $this->getAsArray( apply_filters( 'aal_filter_registered_unit_types', array() ) );
                if ( in_array( $sUnitType, $_aRegisteredUnitTypes ) ) {
                    /**
                     * Each unit type hooks into this filter hook and generates their outputs.
                     */
                    return trim( apply_filters( 'aal_filter_unit_output_' . $sUnitType, '', $_aUnitOptions ) );
                }

                // For undefined unit types,
                $_oOption  = AmazonAutoLinks_Option::getInstance();
                $_sMessage = AmazonAutoLinks_Registry::NAME . ': ' . __( 'Could not identify the unit type. Please make sure to update the auto-insert definition if you have deleted the unit.', 'amazon-auto-links' );
                return $_oOption->isDebug()
                    ? "<h3>" . __( 'Debug', 'amazon-auto-links' ) . "</h3>"
                        . "<p>" . $_sMessage . "</p>"
                        . "<h4>" . __( 'Unit Arguments', 'amazon-auto-links' ) . "</h4>"
                        . "<div>"
                            . AmazonAutoLinks_Debug::get( $_aUnitOptions )
                        . "</div>"
                    : "";

            }

    /* Deprecated Methods */

    /**
     * @deprecated      3
     */
    public function getOutput() {
        return $this->get();
    }

}
