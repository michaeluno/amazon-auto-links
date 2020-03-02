<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Adds the 'Installed' tab to the 'Templates' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Template_ListTable extends AmazonAutoLinks_AdminPage_Tab_Base {
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

        // Set the list table data.
        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
        $this->___oTemplateListTable = new AmazonAutoLinks_ListTable_Template(
            $_oTemplateOption->getActiveTemplates() // precedence
            + $_oTemplateOption->getUploadedTemplates() // merge
        );
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
            <input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 'aal_templates'; ?>" />
            <input type="hidden" name="tab" value="<?php echo isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'table'; ?>" />
            <input type="hidden" name="post_type" value="<?php echo isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $this->___oTemplateListTable->display() ?>
        </form>        
        <?php
                
    }    
        
    public function replyToDoAfterTab( $oFactory ) {
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug() ) {
            return;
        }
        
        echo "<h3>" 
                . __( 'Debug', 'amazon-auto-links' ) 
            . "</h3>";
            
        echo "<h4>" 
                . __( 'Raw Template Option Values', 'amazon-auto-links' ) 
            . "</h4>";
        echo $oFactory->oDebug->get(
            get_option(
                AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ],
                array()
            )
            
        );            
        
        echo "<h4>" 
                . __( 'Data of Active Templates', 'amazon-auto-links' ) 
            . "</h4>";        
        echo $oFactory->oDebug->get(
            AmazonAutoLinks_TemplateOption::getInstance()->get()
        );
        
    }
}
