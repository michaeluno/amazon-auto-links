<?php
abstract class AmazonAutoLinks_MetaBox_TagOptions_ extends AmazonAutoLinks_AdminPageFramework_MetaBox {

    public function setUp() {
            
        $oCategoryOptionFields = new AmazonAutoLinks_Form_Tag;
        foreach( $oCategoryOptionFields->getFields( 'tag', '' ) as $arrField ) {
            
            // remove the prefix for the field ID
            $arrField['strFieldID'] = preg_replace( '/^category_/', '', $arrField['strFieldID'] );
            
            if ( $arrField['strFieldID'] == 'unit_title' ) continue;
            
            // remove the section key because meta box don't use it. ( it is only necessary for Settings API for admin pages. )
            unset( $arrField['strSectionID'] );
            
            $this->addSettingField( $arrField );
            
        }
            
        // Additional fields.
        $this->addSettingFields(
            array(
                'strFieldID'        => 'unit_type',
                'strType'            => 'hidden',
                'vValue'            => 'category',
            ),                                                                
            array(
                'strFieldID'        => 'cache_duration',
                'strTitle'            => __( 'Cache Duration', 'amazon-auto-links' ),
                'strDescription'    => __( 'The cache lifespan in seconds. For no cache, set 0.', 'amazon-auto-links' ) . ' ' . __( 'Default:', 'amazon-auto-links' ) . ': 1200',
                'strType'            => 'number',
                'vDefault'            => 60 * 20,    // 20 minutes
            ),            
            array()
        );
        
    }
    
    
    public function validation_AmazonAutoLinks_MetaBox_TagOptions( $arrInput, $arrOriginal ) {    // validation_ + extended class name
// AmazonAutoLinks_Debug::logArray( $arrOriginal );            
// AmazonAutoLinks_Debug::logArray( $arrInput );            
        $arrInput['count'] = $this->oUtil->fixNumber( 
            $arrInput['count'],     // number to sanitize
            10,     // default
            1,         // minimum
            $GLOBALS['oAmazonAutoLinks_Option']->getMaximumProductLinkCount() // max
        );
        $arrInput['image_size'] = $this->oUtil->fixNumber( 
            $arrInput['image_size'],     // number to sanitize
            160,     // default
            0,         // minimum
            500     // max
        );                
        
        return $arrInput;
        
    }


}