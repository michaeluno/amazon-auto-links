<?php
/**
 * Deals with the plugin admin pages.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.5
 * 
 */
abstract class AmazonAutoLinks_AdminPage_Menu extends AmazonAutoLinks_AdminPage_SetUp {

      /*
     * Customize the Menu
     */
    public function buildMenus() {
    
        parent::buildMenus();

        // Somehow the settings link in the plugin listing page points to the Create Rule by List page. So fix it to the Settings page.
        $this->oProps->strDefaultPageSlug = 'aal_settings';
        
        // Remove the default post type menu item.
        $strPageSlug = $this->oProps->arrRootMenu['strPageSlug'];
        if ( ! isset( $GLOBALS['submenu'][ $strPageSlug ] ) ) return;    // logged-in users of an insufficient access level don't have the menu to be registered.
        foreach ( $GLOBALS['submenu'][ $strPageSlug ] as $intIndex => $arrSubMenu ) {
                        
            if ( ! isset( $arrSubMenu[ 2 ] ) ) continue;
            
            // Remove the default Add New entry.
            if ( $arrSubMenu[ 2 ] == 'post-new.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlug ) {
                unset( $GLOBALS['submenu'][ $strPageSlug ][ $intIndex ] );
                continue;
            }
            
            // Edit the first item
            if ( $arrSubMenu[ 2 ] == 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlug ) {
                $GLOBALS['submenu'][ $strPageSlug ][ $intIndex ][ 0 ] = __( 'Manage Units', 'amazon-auto-links' );
                continue;
            }

            // Copy and remove the Tag menu element to change the position. 
            if ( $arrSubMenu[ 2 ] == 'edit-tags.php?taxonomy=' . AmazonAutoLinks_Commons::TagSlug . '&amp;post_type=' . AmazonAutoLinks_Commons::PostTypeSlug ) {
                $_aMenuEntry_Tag = array( $GLOBALS['submenu'][ $strPageSlug ][ $intIndex ] );
                unset( $GLOBALS['submenu'][ $strPageSlug ][ $intIndex ] );
                continue;                
            }

        }
        
        // Second iterations.
        $intMenuPos_Setting = -1;
        foreach ( $GLOBALS['submenu'][ $strPageSlug ] as $intIndex => $arrSubMenu ) {
            
            $intMenuPos_Setting++;    
            if (  isset( $arrSubMenu[ 2 ] ) && $arrSubMenu[ 2 ] == 'aal_settings' ) 
                break;    // the position variable will now contain the position of the Setting menu item.
    
        }
    
        // Insert the Tag menu item before the Settings menu item.
        if ( isset( $_aMenuEntry_Tag ) )
            array_splice( 
                $GLOBALS['submenu'][ $strPageSlug ], // original array
                $intMenuPos_Setting,     // position
                0,     // offset - should be 0
                $_aMenuEntry_Tag     // replacement array
            );        

        // Unfortunately array_splice() will lose all the associated keys(index).
        
    }
    
        
}