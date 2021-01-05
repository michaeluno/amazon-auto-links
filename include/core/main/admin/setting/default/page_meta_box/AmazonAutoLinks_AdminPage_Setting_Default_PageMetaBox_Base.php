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
 * @since      3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base extends AmazonAutoLinks_AdminPageFramework_PageMetaBox {
    
    /**
     * Stores the section id for the unit default option dimension.
     */
    protected $_sSectionID = 'unit_default';
      
    public function start() {
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
    }

    /**
     * Adds form fields.
     * @since  3.3.0
     * @param  array $aClassNames
     */
    protected function _addFieldsByClasses( $aClassNames ) {
        foreach( $aClassNames as $_sClassName ) {
            $_oFields = new $_sClassName( $this );
            foreach( $_oFields->get() as $_aField ) {
                $this->addSettingFields( $_aField );
            }
        }
    }

    /**
     * Returns the form stored data.
     * @return mixed
     * @since  4.5.0
     */
    public function getValue() {
        $_aParameters = func_get_args();
        if ( ! isset( $_aParameters[ 0 ] ) ) {
            return $this->oUtil->getElementAsArray( $this->oProp->oAdminPage->oProp->aOptions, array( $this->_sSectionID ) );
        }
        $_mDefault = null;
        if ( is_array( $_aParameters[ 0 ] ) ) {
            $_mDefault    = $this->oUtil->getElement( $_aParameters, 1 );
            $_aParameters = $_aParameters[ 0 ];
            array_unshift( $_aParameters[ 0 ], $this->_sSectionID );
        } else {
            array_unshift( $_aParameters, $this->_sSectionID );
        }
        $_aParameters = array( $this->oProp->oAdminPage->oProp->aOptions, $_aParameters, $_mDefault );
        return call_user_func_array( array( $this->oUtil, 'getElement' ), $_aParameters );
    }

}