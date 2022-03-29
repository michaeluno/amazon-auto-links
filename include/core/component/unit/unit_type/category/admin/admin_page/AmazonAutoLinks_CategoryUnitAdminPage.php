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
 * Deals with the plugin admin pages.
 * 
 * @since 3
 */
final class AmazonAutoLinks_CategoryUnitAdminPage extends AmazonAutoLinks_Unit_Admin_Page_UnitCreationWizard {

    /**
     * User constructor.
     */
    public function start() {
        
        parent::start();
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
                
        // For the create new unit page. Disable the default one.
        if ( $this->isUserClickedAddNewLink( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] ) ) {
            exit(
                wp_safe_redirect(
                    add_query_arg(
                        array( 
                            'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                            'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'category_select' ],
                        ), 
                        admin_url( 'edit.php' )
                    )
                )
            );
        }

    }

        
    /**
     * Sets the default option values for the setting form.
     * @callback add_filter() options_{class name}
     * @return   array        The options array.
     */
    public function setOptions( $aOptions ) {

        $_aUnitOptions = array();
        if ( isset( $_GET[ 'post' ] ) ) {   // sanitization unnecessary as just checking
            $_oOption      = AmazonAutoLinks_Option::getInstance();
            $_aUnitOptions = AmazonAutoLinks_WPUtility::getPostMeta( absint( $_GET[ 'post' ] ), '', $_oOption->get( 'unit_default' ) );     // sanitization done
        }
        
        // Set some items for the edit mode.
        $_iMode    = ! isset( $_GET[ 'post' ] ); // 0: edit, 1: new // sanitization unnecessary as just checking
        $_aOptions = array(
            'mode'       => $_iMode,
        );
        if ( ! $_iMode ) {
            $_aOptions[ 'bounce_url' ] = AmazonAutoLinks_WPUtility::getPostDefinitionEditPageURL(
                absint( $_GET[ 'post' ] ),  // post id  // sanitization done
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            );
        }
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return $aOptions
            + $_aOptions
            + $_aUnitOptions
            + $this->_getLastUnitInputs()
            + $_oOption->get( 'unit_default' )  // 3.4.0+
            ;
        
    }

    /**
     * Adds admin pages.
     * @since 5.0.0
     */
    protected function _addPages() {
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect( $this );
    }

    /**
     * Page styling
     * @since 3
     */
    public function doPageSettings() {
        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $this->setDisallowedQueryKeys( array( 'aal-option-upgrade', 'bounce_url' ) );
    }

}