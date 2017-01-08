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
abstract class AmazonAutoLinks_AdminPage_About extends AmazonAutoLinks_AdminPage_Debug {

     /*
     * The About page
     */
    public function do_before_aal_about() {        // do_before_ + {page slug}

        include_once( AmazonAutoLinks_Commons::$strPluginDirPath . '/include/library/wordpress-plugin-readme-parser/parse-readme.php' );
        $this->oWPReadMe = new WordPress_Readme_Parser;
        $this->arrWPReadMe = $this->oWPReadMe->parse_readme( AmazonAutoLinks_Commons::$strPluginDirPath . '/readme.txt' );
    
    }
    public function do_aal_about_features() {        // do_ + page slug + _ + tab slug
        echo $this->arrWPReadMe['sections']['description'];
    }
    public function do_aal_about_change_log() {        // do_ + page slug + _ + tab slug
        echo "<p>" . sprintf( __( 'The other versions of Amazon Auto Links can be downloaded from <a href="%1$s">this page</a>.', 'amazon-auto-links' ), 'http://wordpress.org/plugins/amazon-auto-links/developers/' ) . "</p>";
        echo $this->arrWPReadMe['sections']['changelog'];
    }
    public function do_aal_about_get_pro() {
        
        $strCheckMark = AmazonAutoLinks_Commons::getPluginURL( 'asset/image/checkmark.gif' );
        $strDeclineMark = AmazonAutoLinks_Commons::getPluginURL( 'asset/image/declinedmark.gif' );
        $strAvailable = __( 'Available', 'amazon-auto-links' );
        $strUnavailable = __( 'Unavailable', 'amazon-auto-links' );
        $strImgAvailable = "<img class='feature-available' title='{$strAvailable}' alt='{$strAvailable}' src='{$strCheckMark}' />";
        $strImgUnavailable = "<img class='feature-unavailable' title='{$strUnavailable}' alt='{$strUnavailable}' src='{$strDeclineMark}' />";
        
    ?>
        <h3><?php _e( 'Get Pro Now!', 'amazon-auto-links' ); ?></h3>
        <p><?php _e( 'Please consider upgrading to the pro version if you like the plugin and want more useful features, which includes unlimited numbers of categories, units, and items, and more!', 'amazon-auto-links' ); ?></p>
        <?php $this->printBuyNowButton(); ?>
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
                        <td><?php echo $strImgAvailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Black and White List', 'amazon-auto-links'); ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Sort Order', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Direct Link Bonus', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Insert in Posts and Feeds', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>
                    <tr class="aal-table-row">
                        <td><?php _e( 'Widget', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
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
                        <td><?php echo $strImgUnavailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>                        
                    <tr class="aal-table-row">
                        <td><?php _e( 'Exclude Sub Categories', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $strImgUnavailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>                    
                    <tr class="aal-table-row">
                        <td><?php _e( 'Multiple Columns', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $strImgUnavailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>                        
                    <tr class="aal-table-row">
                        <td><?php _e( 'Advanced Search Options', 'amazon-auto-links' ); ?></td>
                        <td><?php echo $strImgUnavailable; ?></td>
                        <td><?php echo $strImgAvailable; ?></td>
                    </tr>                            
                </tbody>
            </table>
        </div>    
        <h4><?php    _e( 'Max Number of Items to Show', 'amazon-auto-links' ); ?></h4>
        <p><?php    _e( 'Get pro for unlimited items to show.', 'amazon-auto-links' ); ?></p>        
        <h4><?php    _e( 'Max Number of Categories Per Unit', 'amazon-auto-links' ); ?></h4>
        <p><?php    _e( 'Get pro for unlimited categories to set up!', 'amazon-auto-links' ); ?></p>        
        <h4><?php    _e( 'Max Number of Units', 'amazon-auto-links' ); ?></h4>
        <p><?php    _e( 'Get pro for unlimited units so that you can put ads as many as you want.', 'amazon-auto-links' ); ?></p>        
        
        <?php 
            $this->printBuyNowButton(); 
        
    }
        protected function printBuyNowButton() {    
            $_sLink = 'http://en.michaeluno.jp/amazon-auto-links/amazon-auto-links-pro';
            $_sLang = defined( 'WPLANG' ) 
                ? WPLANG
                : 'en';
            ?>
            <div class="get-now-button">
                <a href="<?php echo esc_url( $_sLink ); ?>?lang=<?php echo $_sLang; ?>" title="<?php _e( 'Get Now!', 'amazon-auto-links' ) ?>">
                    <img src="<?php echo AmazonAutoLinks_Commons::getPluginURL( 'asset/image/buynowbutton.gif' ); ?>" />
                </a>
            </div>    
            <?php
        }
    
    public function do_aal_about_contact() {
        include( AmazonAutoLinks_Commons::$strPluginDirPath . '/include/text/about.txt' );
    }     
        
}