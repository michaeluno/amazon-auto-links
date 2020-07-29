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
 * Adds the `Tools` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_ToolAdminPage extends AmazonAutoLinks_AdminPageFramework {

    /**
     * User constructor.
     */
    public function start() {
        add_filter(
            'options_' . $this->oProp->sClassName,
            array( $this, 'replyToSetOptions' )
        );
    }
        /**
         * Sets the default option values for the setting form.
         * @return      array       The options array.
         */
        public function replyToSetOptions( $aOptions ) {

            $_oOption    = AmazonAutoLinks_ToolOption::getInstance();

            // Merging recursively (3.4.0+) to cover newly added elements over new versions.
            return $this->oUtil->uniteArrays(
                $aOptions,
                $_oOption->aDefault
            );

        }

    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
    
        new AmazonAutoLinks_ToolAdminPage_Tool( $this );
        
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.      
        
    }

}