<?php
abstract class AmazonAutoLinks_MetaBox_Template_ extends AmazonAutoLinks_AdminPageFramework_MetaBox {

    public function setUp() {
        
        $oForm_Template = new AmazonAutoLinks_Form_Template();    
        call_user_func_array( array( $this, "addSettingFields" ), $oForm_Template->getTemplateFields( null, '', false ) );
        
        // $oTemplates = $GLOBALS['oAmazonAutoLinks_Templates'];
        // $this->addSettingField(            
            // array(
                // 'strFieldID'        => 'template_id',
                // 'strTitle'            => __( 'Select Template', 'amazon-auto-links' ),
                // 'strDescription'    => __( 'Sets a default template for this unit.', 'amazon-auto-links' ),
                // 'vLabel'            => $arr = $oTemplates->getTemplateArrayForSelectLabel(),
                // 'strType'            => 'select',
                // 'vDefault'            => $oTemplates->getPluginDefaultTemplateID( $GLOBALS['strAmazonAutoLinks_UnitType'] ),    
                // 'fHideTitleColumn'    => true,
            // )                        
        // );
        
    }
    
    public function validation_AmazonAutoLinks_MetaBox_Template( $arrInput, $arrOldInput ) {    // validation_ + extended class name

        // Apply allowed HTML tags for the KSES filter.
        add_filter( 'safe_style_css', array( $this, 'allowInlineStyleMaxWidth' ) );
        $arrAllowedHTMLTags = AmazonAutoLinks_Utilities::convertStringToArray( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['form_options']['allowed_html_tags'], ',' );
        $arrInput['item_format'] = AmazonAutoLinks_WPUtilities::escapeKSESFilter( $arrInput['item_format'], $arrAllowedHTMLTags );
        $arrInput['image_format'] = AmazonAutoLinks_WPUtilities::escapeKSESFilter( $arrInput['image_format'], $arrAllowedHTMLTags );
        $arrInput['title_format'] = AmazonAutoLinks_WPUtilities::escapeKSESFilter( $arrInput['title_format'], $arrAllowedHTMLTags );
        remove_filter( 'safe_style_css', array( $this, 'allowInlineStyleMaxWidth' ) );
        
        return $arrInput;
        
    }
        public function allowInlineStyleMaxWidth( $arrProperties ) {
            $arrProperties[] = 'max-width';
            return $arrProperties;
        }
    
    
}