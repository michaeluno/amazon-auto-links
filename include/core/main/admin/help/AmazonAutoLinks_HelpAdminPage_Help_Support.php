<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Adds an in-page tab of `Support` to the `Help` admin page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_HelpAdminPage_Help_Support extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'support',
            'title'     => __( 'Support', 'amazon-auto-links' ),
            'style'     => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/help.css',
        );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _loadTab( $oAdminPage ) {

        new AmazonAutoLinks_RevealerCustomFieldType( $oAdminPage->oProp->sClassName );

        // Sections
        new AmazonAutoLinks_HelpAdminPage_Help_Section_Select( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug ) );
        new AmazonAutoLinks_HelpAdminPage_Help_Section_Support( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug ) );
        new AmazonAutoLinks_HelpAdminPage_Help_Section_Feedback( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug ) );
        new AmazonAutoLinks_HelpAdminPage_Help_Section_BugReport( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug ) );
        add_action( "do_form_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToDoForm' ) );

    }

    /**
     * @param $oFactory
     * @callback add_action() do_form_{page slug}_{tab_slug}
     */
    public function replyToDoForm( $oFactory ) {
    }

    /**
     * 
     * @callback        action      do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {}
            
}