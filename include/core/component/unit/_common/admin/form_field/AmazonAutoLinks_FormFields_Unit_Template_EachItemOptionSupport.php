<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           4.0.0
 */
class AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     *
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {

        $_aActiveTemplateLabels = $this->oTemplateOption->getUsableTemplateLabels();
        $_iMaxCol               = $this->oOption->getMaxSupportedColumnNumber();
        $_aItemFormat           = $this->oOption->getDefaultItemFormat();
        $_sTemplateScreenURL    = add_query_arg(
            array(
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'template' ],
            ),
            admin_url( 'edit.php' )
        );
        $_aFields               = array(
            array(
                'field_id'          => $sFieldIDPrefix . 'template_id',
                'type'              => 'revealer',
                'select_type'       => 'select',
                'selectors'         => $this->___getRevealerSelectorsForItemFormatFields( $_aActiveTemplateLabels ),
                'title'             => __( 'Template Name', 'amazon-auto-links' ),
                'tip'               => __( 'Sets a default template for this unit.', 'amazon-auto-links' ),
                'label'             => $_aActiveTemplateLabels,
                'default'           => $this->oTemplateOption->getDefaultTemplateIDByUnitType( $sUnitType ),
                'description'       => sprintf(
                    __( 'If the template you like to use is not listed here, make sure it is activated in the <a href="%1$s">%2$s</a> screen.', 'amazon-auto-links' ),
                    esc_url( $_sTemplateScreenURL ),
                    __( 'Templates', 'amazon-auto-links' )
                ),
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
                    : ' ' . sprintf( __( 'Get one <a href="%1$s" target="_blank">here</a>!' ), 'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/' ),
                'default'           => 4,
                'delimiter'         => '',
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'output_formats',
                'title'             => __( 'Output Formats', 'amazon-auto-links' ),
                'content'           => $this->___getTemplateFormatFields( $_aActiveTemplateLabels, $sFieldIDPrefix, $_aItemFormat ),
            )
        );
        return $_aFields;
        
    }

        /**
         * @param array $aActiveTemplateLabels
         *
         * @return array
         */
        private function ___getRevealerSelectorsForItemFormatFields( array $aActiveTemplateLabels ) {
            $_aSelectors = array();
            foreach( $aActiveTemplateLabels as $_sTemplateID => $_sLabel ) {
                $_sTemplateID = untrailingslashit( $_sTemplateID );
                $_aSelectors[ $_sTemplateID ] = '.' . $this->___getClassAttributeNameFromTemplateIDGenerated( $_sTemplateID );
            }
            return $_aSelectors;
        }

            /**
             * @param sring $sTemplateID
             *
             * @return string
             */
            private function ___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ) {
                $sTemplateID = untrailingslashit( $sTemplateID );
                $_sSelector  = wp_normalize_path( $sTemplateID );
                $_sSelector  = ltrim( $_sSelector, '.' );
                return str_replace( '/', '_', $_sSelector );
            }

        /**
         * @param array $aActiveTemplateLabels
         * @param $sFieldIDPrefix
         * @param array $aDefaultItemFormat
         *
         * @return array
         */
        private function ___getTemplateFormatFields( array $aActiveTemplateLabels, $sFieldIDPrefix, array $aDefaultItemFormat ) {
            $_aFormatFields = array();
            foreach( $aActiveTemplateLabels as $_sTemplateID => $_sLabel ) {
                $_aFormatFields[] = array(
                    'field_id' => $_sTemplateID,
                    'content'   => array(
                        $this->___getField_ItemFormat( $sFieldIDPrefix, $aDefaultItemFormat[ 'item_format' ], $_sTemplateID ),
                        $this->___getField_TitleFormat( $sFieldIDPrefix, $aDefaultItemFormat[ 'title_format' ], $_sTemplateID ),
                        $this->___getField_ImageFormat( $sFieldIDPrefix, $aDefaultItemFormat[ 'image_format' ], $_sTemplateID )
                    ),
                    'attributes' => array(
                        'fieldset' => array(
                            'style' => 'margin-bottom: 0;',
                        ),
                    ),
                );
            }
            return $_aFormatFields;
        }

        /**
         * @param string $sFieldIDPrefix
         * @param string $sDefault
         * @param string $sTemplateID
         *
         * @return array
         */
        private function ___getField_ItemFormat( $sFieldIDPrefix, $sDefault, $sTemplateID ) {

            $sDefault       = apply_filters( 'aal_filter_template_default_item_format_' . $sTemplateID, $sDefault );

            $_bAPIConnected = $this->oOption->isAPIConnected();
            $_sDel          = $_bAPIConnected
                ? ''
                : "delete-line";
            // 3.8.0 If the database table version is below 1.1.0b01,
            $_bTableUpdateRequired = false;
            if ( version_compare( get_option( "aal_products_version", '0' ), '1.1.0b01', '<' ) ) {
                $_sDel = $_sDel ? $_sDel : 'delete-line';
                $_bTableUpdateRequired = true;
            }

            return array(
                'field_id'          => $sFieldIDPrefix . 'item_format',
                'type'              => 'textarea',
                'title'             => __( 'Item Format', 'amazon-auto-links' ),
                'attributes'        => array(
                    'rows'     => 6,
                    'style'    => 'width: 96%',
                    'fieldset' => array(
                        'style: margin-bottom: 0;',
                    ),
                ),
                'default'           => $sDefault,
                'class'         => array(
                    'fieldset'  => $this->___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ),
                ),
                'description'       => array(
                    __( 'Sets the layout of the product. The following variables are available.', 'amazon-auto-links' ) . '<br />'
                        . "<code>%href%</code> - " . __( 'a product link url', 'amazon-auto-links' ) . '<br />'
                        . "<code>%title%</code> - " . __( 'a title with HTML tags defined in the Title Format option', 'amazon-auto-links' ) . '<br />'
                        . "<code>%title_text%</code> - " . __( 'a title without HTML tags', 'amazon-auto-links' ) . '<br />'
                        . "<code>%image%</code> - " . __( 'a thumbnail with HTML tags defined in the Image Format option', 'amazon-auto-links' ) . '<br />'
                        . "<code class='{$_sDel}'>%image_set%</code> - " . __( 'sub-images.', 'amazon-auto-links' ) . '<br />'
                        . "<code>%description%</code> - " . __( 'a description with HTML tags', 'amazon-auto-links' ) . '<br />'
                        . "<code>%description_text%</code> - " . __( 'a description without HTML tags', 'amazon-auto-links' ) . '<br />'
                        . "<code>%price%</code> - " . __( 'a product price.', 'amazon-auto-links' ) . '<br />'
                        . "<code class='{$_sDel}'>%rating%</code> - " . __( 'user rating.', 'amazon-auto-links' ) . '<br />'
                        . "<code class='{$_sDel}'>%review%</code> - " . __( 'customer reviews.', 'amazon-auto-links' ) . '<br />'
                        . "<code>%button%</code> - " . __( 'a store link button.', 'amazon-auto-links' ) . '<br />'
                        . "<code>%disclaimer%</code> - " . __( 'a disclaimer for the product information.', 'amazon-auto-links' ) . '<br />'
                        . "<code class='{$_sDel}'>%content%</code> - " . __( 'a full product HTML description.', 'amazon-auto-links' ) . '<br />'    // 3.3.0+
                        // . "<code class='{$_sDel}'>%similar%</code> - " . __( 'similar products.', 'amazon-auto-links' ) . '<br />'    // 3.3.0+ // @deprecated 3.9.0
                        . "<code class='{$_sDel}'>%meta%</code> - " . __( 'meta data of the product.', 'amazon-auto-links' ) . '<br />'    // 3.3.0+
                        . "<code class='{$_sDel}'>%category%</code> - " . __( 'categories that the product belongs to.', 'amazon-auto-links' ) . '<br />'    // 3.8.0+
                        . "<code class='{$_sDel}'>%feature%</code> - " . __( 'list of product features.', 'amazon-auto-links' ) . '<br />'    // 3.8.0+
                        . "<code class='{$_sDel}'>%rank%</code> - " . __( 'sales rank of the product.', 'amazon-auto-links' ) . '<br />'    // 3.8.0+
                        . "<code>%date%</code> - " . __( 'the updated date.', 'amazon-auto-links' ) . '<br />' // 3.8.0+
                        . "<code class='{$_sDel}'>%prime%</code> - " . __( 'prime mark.', 'amazon-auto-links' ) . '<br />'    // 3.9.0+
                        . ( $_bAPIConnected
                            ? null
                            : sprintf(
                                '* <span class="warning">'
                                    . __( 'Some items need <a href="%1$s">API</a> to be set up.', 'amazon-auto-links' )
                                . "</span>",
                                $this->getAPIAuthenticationPageURL()
                            )
                        ),
                        $_bTableUpdateRequired
                            ? '<span style="color: red;">' . __( 'Some variables require the plugin database table to be updated.', 'amazon-auto-links' ) . "</span>"
                            : '',
                    ),
            );
        }

        /**
         * @param string $sFieldIDPrefix
         * @param string $sDefault
         * @param string $sTemplateID
         *
         * @return array
         */
        private function ___getField_TitleFormat( $sFieldIDPrefix, $sDefault, $sTemplateID ) {
            $sDefault       = apply_filters( 'aal_filter_template_default_title_format_' . $sTemplateID, $sDefault );
            return array(
                'field_id'          => $sFieldIDPrefix . 'title_format',
                'title'             => __( 'Title Format', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'default'           => $sDefault,
                'attributes'        => array(
                    'rows'      => 6,
                    'style'     => 'width: 96%',
                ),
                'description'        => __( 'Sets the layout of the title.', 'amazon-auto-links' ) . '<br />'
                    . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%title_text%</code> - ' . __( 'title', 'amazon-auto-links' ) . '<br />'
                    . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldset'  => $this->___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ),
                ),
            );
        }

        /**
         * @param string $sFieldIDPrefix
         * @param string $sDefault
         * @param string $sTemplateID
         *
         * @return array
         */
        private function ___getField_ImageFormat( $sFieldIDPrefix, $sDefault, $sTemplateID ) {
            $sDefault       = apply_filters( 'aal_filter_template_default_image_format_' . $sTemplateID, $sDefault );
            return array(
                'field_id'      => $sFieldIDPrefix. 'image_format',
                'title'         => __( 'Image Format', 'amazon-auto-links' ),
                'type'          => 'textarea',
                'attributes'    => array(
                    'rows'      => 6,
                    'style'     => 'width: 96%',
                ),
                'default'       => $sDefault,
                'description'   => __( 'Sets the layout of the image.', 'amazon-auto-links' ) . '<br />'
                    . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%title_text%</code> - ' . __( 'title', 'amazon-auto-links' ) . '<br />'
                    . '<code>%src%</code> - ' . __( 'image url', 'amazon-auto-links' ) . '<br />'
                    . '<code>%max_width%</code> - ' . __( 'image size', 'amazon-auto-links' ) . '<br />'
                    . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldset'  => $this->___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ),
                ),
            );
        }
}