<?php
/**
 * Provides the definitions of form fields for the template section.
 * 
 * @since            2.0.0
 * @remark            The admin page and meta box access it.
 */
abstract class AmazonAutoLinks_Form_Template_ extends AmazonAutoLinks_Form {

    public function getTemplateFields( $strSectionID, $strPrefix, $fButton=true, $strUnitType='category' ) {
        
        $arrItemFormat = AmazonAutoLinks_Unit::getItemFormatArray();
        
        return array(
            array(
                'strFieldID' => $strPrefix . 'template_id',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Template Name', 'amazon-auto-links' ),
                'strType' => 'select',            
                'strDescription'    => __( 'Sets a default template for this unit.', 'amazon-auto-links' ),
                'vLabel'            => $GLOBALS['oAmazonAutoLinks_Templates']->getTemplateArrayForSelectLabel(),
                'vDefault'            => $GLOBALS['oAmazonAutoLinks_Templates']->getPluginDefaultTemplateID( $strUnitType ),    // defined in the 'unit_type' field
            ),
            array(
                'strFieldID' => $strPrefix . 'column',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strTitle' => __( 'Number of Columns', 'amazon-auto-links' ),
                'strType' => 'number',
                'vClassAttribute' => ( $intMaxCol = $GLOBALS['oAmazonAutoLinks_Option']->getMaxSupportedColumnNumber() ) > 1 ? '' : 'disabled',
                'vDisable' => $intMaxCol > 1 ? false : true,
                'vMax' => $intMaxCol,
                // 'vMin' => 1, // <-- not sure this horizontally diminishes the input element
                'vAfterInputTag' => "<div style='margin:auto; width:100%; clear: both;'><img src='" . AmazonAutoLinks_Commons::getPluginURL( 'asset/image/columns.gif' ) . "' title='" . __( 'The number of columns', 'amazon-auto-links' ) . "' style='width:220px; margin-top: 8px;' /></div>",
                'strDescription' => __( 'This option requires a column supported template to be activated.' ) 
                    . ( $intMaxCol > 1 ? '' : ' ' . sprintf( __( 'Get one <a href="%1$s" target="_blank">here</a>!' ), 'http://en.michaeluno.jp/amazon-auto-links-pro/' ) ),
                'vDefault' => 4,
                'vDelimiter' => '',
            ),                
            array(
                'strFieldID' => $strPrefix . 'item_format',
                'strTitle' => __( 'Item Format', 'amazon-auto-links' ),
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strType' => 'textarea',    
                'vClassAttribute' => $GLOBALS['oAmazonAutoLinks_Option']->isAdvancedAllowed() ? '' : 'read-only',
                'vReadOnly' => $GLOBALS['oAmazonAutoLinks_Option']->isAdvancedAllowed() ? false : true, 
                'vCols' => 60,
                'vRows' => 4,                
                'strDescription'    => __( 'Sets the layout of an item. The following variables are available.', 'amazon-auto-links' ) . '<br />'
                    . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%title%</code> - ' . __( 'title with HTML tags defined in the Title Format option', 'amazon-auto-links' ) . '<br />'
                    . '<code>%title_text%</code> - ' . __( 'title without HTML tags', 'amazon-auto-links' ) . '<br />'
                    . '<code>%image%</code> - ' . __( 'thumbnail with HTML tags defined in the Image Format option', 'amazon-auto-links' ) . '<br />'
                    . '<code>%description%</code> - ' . __( 'description with HTML tags', 'amazon-auto-links' ) . '<br />'
                    . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ) . '<br />'
                    . '<code>%price%</code> - ' . __( 'the product price (only for the search unit type).', 'amazon-auto-links' ),
                'vDefault'    => $arrItemFormat['item_format'],
            ),
            array(
                'strFieldID' => $strPrefix . 'title_format',
                'strTitle' => __( 'Title Format', 'amazon-auto-links' ),
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strType' => 'textarea',
                'vCols' => 60,
                'vRows' => 4,
                'vClassAttribute' => $GLOBALS['oAmazonAutoLinks_Option']->isAdvancedAllowed() ? '' : 'read-only',
                'vReadOnly' => $GLOBALS['oAmazonAutoLinks_Option']->isAdvancedAllowed() ? false : true, 
                'strDescription'    => __( 'Sets the layout of a title.', 'amazon-auto-links' ) . '<br />'
                    . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%title_text%</code> - ' . __( 'title', 'amazon-auto-links' ) . '<br />'
                    . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ),
                'vDefault' => $arrItemFormat['title_format'],
            ),                
            array(
                'strFieldID' => $strPrefix . 'image_format',
                'strTitle' => __( 'Image Format', 'amazon-auto-links' ),
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strType' => 'textarea',
                'vCols' => 60,
                'vRows' => 4,
                'vClassAttribute' => $GLOBALS['oAmazonAutoLinks_Option']->isAdvancedAllowed() ? '' : 'read-only',
                'vReadOnly' => $GLOBALS['oAmazonAutoLinks_Option']->isAdvancedAllowed() ? false : true, 
                'strDescription'    => __( 'Sets the layout of an image.', 'amazon-auto-links' ) . '<br />'
                    . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%title_text%</code> - ' . __( 'title', 'amazon-auto-links' ) . '<br />'
                    . '<code>%src%</code> - ' . __( 'image url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%max_width%</code> - ' . __( 'image size', 'amazon-auto-links' ) . '<br />'
                    . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ),
                'vDefault' => $arrItemFormat['image_format'],

            ),            
            array(  // single button
                'fIf' => $fButton,
                'strFieldID' => $strPrefix . 'submit_initial_options',
                'strSectionID' => $strSectionID ? $strSectionID : null,
                'strType' => 'submit',
                'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    . "<div class='right-button'>",
                'strAfterField' => "</div>",
                'vLabelMinWidth' => 0,
                'vLabel' => __( 'Create', 'amazon-auto-links' ),
                'vClassAttribute' => 'button button-primary',
                'strAfterField' => '<input type="hidden" name="amazon_auto_links_admin[' . $this->strPageSlug . '][' . $strSectionID . '][' . $strPrefix . 'unit_type]" value="' . $strUnitType . '">',
            )                
            
        );
        
    }


}