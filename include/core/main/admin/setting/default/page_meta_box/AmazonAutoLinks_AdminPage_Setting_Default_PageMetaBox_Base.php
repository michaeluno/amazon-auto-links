<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */
 

/**
 * @since      3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base extends AmazonAutoLinks_PageMetaBox_Base {

    /**
     * @var   array Stores field definition classes.
     * @since 5.2.0
     */
    protected $_aFieldClasses = array();

    /**
     * Stores the section id for the unit default option dimension.
     */
    protected $_sSectionID = 'unit_default';
      
    public function start() {
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
    }

    /**
     * Sets up form fields.
     */
    public function setUp() {

        $this->addSettingSections(
            array(
                'section_id'    => $this->_sSectionID,
            )
        );
        $this->addSettingFields( $this->_sSectionID );

        foreach( $this->_aFieldClasses as $_sClassName ) {
            $_oFields = new $_sClassName( $this );
            $_aFields = $_oFields->get();
            foreach( $_aFields as $_aField ) {
                $this->addSettingFields( $_aField );
            }
        }

    }

}