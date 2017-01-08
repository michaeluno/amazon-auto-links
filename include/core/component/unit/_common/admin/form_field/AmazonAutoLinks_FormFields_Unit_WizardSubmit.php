<?php
/**
 * Define form fields of submit fields for unit type wizards.
 *
 * @since           3.5.0
 */
class AmazonAutoLinks_FormFields_Unit_WizardSubmit extends AmazonAutoLinks_FormFields_Base {

    protected $_sUnitType = '';

    /**
     * Returns field definition arrays.
     *
     * Pass an empty string to the parameter for meta box options.
     *
     * @return      array
     */
    public function get( $sFieldIDPrefix='' ) {

        $_aFields =  array(
            array(
                'field_id'          => 'transient_id',
                'type'              => 'hidden',
                'hidden'            => true,    // hide the row
                'value'             => $GLOBALS[ 'aal_transient_id' ],

                // Set a custom name attribute without any dimensional key.
                // The form will check $_REQUEST[ 'transient_id' ] to repopulate form data from the transient.
                'attributes'        => array(
                    'name'  => 'transient_id',
                ),
            ),
            array(
                'field_id'          => 'submit_tag_unit',
                'type'              => 'submit',
                'save'              => false,
                'value'             => __( 'Create', 'amazon-auto-links' ),
                'label_min_width'   => 0,
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),
                'redirect_url'      => add_query_arg(
                    array(
                        'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    ),
                    admin_url( 'edit.php' )
                ),
            ),
        );
        if ( $this->_sUnitType ) {
            $_aFields[] = array(
                'field_id'          => 'unit_type',
                'type'              => 'hidden',
                'value'             => $this->_sUnitType,
                'hidden'            => true,   // hides the field row as well.
            );
        }
        return $_aFields;
    }

}