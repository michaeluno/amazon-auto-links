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
 * Adds the 'Installed' tab to the 'Templates' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Template_ListTable extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @since  4.6.17
     * @return array
     */
    protected function _getArguments() {

        return array(
            'tab_slug'  => 'table',
            'title'     => __( 'Installed', 'amazon-auto-links' ),
            'script'    => array(
                array(
                    'src'           => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/accordion.js',
                    'dependencies'  => array( 'jquery', 'jquery-ui-accordion', ),
                    'in_footer'     => true,
                ),
                array(
                    'src'           => AmazonAutoLinks_TemplateLoader::$sDirPath . '/asset/lightbox2/js/lightbox.js',
                    'dependencies'  => array( 'jquery', ),
                    'in_footer'     => true,
                ),
                array(
                    'src'           => AmazonAutoLinks_TemplateLoader::$sDirPath . '/asset/js/warning-tooltip.js',
                    'dependencies'  => array( 'jquery', 'wp-pointer' ),
                    'in_footer'     => true,
                ),
            ),
            'style'     => array(
                AmazonAutoLinks_TemplateLoader::$sDirPath . '/asset/lightbox2/css/lightbox.css',
            ),
        );
    }

    /**
     * @var AmazonAutoLinks_ListTable_Template
     */
    private $___oTemplateListTable;

    /**
     * Triggered when the tab is loaded.
     *
     * @callback        load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        wp_enqueue_style( 'wp-pointer' );

        do_action( 'aal_action_before_template_list_set_up' );

        // Set the list table data.
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $this->___oTemplateListTable = new AmazonAutoLinks_ListTable_Template( $_oTemplateOption->getAvailable() );
        $this->___oTemplateListTable->process_bulk_action();

    }

    /**
     *
     * @callback        do_{page slug}_{tab slug}
     */
    public function replyToDoTab( $oFactory ) {

        $this->___oTemplateListTable->prepare_items();
        ?>
        <form id="template-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr( AmazonAutoLinks_Registry::$aAdminPages[ 'template' ] ); ?>" />
            <input type="hidden" name="tab" value="table" />
            <input type="hidden" name="post_type" value="<?php echo esc_attr( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] ); ?>" />
            <!-- Now we can render the completed list table -->
            <?php $this->___oTemplateListTable->display() ?>
        </form>        
        <?php
                
    }    
        
    public function replyToDoAfterTab( $oFactory ) {
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug( 'back_end' ) ) {
            return;
        }

        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        echo "<h3>" . esc_html__( 'Debug', 'amazon-auto-links' ) . "</h3>";
        echo "<div class='aal-accordion'>";
        echo "<h4>" . 'Available Templates' . "</h4>";
        echo "<div>" . $oFactory->oDebug->getDetails( $_oTemplateOption->getAvailable() ) . "</div>";
        echo "<h4>" . 'Active Templates' . "</h4>";
        echo "<div>" . $oFactory->oDebug->getDetails( $_oTemplateOption->getActiveTemplates() ) . "</div>";
        echo "<h4>" . 'Uploaded Templates' . "</h4>";
        echo "<div>" . $oFactory->oDebug->getDetails( $_oTemplateOption->getUploadedTemplates() ) . "</div>";
        echo "<h4>" . 'Raw Template Option Values' . "</h4>";
        echo "<div>" . $oFactory->oDebug->getDetails( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ], array() ) ) . "</div>";
        echo "<h4>" . 'Data of Stored Templates' . "</h4>";
        echo "<div>" . $oFactory->oDebug->getDetails( $_oTemplateOption->get() ) . "</div>";
        echo "</div>"; // .aal-accordion
        
    }

}