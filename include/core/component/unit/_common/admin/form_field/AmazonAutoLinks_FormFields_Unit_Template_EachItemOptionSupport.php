<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since 4.0.0
 * @since 4.5.0 Change the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     *
     * @param       string  $sFieldIDPrefix
     * @param       string  $sUnitType
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {

        $_aActiveTemplateLabels = $this->oTemplateOption->getUsableTemplateLabels();
        $_iMaxCol               = $this->oOption->getMaxSupportedColumnNumber();
        $_aOutputFormats        = $this->oOption->getDefaultOutputFormats();
        $_sTemplateScreenURL    = add_query_arg(
            array(
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'template' ],
            ),
            admin_url( 'edit.php' )
        );
        return array(
            array(
                'field_id'          => $sFieldIDPrefix . 'template_id',
                'type'              => 'revealer',
                'select_type'       => 'select',
                'selectors'         => $this->___getRevealerSelectorsForItemFormatFields( $_aActiveTemplateLabels ),
                'title'             => __( 'Template Name', 'amazon-auto-links' ),
                'label'             => $_aActiveTemplateLabels,
                'default'           => $this->oTemplateOption->getDefaultTemplateIDByUnitType( $sUnitType ),
                'tip'               => __( 'Sets a default template for this unit.', 'amazon-auto-links' )
                    . ' ' . sprintf(
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
               'after_input'       => "<div style='margin:auto; width:100%; clear: both;'>"
                        . "<img src='" . AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/image/columns.gif', true ) . "' title='" . __( 'The number of columns', 'amazon-auto-links' ) . "' style='width:220px; margin-top: 8px;' alt='" . esc_attr( __( 'Columns', 'amazon-auto-links' ) ) . "' />"
                    . "</div>",
                'tip'               => __( 'This option requires a column supported template to be activated.', 'amazon-auto-links' ),
                'description'       => $_iMaxCol > 1 
                    ? '' 
                    : ' ' . sprintf( __( 'Get one <a href="%1$s" target="_blank">here</a>!', 'amazon-auto-links' ), 'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/' ),
                'default'           => 4,
                'delimiter'         => '',
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'custom_text',
                'title'             => __( 'Custom Text', 'amazon-auto-links' ),
                'tip'               => __( 'The text replaced with the <code>%text%</code> tag in the <b>Unit Format</b> option.', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'rich'              => true,
                'class'             => array(
                    'field' => 'width-full',
                    'input' => 'width-full',
                ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'output_formats',
                'title'             => __( 'Output Formats', 'amazon-auto-links' ),
                'content'           => $this->___getTemplateFormatFields( $_aActiveTemplateLabels, $sFieldIDPrefix, $_aOutputFormats, $sUnitType ),
            )
        );

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
             * @param  string $sTemplateID
             * @return string
             */
            private function ___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ) {
                $sTemplateID = untrailingslashit( $sTemplateID );
                $_sSelector  = wp_normalize_path( $sTemplateID );
                $_sSelector  = ltrim( $_sSelector, '.' );
                return str_replace( '/', '_', $_sSelector );
            }

        /**
         * @param  array  $aActiveTemplateLabels
         * @param  string $sFieldIDPrefix
         * @param  array  $aDefaultItemFormat
         * @param  string $sUnitType
         * @return array
         */
        private function ___getTemplateFormatFields( array $aActiveTemplateLabels, $sFieldIDPrefix, array $aDefaultItemFormat, $sUnitType ) {
            $_aFormatFields = array();
            foreach( $aActiveTemplateLabels as $_sTemplateID => $_sLabel ) {
                $_aFormatFields[] = array(
                    'field_id' => $_sTemplateID,
                    'content'   => array(
                        $this->___getField_UnitFormat( $sFieldIDPrefix, $aDefaultItemFormat[ 'unit_format' ], $_sTemplateID ),
                        $this->___getField_ItemFormat( $sFieldIDPrefix, $aDefaultItemFormat[ 'item_format' ], $_sTemplateID, $sUnitType ),
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
         * @param  string $sFieldIDPrefix
         * @param  string $sDefault
         * @param  string $sTemplateID
         * @param  string $sUnitType
         * @return array
         * @since  unknown
         * @since  5.0.0   Added the `$sUnitType` parameter.
         */
        private function ___getField_ItemFormat( $sFieldIDPrefix, $sDefault, $sTemplateID, $sUnitType ) {

            $_sLocale       = isset( $this->oFactory ) && method_exists( $this->oFactory, 'getValue' )
                ? $this->oFactory->getValue( 'country' )
                : '';
            $_sUnitType     = isset( $this->oFactory ) && method_exists( $this->oFactory, 'getValue' )
                ? $this->oFactory->getValue( array( 'unit_type' ), $sUnitType )
                : $sUnitType;
            $_sUnitType     = empty( $_sUnitType ) ? $sUnitType : $_sUnitType; // somehow, an empty array is set sometimes.
            $_bAPIConnected = ( boolean ) $this->oOption->getPAAPIStatus( $_sLocale );
            // 3.8.0 If the database table version is below 1.1.0b01,
            $_bTableUpdateRequired = false;
            if ( version_compare( get_option( "aal_products_version", '0' ), '1.1.0b01', '<' ) ) {
                $_bTableUpdateRequired = true;
            }

            return array(
                'field_id'          => $sFieldIDPrefix . 'item_format',
                'type'              => 'textarea',
                'title'             => __( 'Item Format', 'amazon-auto-links' ),
                'attributes'        => array(
                    'rows'     => 6,
                    'fieldset' => array(
                        'style: margin-bottom: 0;',
                    ),
                ),
                'default'           => apply_filters( 'aal_filter_template_default_item_format_' . $sTemplateID, $sDefault, $_sLocale ),
                'class'         => array(
                    'fieldset'  => $this->___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ),
                    'field'     => 'width-full',
                    'input'     => 'width-full',
                ),
                'tip'               => array(
                    'width'   => 480,
                    'content' => __( 'Sets the layout of the product. The following tags are available.', 'amazon-auto-links' ) . '<br />'
                        . $this->___getItemFormatTags( $_sUnitType, $_sLocale, $_bAPIConnected, $_bTableUpdateRequired )
                        . $this->___getPAAPIRequirementNotice( $_bAPIConnected )
                        . $this->___getTableUpdateNotice( $_bTableUpdateRequired )
                ),
            );
        }
            /**
             * @param  boolean $bTableUpdateRequired
             * @return string
             * @since  5.0.0
             */
            private function ___getTableUpdateNotice( $bTableUpdateRequired ) {
                if ( ! $bTableUpdateRequired ) {
                    return '';
                }
                return '<span style="color: red;">' . __( 'Some tags require the plugin database table to be updated.', 'amazon-auto-links' ) . "</span>";
            }
            /**
             * @param  boolean $bAPIConnected
             * @return string
             * @sicne  5.0.0
             */
            private function ___getPAAPIRequirementNotice( $bAPIConnected ) {
                if ( $bAPIConnected ) {
                    return '';
                }
                return sprintf(
                    '* <span class="warning">'
                        . __( 'Some items need <a href="%1$s">API</a> to be set up.', 'amazon-auto-links' )
                    . "</span>",
                    $this->getAPIAuthenticationPageURL()
                );
            }
            /**
             * @return string
             * @since  5.0.0
             */
            private function ___getItemFormatTags( $sUnitType, $sLocale, $bAPIConnected, $bTableUpdateRequired ) {
                $_bTableSupport    = ( version_compare( get_option( "aal_products_version", '0' ), '1.1.0b01', '>=' ) );
                $_bAdWidgetSupport = in_array( $sLocale, AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport(), true );
                $_aTags            = array(
                    '%href%' => array(
                        'description'   => __( 'a product link url', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%title%' => array(
                        'description'   => __( 'a title with HTML tags defined in the Title Format option', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%title_text%' => array(
                        'description'   => __( 'a title without HTML tags', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%image%' => array(
                        'description'   => __( 'a thumbnail with HTML tags defined in the Image Format option', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%image_set%' => array(
                        'description'   => __( 'sub-images', 'amazon-auto-links' ),
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%image_size%' => array(
                        'description'   => __( 'the thumbnail size set in the Image Size option', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%description%' => array(
                        'description'   => __( 'a description with HTML tags', 'amazon-auto-links' ),
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%description_text%' => array(
                        'description'   => __( 'a description without HTML tags', 'amazon-auto-links' ),
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%price%' => array(
                        'description'   => __( 'a product price', 'amazon-auto-links' ),
                        'available'     => $_bAdWidgetSupport || ( $bAPIConnected && $_bTableSupport ),
                    ),
                    '%discount%' => array(
                        'description'   => __( 'discount percentage', 'amazon-auto-links' ), // 5.0.0+
                        'available'     => $_bAdWidgetSupport || ( $bAPIConnected && $_bTableSupport ),
                    ),
                    '%rating%' => array(
                        'description'   => __( 'user rating', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%review%' => array(
                        'description'   => __( 'customer reviews.', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%button%' => array(
                        'description'   => __( 'a store link button.', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%disclaimer%' => array(
                        'description'   => __( 'a disclaimer for the product information', 'amazon-auto-links' ),
                        'available'     => true,
                    ),
                    '%date%' => array(
                        'description'   => __( 'the updated date', 'amazon-auto-links' ),   // 3.8.0+
                        'available'     => true,
                    ),
                    '%content%' => array(
                        'description'   => __( 'a full product HTML description', 'amazon-auto-links' ), // 3.3.0+
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%meta%' => array(
                        'description'   => __( 'meta data of the product', 'amazon-auto-links' ), // 3.3.0+
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%author%' => array(
                        'description'   => __( 'the author of the product', 'amazon-auto-links' ), // 4.1.0+
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%author_text%' => array(
                        'description'   => __( 'the author text without HTML tags', 'amazon-auto-links' ), // 4.7.10+
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%category%' => array(
                        'description'   => __( 'categories that the product belongs to', 'amazon-auto-links' ), // 3.8.0+
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%feature%' => array(
                        'description'   => __( 'list of product features', 'amazon-auto-links' ), // 3.8.0+
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%rank%' => array(
                        'description'   => __( 'sales rank of the product', 'amazon-auto-links' ), // 3.8.0+
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                    '%prime%' => array(
                        'description'   => __( 'prime mark', 'amazon-auto-links' ), // 3.9.0+
                        'available'     => $bAPIConnected && $_bTableSupport,
                    ),
                );
                $_aTags = apply_filters( 'aal_filter_admin_item_format_tags', $_aTags, $sUnitType, $sLocale, $bAPIConnected, $bTableUpdateRequired );
                $_sOutput = '';
                foreach( $_aTags as $_sTagName => $_aTag ) {
                    $_aTag       = $_aTag + array( 'description' => '', 'available' => true );
                    $_sName   = $_aTag[ 'available' ]
                        ? "<code>{$_sTagName}</code>"
                        : "<code class='delete-line'>{$_sTagName}</code>";
                    $_sOutput .= "<li>"
                           . $_sName . ' - ' . $_aTag[ 'description' ] . '<br />'
                        . "</li>";
                }
                return "<ul>" . $_sOutput . "</ul>";

            }

        /**
         * @param   string  $sFieldIDPrefix
         * @param   string  $sDefault
         * @param   string  $sTemplateID
         * @return  array
         * @since   4.3.0
         */
        private function ___getField_UnitFormat( $sFieldIDPrefix, $sDefault, $sTemplateID ) {
            $sDefault       = apply_filters( 'aal_filter_template_default_unit_format_' . $sTemplateID, $sDefault );
            return array(
                'field_id'          => $sFieldIDPrefix . 'unit_format',
                'title'             => __( 'Unit Format', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'default'           => $sDefault,
                'attributes'        => array(
                    'rows'      => 6,
                ),
                'tip'               => array(
                    'content' => __( 'Sets the layout of the unit.', 'amazon-auto-links' )
                        . ' ' . __( 'The following tags are available.', 'amazon-auto-links' )
                        . '<br />'
                        . '<code>%text%</code> - ' . __( 'the custom text set in the <b>Custom Text</b> option.', 'amazon-auto-links' ) . '<br />'
                        . '<code>%products%</code> - ' . __( 'products', 'amazon-auto-links' ),
                    'width'   => 480,
                ),
                'class'         => array(
                    'fieldset'  => $this->___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ),
                    'field'     => 'width-full',
                    'input'     => 'width-full',
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
                ),
                'tip'               => array(
                    'width'   => 480,
                    'content' => __( 'Sets the layout of the title.', 'amazon-auto-links' )
                        . ' ' . __( 'The following tags are available.', 'amazon-auto-links' )
                        . '<br />'
                        . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                        . '<code>%title_text%</code> - ' . __( 'title', 'amazon-auto-links' ) . '<br />'
                        . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ),
                ),
                'class'         => array(
                    'fieldset'  => $this->___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ),
                    'field'     => 'width-full',
                    'input'     => 'width-full',
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
                ),
                'default'       => $sDefault,
                'tip'           => array(
                    'width'   => 480,
                    'content' => __( 'Sets the layout of the image.', 'amazon-auto-links' )
                        . ' ' . __( 'The following tags are available.', 'amazon-auto-links' ) . '<br />'
                        . '<code>%href%</code> - ' . __( 'product link url', 'amazon-auto-links' ) . '<br />'
                        . '<code>%title_text%</code> - ' . __( 'title', 'amazon-auto-links' ) . '<br />'
                        . '<code>%src%</code> - ' . __( 'image url', 'amazon-auto-links' ) . '<br />'
                        // . '<code>%max_width%</code> - ' . __( 'image size', 'amazon-auto-links' ) . '<br />' // @deprecated 4.1.0
                        . '<code>%image_size%</code> - ' . __( 'image size', 'amazon-auto-links' ) . '<br />'
                        . '<code>%description_text%</code> - ' . __( 'description without HTML tags', 'amazon-auto-links' ),
                ),
                'class'         => array(
                    'fieldset'  => $this->___getClassAttributeNameFromTemplateIDGenerated( $sTemplateID ),
                    'field'     => 'width-full',
                    'input'     => 'width-full',
                ),
            );
        }
}