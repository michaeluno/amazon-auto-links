<?php
abstract class AmazonAutoLinks_MetaBox_SimilarityLookupOptions_Advanced_ extends AmazonAutoLinks_AdminPageFramework_MetaBox {


    public function setUp() {
            
        $oSearchOptionFields = new AmazonAutoLinks_Form_Search;
        foreach( $oSearchOptionFields->getFieldOfSimilarityLookUpAdvanced( '', '' ) as $arrField ) {
                    
            if ( ! isset( $arrField['strFieldID'] ) || $arrField['strFieldID'] == 'title' ) continue;
            
            // remove the section key because meta box don't use it. ( it is only necessary for Settings API for admin pages. )
            unset( $arrField['strSectionID'] );
            
            $this->addSettingField( $arrField );
            
        }
            
    }    
    
    public function validation_AmazonAutoLinks_MetaBox_ItemLookupOptions_Advanced( $arrInput, $arrOriginal ) {    // validation_ + extended class name
        return $arrInput;        
    }

}