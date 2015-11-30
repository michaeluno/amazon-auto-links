<?php
/**
 * Provides the definitions of auto-insert form fields for units.
 * 
 * @since           3  
 * @remark          The admin page and meta box access it.
 */
class AmazonAutoLinks_FormFields_Unit_Template extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        $_oOption       = $this->oOption;
        $_bAPIConnected = $this->oOption->isAPIConnected();
        $_sDel          = $_bAPIConnected
            ? ''
            : "delete-line";
        $_iMaxCol       = $this->oOption->getMaxSupportedColumnNumber();
        $_aItemFormat   = AmazonAutoLinks_UnitOption_Base::getDefaultItemFormat();
        $_aFields       = array(
            array(
                'field_id'          => $sFieldIDPrefix . 'template_id',
                'type'              => 'select',            
                'title'             => __( 'Template Name', 'amazon-auto-links' ),
                'tip'               => __( 'Sets a default template for this unit.', 'amazon-auto-links' ),
                'label'             => $this->oTemplateOption->getActiveTemplateLabels(),
                'default'           => $this->oTemplateOption->getDefaultTemplateIDByUnitType( $sUnitType ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'column',
                'title'             => __( 'Number of Columns', 'amazon-auto-links' ),
                'type'              => 'number',
                'attributes'        => array(
                    'class'             => $_iMaxCol > 1 
                        ? '' 
                        : 'disabled',
                    'disabled'           => $_iMaxCol > 1 
                        ? null
                        : 'disabled',                    
                    'max'               => $_iMaxCol,
                    // 'min' => 1, // <-- not sure this horizontally diminishes the input element
                ),
                'after_input'       => "<div style='margin:auto; width:100%; clear: both;'><img src='" . AmazonAutoLinks_Registry::getPluginURL( 'asset/image/columns.gif' ) . "' title='" . __( 'The number of columns', 'amazon-auto-links' ) . "' style='width:220px; margin-top: 8px;' /></div>",
                'tip'               => __( 'This option requires a column supported template to be activated.' ),
                'description'       => $_iMaxCol > 1 
                    ? '' 
                    : ' ' . sprintf( __( 'Get one <a href="%1$s" target="_blank">here</a>!' ), 'http://en.michaeluno.jp/amazon-auto-links-pro/' ),                
                'default'           => 4,
                'delimiter'         => '',
            ),                
            array(
                'field_id'          => $sFieldIDPrefix . 'item_format',
                'type'              => 'textarea',    
                'title'             => __( 'Item Format', 'amazon-auto-links' ),
                'attributes'        => array(
                    'class'    => $_oOption->isAdvancedAllowed() 
                        ? '' 
                        : 'read-only',
                    'readonly' => $_oOption->isAdvancedAllowed() 
                        ? null
                        : 'raedonly', 
                    'rows'     => 6,                
                    'style'    => 'width: 96%',
                    
                ),
                'default'           => $_aItemFormat['item_format'],
                'tip'               => __( 'Sets the layout of an item. The following variables are available.', 'amazon-auto-links' ) . '<br />'
                        . "<code>%href%</code> - " . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                        . "<code>%title%</code> - " . __( 'title with HTML tags defined in the Title Format option', 'amazon-auto-links' ) . '<br />'
                        . "<code>%title_text%</code> - " . __( 'title without HTML tags', 'amazon-auto-links' ) . '<br />'
                        . "<code>%image%</code> - " . __( 'thumbnail with HTML tags defined in the Image Format option', 'amazon-auto-links' ) . '<br />'
                        . "<code class='{$_sDel}'>%image_set%</code> - " . __( 'sub-images.', 'amazon-auto-links' ) . '<br />'
                        . "<code>%description%</code> - " . __( 'description with HTML tags', 'amazon-auto-links' ) . '<br />'
                        . "<code>%description_text%</code> - " . __( 'description without HTML tags', 'amazon-auto-links' ) . '<br />'
                        . "<code class='{$_sDel}'>%price%</code> - " . __( 'product price.', 'amazon-auto-links' ) . '<br />'
                        . "<code class='{$_sDel}'>%rating%</code> - " . __( 'user rating.', 'amazon-auto-links' ) . '<br />'
                        . "<code class='{$_sDel}'>%review%</code> - " . __( 'customer review.', 'amazon-auto-links' ) . '<br />'
                        . "<code>%button%</code> - " . __( 'store link button.', 'amazon-auto-links' ) . '<br />'
                        . "<code>%disclaimer%</code> - " . __( 'disclaimer for the product information.', 'amazon-auto-links' ),
                'description'       => $_bAPIConnected
                        ? null
                        : sprintf(
                            '* <span class="warning">' 
                                . __( 'Some items need <a href="%1$s">API</a> to be set up.', 'amazon-auto-links' )
                            . "</span>",
                            AmazonAutoLinks_PluginUtility::getAPIAuthenticationPageURL()
                        ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'title_format',
                'title'             => __( 'Title Format', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'default'           => $_aItemFormat['title_format'],
                'attributes'        => array(
                    'rows'      => 6,
                    'class'     => $_oOption->isAdvancedAllowed() 
                        ? '' 
                        : 'read-only',
                    'readonly'  => $_oOption->isAdvancedAllowed() 
                        ? null
                        : 'readonly', 
                    'style'     => 'width: 96%',
                ),
                'tip'               => __( 'Sets the layout of a title.', 'amazon-auto-links' ) . '<br />'
                    . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%title_text%</code> - ' . __( 'title', 'amazon-auto-links' ) . '<br />'
                    . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ),
            ),                
            array(
                'field_id'      => $sFieldIDPrefix. 'image_format',
                'title'         => __( 'Image Format', 'amazon-auto-links' ),
                'type'          => 'textarea',
                'attributes'    => array(
                    // 'cols'      => 40,
                    'rows'      => 6,
                    'class'     => $_oOption->isAdvancedAllowed() 
                        ? '' 
                        : 'read-only',
                    'readonly'  => $_oOption->isAdvancedAllowed() 
                        ? null
                        : 'readonly', 
                    'style'     => 'width: 96%',
                ),
                'default'       => $_aItemFormat['image_format'],
                'tip'           => __( 'Sets the layout of an image.', 'amazon-auto-links' ) . '<br />'
                    . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%title_text%</code> - ' . __( 'title', 'amazon-auto-links' ) . '<br />'
                    . '<code>%src%</code> - ' . __( 'image url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%max_width%</code> - ' . __( 'image size', 'amazon-auto-links' ) . '<br />'
                    . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ),
            )      
            
        );

        // Insert common field arguments.
        $_bIsDisabled  = ! $_oOption->isAdvancedAllowed();
        if ( ! $_bIsDisabled ) {
            return $_aFields;
        }
        $_sOpeningTag  = $_bIsDisabled 
            ? "<div class='upgrade-to-pro' style='margin:0; padding:0; display: inline-block;' title='" . __( 'Please consider upgrading to Pro to use this feature!', 'amazon-auto-links' ) . "'>" 
            : "";
        $_sClosingTag  = $_bIsDisabled 
            ? "</div>" 
            : "";        
        foreach( $_aFields as &$_aField ) {
            if ( $sFieldIDPrefix . 'template_id' === $_aField[ 'field_id' ] ) {
                continue;
            }
            $_aField = array(
                    'before_fieldset' => $_sOpeningTag,
                    'after_fieldset'  => $_sClosingTag,
                )
                + $_aField
            ;
        }  
        return $_aFields;
        
    }
      
}