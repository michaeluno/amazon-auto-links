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
 * Inserts warnings to the template list table.
 *
 * @since       4.6.17
 */
class AmazonAutoLinks_Template_Event_Filter_TemplateListWarning extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores uploaded template directory paths that the plugin can recognize.
     *
     * This is compared to the available templates and if there are duplicated items, sets warnings.
     * @var  string[]
     * @since 4.6.17
     */
    public $aUploadedTemplateDirPaths = array();
    /**
     * @var   string[]
     * @since 4.6.17
     */
    public $aUploadedTemplateIDs = array();

    /**
     * Sets up hooks.
     * @since  4.6.17
     */
    public function __construct() {
        add_filter( 'aal_filter_template_list_table_warning', array( $this, 'replyToGetWarnings' ), 10, 2 );
        add_filter( 'aal_filter_available_templates', array( $this, 'replyToParseAvailableTemplates' ), 10, 2 );
        add_action( 'aal_action_before_template_list_set_up', array( $this, 'replyToSetUploadedTemplates' ) );
    }

    /**
     * @param    string $sWarning
     * @param    array  $aTemplate
     * @since    4.6.17
     * @return   string
     * @callback add_filter() aal_filter_template_list_table_warning
     */
    public function replyToGetWarnings( $sWarning, array $aTemplate ) {
        if ( empty( $aTemplate[ 'warnings' ] ) ) {
            return $sWarning;
        }
        // Add a warning icon
        $_sWarnings = "<p>" . implode('</p><p>', $aTemplate[ 'warnings' ] ) . "</p>";
        return $sWarning . "<div class='warning icon-container' data-has-warning='1'>"
                . "<span class='dashicons dashicons-warning'></span>"
                . "<div class='tooltip-content'>" . $_sWarnings ."</div>"
            . "</div>";
    }

    /**
     * Checks if there are duplicated items.
     * @param  array  $aAvailableTemplates
     * @sicne  4.6.17
     * @return array
     */
    public function replyToParseAvailableTemplates( array $aAvailableTemplates ) {

        $_sDuplicateNotice = __( 'This is a duplicate of an already listed template and can cause issues so please remove it using the action link.', 'amazon-auto_links' );
        $_sInvalidNotice   = __( 'The plugin cannot load the template.', 'amazon-auto-links' )
                        . ' ' . __( 'Please remove this from the action link. If there are associated units to this template, please update their template unit setting.', 'amazon-auto-links' );

        foreach( $aAvailableTemplates as $_sTemplateID => &$_aTemplate ) {

            $_aTemplate[ 'warnings' ] = is_array( $_aTemplate[ 'warnings' ] )
                ? $_aTemplate[ 'warnings' ]
                : array();

            if ( ! $_aTemplate[ 'is_valid' ] ) {
                $_aTemplate[ 'warnings' ][] = $_sInvalidNotice;
                continue;
            }

            // The uploaded templates are treated as original
            if ( in_array( $_sTemplateID, $this->aUploadedTemplateIDs, true ) ) {
                continue;
            }
            $_sThisDirPath = realpath( $this->getElement( $_aTemplate, array( 'dir_path' ) ) );
            if ( ! in_array( $_sThisDirPath, $this->aUploadedTemplateDirPaths, true ) ) {
                continue;
            }
            $_aTemplate[ 'warnings' ][] = $_sDuplicateNotice;
            $_aTemplate[ 'should_remove' ] = true;

        }
        return $aAvailableTemplates;

    }

    /**
     * @since 4.6.17
     * @callback add_action() aal_action_before_template_list_set_up
     */
    public function replyToSetUploadedTemplates() {
        $_oTemplateOption                = AmazonAutoLinks_TemplateOption::getInstance();
        $_aUploadedTemplates             = $_oTemplateOption->getUploadedTemplates();
        $this->aUploadedTemplateIDs      = array_keys( $_aUploadedTemplates );
        $this->aUploadedTemplateDirPaths = array();
        foreach( $_aUploadedTemplates as $_sTemplateID => $_aTemplate ) {
            $_sDirPath = realpath( $this->getElement( $_aTemplate, array( 'dir_path' ) ) );
            if ( empty( $_sDirPath ) ) {
                continue;
            }
            $this->aUploadedTemplateDirPaths[ $_sTemplateID ] = $_sDirPath;
        }
    }

}