<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Provides methods for handing user submitting actions.
 * 
 * @package     Amazon Auto Links
 * @since       3.1.0
 */
class AmazonAutoLinks_PostType_Unit_Action extends AmazonAutoLinks_PostType_Unit_ListTable {
    
    /**
     * Stores a custom nonce.
     */
    public $sCustomNonce;
    
    /**
     * Sets up hooks.
     * @since       3.2.0
     */
    public function setUp() {
    
        if (  $this->_isInThePage() ) {

            $this->handleCustomActions();   

            $this->sCustomNonce = uniqid();            
            AmazonAutoLinks_WPUtility::setTransient( 
                'AAL_Nonce_' . $this->sCustomNonce, 
                $this->sCustomNonce, 
                60*10 
            );

            // Add an warning icon to the tag unit type's action link.
            add_filter( 
                'action_links_' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                array( $this, 'replyToModifyActionLinks' ), 
                10, 
                2 
            );
            
        }
        
        parent::setUp();
        
    }

    /**
     * @return      array
     * @callback    filter      action_links_{post type slug}
     */
    public function replyToModifyActionLinks( $aActionLinks, $oPost ){
                
        $aActionLinks = $this->_getTagUnitTypeWarning( $aActionLinks, $oPost );
        
        /**
         * @todo Implement the ability to clear caches used by the unit.
         * This is complex than it seems because currently unit does not have a method to return
         * cache ids. Also, API request uris need to have signature with requested dates so the uri constantly changes.
         * Therefore, the plugin API cache renewal mechanism is a bit complicated. 
         * This may require a bit of refinement of the cache mechanism.
         */
        //  $aActionLinks = $this->_getClearCacheActionLink( $aActionLinks, $oPost );
        
        $aActionLinks = $this->_getCloneActionLink( $aActionLinks, $oPost );

        return $aActionLinks;
        
        
    }    
        /**
         * @since       3.3.0
         * @return      array
         */
        private function _getClearCacheActionLink( $aActionLinks, $oPost ) {
            
            $_sURL = add_query_arg( 
                array( 
                    'post_type'     => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    'custom_action' => 'clear_cache',
                    'post'          => $oPost->ID,
                    'nonce'         => $this->sCustomNonce,
                ), 
                admin_url( $this->oProp->sPageNow ) 
            );    
            $_sLabel = __( 'Clear Cache', 'amazon-auto-links' );
            $aActionLinks[ 'clear_cache' ] = "<a href='{$_sURL}' title='{$_sLabel}'>"
                        . $_sLabel
                    . "</a> ";
            return $aActionLinks;
            
        }
        /**
         * @since       3.3.0
         * @return      array
         */
        private function _getCloneActionLink( $aActionLinks, $oPost ) {
            
            $_sURL = add_query_arg( 
                array( 
                    'post_type'     => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    'custom_action' => 'clone_unit',
                    'post'          => $oPost->ID,
                    'nonce'         => $this->sCustomNonce,
                ), 
                admin_url( $this->oProp->sPageNow ) 
            );    
            $_sLabel    = __( 'Clone', 'amazon-auto-links' );
            $_sTitle    = __( 'Clone Unit', 'amazon-auto-links' );
            
            $_oOption   = AmazonAutoLinks_Option::getInstance();            
            $aActionLinks[ 'clone_unit' ] = $_oOption->canCloneUnits()
                ? "<a href='{$_sURL}' title='{$_sTitle}'>"
                    . $_sLabel
                . "</a> "
                : "<span class='disabled' title='" . esc_attr( AmazonAutoLinks_PluginUtility::getUpgradePromptMessage( false /* no link */ ) ) . "'>"
                    . $_sLabel
                . "</span>";
            return $aActionLinks;
        }        
        /**
         * @since       3.3.0
         * @return      array
         */
        private function _getTagUnitTypeWarning( $aActionLinks, $oPost ) {
            $_sUnitType = get_post_meta( $oPost->ID, 'unit_type', true );
            if ( 'tag' !== $_sUnitType )  {
                return $aActionLinks;
            }
            $aActionLinks[ 'tag_deprecated_warning' ] = $this->_getTagDeprecateWarning();            
            return $aActionLinks;
        }
            /**
             * @since       3.2.0
             * @return      string       
             */
            private function _getTagDeprecateWarning() {
                
                $_sTitle              = esc_attr( 
                    __( 'Amazon has deprecated the tags feature. So this is no longer functional.', 'amazon-auto-links' )
                );
                $_sWarning            = esc_attr( __( 'Warning!', 'amazon-auto-links' ) );
                $_sURL                = 'https://www.amazon.com/gp/help/customer/display.html?nodeId=16238571';
                $_sExclamationIconURL = AmazonAutoLinks_Registry::getPluginURL( 'asset/image/exclamationmark_16x16.png' );
                return "<a href='{$_sURL}' target='_blank'>"
                    . "<img src='{$_sExclamationIconURL}' alt='{$_sWarning}' title='{$_sTitle}' />"
                    . "</a> ";
                
            }    
    
    /**
     * @since       3.1.0
     */
    protected function handleCustomActions() {
        
        if ( ! isset( $_GET[ 'custom_action' ], $_GET[ 'nonce' ], $_GET[ 'post' ], $_GET[ 'post_type' ] ) ) { 
            return; 
        }
        // If a WordPress action is performed, do nothing.
        if ( isset( $_GET[ 'action' ] ) ) {
            return;
        }
        
        $_sNonce = $this->oUtil->getTransient( 'AAL_Nonce_' . $_GET[ 'nonce' ] );
        if ( false === $_sNonce ) { 
            new AmazonAutoLinks_AdminPageFramework_AdminNotice(
                __( 'The action could not be processed due to the inactivity.', 'amazon-auto-links' ),
                array(
                    'class' => 'error',
                )
            );
            return;
        }
        $this->oUtil->deleteTransient( 'AAL_Nonce_' . $_GET['nonce'] );

        $_sClassName = "AmazonAutoLinks_ListTableAction_{$_GET[ 'custom_action' ]}";
        if ( class_exists( $_sClassName ) ) {
            new $_sClassName( $this->oUtil->getAsArray( $_GET[ 'post' ] ), $this );
        }
        
        // Reload the page without query arguments so that the admin notice will not be shown in the next page load with other actions.
        $_sURLSendback = add_query_arg(
            array(
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
            ),
            admin_url( 'edit.php' )
        );
        wp_safe_redirect( $_sURLSendback );    
        exit();
    
    }
    

    
  
    
}