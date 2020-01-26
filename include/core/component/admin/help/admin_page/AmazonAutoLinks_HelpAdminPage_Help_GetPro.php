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
 * Adds an in-page tab to an admin page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_HelpAdminPage_Help_GetPro extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'get_pro',
            'title'     => __( 'Get Pro', 'amazon-auto-links' ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        
        $oFactory->enqueueStyle( 
            AmazonAutoLinks_Registry::getPluginURL( 'asset/css/get_pro.css' ) 
        );   
    }
    
    /**
     * 
     * @callback        action      do_{page slug}_{tab slug}
     */
    public function replyToDoTab( $oFactory ) {
        
        $_sCheckMark        = esc_url( AmazonAutoLinks_Registry::getPluginURL( 'asset/image/checkmark.gif' ) );
        $_sDeclineMark      = esc_url( AmazonAutoLinks_Registry::getPluginURL( 'asset/image/declinedmark.gif' ) );
        $_sAvailable        = __( 'Available', 'amazon-auto-links' );
        $_sUnavailable      = __( 'Unavailable', 'amazon-auto-links' );
        $_sImgAvailable     = "<img class='feature-available' title='{$_sAvailable}' alt='{$_sAvailable}' src='{$_sCheckMark}' />";
        $_sImgUnavailable   = "<img class='feature-unavailable' title='{$_sUnavailable}' alt='{$_sUnavailable}' src='{$_sDeclineMark}' />";
        
    ?>
        <h3><?php _e( 'Get Pro Now!', 'amazon-auto-links' ); ?></h3>
        <p><?php _e( 'Please consider upgrading to the pro version if you like the plugin and want more useful features, which includes unlimited numbers of categories, units, and items, and more!', 'amazon-auto-links' ); ?></p>
        <?php $this->_printBuyNowButton(); ?>
        <h3><?php _e( 'Supported Features', 'amazon-auto-links' ); ?></h3>
        <div class="get-pro">
            <table class="aal-table" cellspacing="0" cellpadding="10">
                <tbody>
                    <tr class="aal-table-head">
                        <th>&nbsp;</th>
                        <th><?php _e( 'Standard', 'amazon-auto-links' ); ?></th>
                        <th><?php _e( 'Pro', 'amazon-auto-links' ); ?></th>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Image Size', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Black and White List', 'amazon-auto-links'); ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Sort Order', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Direct Link Bonus', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Insert in Posts and Feeds', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Widget', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>    
                    <tr class="aal-table-row">
                        <td><?php _e( 'Max Number of Items to Show', 'amazon-auto-links' ); ?></td>
                        <td>10</td>
                        <td><strong><?php _e( 'Unlimited', 'amazon-auto-links' ); ?></strong></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Max Number of Categories Per Unit', 'amazon-auto-links' ); ?></td>
                        <td>3</td>
                        <td><strong><?php _e( 'Unlimited', 'amazon-auto-links' ); ?></strong></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Max Number of Units', 'amazon-auto-links' ); ?></td>
                        <td>3</td>
                        <td><strong><?php _e( 'Unlimited', 'amazon-auto-links' ); ?></strong></td>
                    </tr>        
                    <tr class="aal-table-row">
                        <td><?php _e( 'Export and Import Units', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgUnavailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>                        
                    <tr class="aal-table-row">
                        <td><?php _e( 'Exclude Sub Categories', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgUnavailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>                    
                    <tr class="aal-table-row">
                        <td><?php _e( 'Multiple Columns', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgUnavailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>                        
                    <tr class="aal-table-row">
                        <td><?php _e( 'Advanced Search Options', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $_sImgUnavailable; ?></td>
                        <td><?php echo $_sImgAvailable; ?></td>
                    </tr>                            
                </tbody>
            </table>
        </div>    
        <h4><?php   _e( 'Max Number of Items to Show', 'amazon-auto-links' ); ?></h4>
        <p><?php    _e( 'Get pro for unlimited items to show.', 'amazon-auto-links' ); ?></p>        
        <h4><?php   _e( 'Max Number of Categories Per Unit', 'amazon-auto-links' ); ?></h4>
        <p><?php    _e( 'Get pro for unlimited categories to set up!', 'amazon-auto-links' ); ?></p>        
        <h4><?php   _e( 'Max Number of Units', 'amazon-auto-links' ); ?></h4>
        <p><?php    _e( 'Get pro for unlimited units so that you can put ads as many as you want.', 'amazon-auto-links' ); ?></p>        
        
        <?php 
            $this->_printBuyNowButton(); 
        
    }
        private function _printBuyNowButton() {    
        
            $_sLink = 'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/';
            $_sLang = defined( 'WPLANG' ) 
                ? WPLANG
                : 'en';
            ?>
            <div class="get-now-button">
                <a target="_blank" href="<?php echo esc_url( $_sLink ); ?>?lang=<?php echo $_sLang; ?>" title="<?php _e( 'Get Now!', 'amazon-auto-links' ) ?>">
                    <img src="<?php echo AmazonAutoLinks_Registry::getPluginURL( 'asset/image/buynowbutton.gif' ); ?>" />
                </a>
            </div>    
            <?php
        }
        
            
}