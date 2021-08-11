<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds an in-page tab of `Get Pro` to the `Help` admin page.
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
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action() load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        $_sPath = $oFactory->oUtil->isDebugMode()
            ? AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/get_pro.css'
            : AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/get_pro.min.css';
        $oFactory->enqueueStyle( $_sPath );
    }
    
    /**
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action() do_{page slug}_{tab slug}
     */
    public function replyToDoTab( $oFactory ) {
        
        $_sCheckMark        = esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/upgrade/checkmark.gif', true ) );
        $_sDeclineMark      = esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/upgrade/declinedmark.gif', true ) );
        $_sAvailable        = __( 'Available', 'amazon-auto-links' );
        $_sUnavailable      = __( 'Unavailable', 'amazon-auto-links' );
        $_sImgAvailable     = "<img class='feature-available' title='" . esc_attr( $_sAvailable ) . "' alt='" . esc_attr( $_sAvailable ) . "' src='" . esc_attr( $_sCheckMark ) . "' />";
        $_sImgUnavailable   = "<img class='feature-unavailable' title='" . esc_attr( $_sUnavailable ) . "' alt='" . esc_attr( $_sUnavailable ) . "' src='" . esc_attr( $_sDeclineMark ) . "' />";
        $_aRows             = array(
            array(
                'text' => __( 'Image Size', 'amazon-auto-links' ),
                'free' => $_sImgAvailable,
                'pro'  => $_sImgAvailable,
            ),
            array(
                'text' => __( 'Black and White List', 'amazon-auto-links' ),
                'free' => $_sImgAvailable,
                'pro'  => $_sImgAvailable,
            ),
            array(
                'text' => __( 'Sort Order', 'amazon-auto-links' ),
                'free' => $_sImgAvailable,
                'pro'  => $_sImgAvailable,
            ),
            array(
                'text' => __( 'Insert in Posts and Feeds', 'amazon-auto-links' ),
                'free' => $_sImgAvailable,
                'pro'  => $_sImgAvailable,
            ),
            array(
                'text' => __( 'Widgets', 'amazon-auto-links' ),
                'free' => $_sImgAvailable,
                'pro'  => $_sImgAvailable,
            ),
            array(
                'text' => __( 'Max Number of Items to Show', 'amazon-auto-links' ),
                'free' => 10,
                'pro'  => __( 'Unlimited', 'amazon-auto-links' ),
            ),
            array(
                'text' => __( 'Max Number of Units', 'amazon-auto-links' ),
                'free' => 3,
                'pro'  => __( 'Unlimited', 'amazon-auto-links' ),
            ),
            array(
                'text' => __( 'Exclude Sub Categories', 'amazon-auto-links' ),
                'free' => $_sImgUnavailable,
                'pro'  => $_sImgAvailable,
            ),
            array(
                'text' => __( 'Multiple Columns', 'amazon-auto-links' ),
                'free' => $_sImgUnavailable,
                'pro'  => $_sImgAvailable,
            ),
            array(
                'text' => __( 'Carousel Template', 'amazon-auto-links' ),
                'free' => $_sImgUnavailable,
                'pro'  => $_sImgAvailable,
            ),
            array(
                'text' => __( 'Advanced Search Options', 'amazon-auto-links' ),
                'free' => $_sImgUnavailable,
                'pro'  => $_sImgAvailable,
            ),
        );

    ?>
        <h3><?php esc_html_e( 'Get Pro Now!', 'amazon-auto-links' ); ?></h3>
        <p class="description"><?php esc_html_e( 'Please consider upgrading to the pro version if you like the plugin and want more useful features, which includes unlimited numbers of categories, units, and items, and more!', 'amazon-auto-links' ); ?></p>
        <?php $this->___printBuyNowButton(); ?>
        <h3><?php esc_html_e( 'Supported Features', 'amazon-auto-links' ); ?></h3>
        <div class="get-pro">
            <table class="aal-table" cellspacing="0" cellpadding="10">
                <tbody>
                    <tr class="aal-table-head">
                        <th>&nbsp;</th>
                        <th><?php esc_html_e( 'Standard', 'amazon-auto-links' ); ?></th>
                        <th><?php esc_html_e( 'Pro', 'amazon-auto-links' ); ?></th>
                    </tr>
                    <?php
                    foreach( $_aRows as $_aRow ):
                        echo "<tr class='aal-table-row'>"
                         . "<td>" . esc_html( $_aRow[ 'text' ] ) . "</td>"
                         . "<td>" . wp_kses( $_aRow[ 'free' ], 'post' ) . "</td>"
                         . "<td>" . wp_kses( $_aRow[ 'pro' ], 'post' ) . "</td>";
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>    
        <h4><?php esc_html_e( 'Max Number of Items to Show', 'amazon-auto-links' ); ?></h4>
        <p><?php  esc_html_e( 'Get pro for unlimited items to show.', 'amazon-auto-links' ); ?></p>
        <h4><?php esc_html_e( 'Max Number of Units', 'amazon-auto-links' ); ?></h4>
        <p><?php  esc_html_e( 'Get pro for unlimited units so that you can put ads as many as you want.', 'amazon-auto-links' ); ?></p>
        
        <?php 
            $this->___printBuyNowButton();
        
    }
        private function ___printBuyNowButton() {
        
            $_sURL     = 'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/';
            $_sLang    = defined( 'WPLANG' ) ? WPLANG : 'en';
            $_sMessage = __( 'Get Now!', 'amazon-auto-links' );
            ?>
            <div class="get-now-button">
                <a target="_blank" href="<?php echo esc_url( $_sURL ); ?>?lang=<?php echo $_sLang; ?>" title="<?php echo esc_attr( $_sMessage ); ?>">
                    <img src="<?php echo esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/upgrade/buynowbutton.gif', true ) ); ?>" alt="<?php echo esc_attr( $_sMessage ); ?>" />
                </a>
            </div>    
            <?php

        }

}