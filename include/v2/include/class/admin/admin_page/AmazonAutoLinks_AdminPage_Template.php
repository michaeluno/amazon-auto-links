<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2015, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.5
 * 
 */

/**
 * Deals with the plugin admin pages. 
 */
abstract class AmazonAutoLinks_AdminPage_Template extends AmazonAutoLinks_AdminPage_Extension {

    /**
     * 
     * @callback        action      load_ + {page slug}
     */
    public function load_aal_templates() {   
    
        $oTemplate = $GLOBALS['oAmazonAutoLinks_Templates'];        
        $this->oTemplateListTable = new AmazonAutoLinks_ListTable( 
            $oTemplate->getActiveTemplates() + $oTemplate->getUploadedTemplates() 
        );
        $this->oTemplateListTable->process_bulk_action();
        
    }        
    /**
     * 
     * @callback        action      do_ + page slug + tab slug
     */
    public function do_aal_templates_table() {   
            
        $this->oTemplateListTable->prepare_items();
        ?>
        <form id="template-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 'aal_templates'; ?>" />
            <input type="hidden" name="tab" value="<?php echo isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'table'; ?>" />
            <input type="hidden" name="post_type" value="<?php echo isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : AmazonAutoLinks_Commons::PostTypeSlug; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $this->oTemplateListTable->display() ?>
        </form>        
        <?php
                
    }
    public function do_aal_templates_get() {
        
        echo "<p>" . sprintf( __( 'Want your template to be listed here? Send the file to %1$s.', 'amazon-auto-links' ), 'wpplugins@michaeluno.jp' ) . "</p>";
        $_oExtensionLoader  = new AmazonAutoLinks_ListExtensions();
        $_aFeedItems        = $_oExtensionLoader->fetchFeed( 'http://feeds.feedburner.com/AmazonAutoLinksTemplates' );
        if ( empty( $_aFeedItems ) ) {
            echo "<h3>" . __( 'No extension has been found.', 'amazon-auto-links' ) . "</h3>";
            return;
        }
        $_oExtensionLoader->printColumnOutput( $_aFeedItems );

    }    
        
}