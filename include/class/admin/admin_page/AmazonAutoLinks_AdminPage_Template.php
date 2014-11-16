<?php
/**
 * Deals with the plugin admin pages.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since		2.0.5
 * 
 */
abstract class AmazonAutoLinks_AdminPage_Template extends AmazonAutoLinks_AdminPage_Extension {

	/*
	 * The Template page
	 */ 
	public function load_aal_templates() {	// load_ + {page slug}

		// For the list table bulk actions. The WP_List_Table class does not set the post type query string in the redirected page.
		// if ( 
			// ( isset( $_POST['post_type'] ) && $_POST['post_type'] == AmazonAutoLinks_Commons::PostTypeSlug )	// the form is submitted 
			// && ( ! isset( $_GET['post_type'] ) )	// and post_type query string is not in the url
			// && ( isset( $_GET['page'] ) && $_GET['page'] == 'templates' ) // and the page is the template listing table page,
		// )
			// die( wp_redirect( add_query_arg( array( 'post_type' => AmazonAutoLinks_Commons::PostTypeSlug ) + $_GET, admin_url( $GLOBALS['pagenow'] )  ) ) );

		$oTemplate = $GLOBALS['oAmazonAutoLinks_Templates'];		
		$this->oTemplateListTable = new AmazonAutoLinks_ListTable( $oTemplate->getActiveTemplates() + $oTemplate->getUploadedTemplates() );
		$this->oTemplateListTable->process_bulk_action();
		
	}		
	public function do_aal_templates_table() {	// do_ + page slug + tab slug
			
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
		$oExtensionLoader = new AmazonAutoLinks_ListExtensions();
		$arrFeedItems = $oExtensionLoader->fetchFeed( 'http://feeds.feedburner.com/AmazonAutoLinksTemplates' );
		if ( empty( $arrFeedItems ) ) {
			echo "<h3>" . __( 'No extension has been found.', 'amazon-auto-links' ) . "</h3>";
			return;
		}
		$oExtensionLoader->printColumnOutput( $arrFeedItems );

	}	
		
}