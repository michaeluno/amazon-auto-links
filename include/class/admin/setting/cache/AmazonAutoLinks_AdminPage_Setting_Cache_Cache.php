<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Cache' form section to the 'Cache' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Cache_Cache extends AmazonAutoLinks_AdminPage_Section_Base {
    
    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    public function addFields( $oFactory, $sSectionID ) {
        
        $_oProductTable    = new AmazonAutoLinks_DatabaseTable_product(
            AmazonAutoLinks_Registry::$aDatabaseTables[ 'product' ]
        );                     
        $_iProductCount    = $_oProductTable->getVariable( 
            "SELECT COUNT(*) FROM {$_oProductTable->sTableName}"
        );
        $_iExpiredProducts = $_oProductTable->getVariable( 
            "SELECT COUNT(*) FROM {$_oProductTable->sTableName} "
            . "WHERE expiration_time < NOW()" 
        );
        
        $_oCacheTable      = new AmazonAutoLinks_DatabaseTable_request_cache(
            AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
        );        
        $_iRequestCount    = $_oCacheTable->getVariable( 
            "SELECT COUNT(*) FROM {$_oCacheTable->sTableName}"
        );
        $_iExpiredRequests = $_oCacheTable->getVariable( 
            "SELECT COUNT(*) FROM {$_oCacheTable->sTableName} "
            . "WHERE expiration_time < NOW()" 
        );
        
        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array( 
                'field_id'        => 'submit_clear_all_caches',
                'type'            => 'submit',
                'title'           => __( 'Clear All Caches', 'adazon-auto-links' ),
                'label_min_width' => 0,
                'label'           => __( 'Clear', 'amazon-auto-links' ),
                'attributes'      => array(
                    'class' => 'button button-secondary',
                ),
                'before_fieldset' => ''
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong>: ' . $_iProductCount 
                    . '</p>'
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Requests', 'amazon-auto-links' ) . '</strong>: ' . $_iRequestCount 
                    . '</p>',
            ),            
            array( 
                'field_id'        => 'submit_clear_expired_caches',
                'type'            => 'submit',
                'title'           => __( 'Clear Expired Caches', 'adazon-auto-links' ),
                'label_min_width' => 0,
                'label'           => __( 'Clear', 'amazon-auto-links' ),
                'attributes'      => array(
                    'class' => 'button button-secondary',
                ),
                'before_fieldset' => ''
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong>: ' . $_iExpiredProducts 
                    . '</p>'
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Requests', 'amazon-auto-links' ) . '</strong>: ' . $_iExpiredRequests 
                    . '</p>',                
            ),
            array(
                'field_id'          => 'chaching_mode',
                'type'              => 'radio',
                'title'             => __( 'Caching Mode', 'amazon-auto-links' ),
                'capability'        => 'manage_options',
                'label' => array(
                    'normal'        => __( 'Normal', 'amazon-auto-links' ) . ' - ' . __( 'relies on WP Cron.', 'amazon-auto-links' ) . '<br />',
                    'intense'       => __( 'Intense', 'amazon-auto-links' ) . ' - ' . __( 'relies on the plugin caching method.', 'amazon-auto-links' ) . '<br />',
                ),
                'description'       => __( 'The intense mode should only be enabled when the normal mode does not work.', 'amazon-auto-links' ),
                'default' => 'normal',
            )            
        );    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3
     */
    public function validate( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        
        if ( 'submit_clear_all_caches' === $aSubmitInfo[ 'field_id' ] ) {
            $this->_clearAllCaches( $oAdminPage );
            return $aOldInput;            
        }
        if ( 'submit_clear_expired_caches' === $aSubmitInfo[ 'field_id' ] ) {
            $this->_clearExpiredCaches( $oAdminPage );
            return $aOldInput;
        }
                

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInput;
        }
                
        return $aInput;     
        
    }
    
        /**
         * Clears all the plugin caches.
         * @since       3
         * @return      void
         */
        private function _clearAllCaches( $oFactory ) {
            
            // Clear transients.
            AmazonAutoLinks_WPUtility::cleanTransients( 
                AmazonAutoLinks_Registry::TRANSIENT_PREFIX
            );
            AmazonAutoLinks_WPUtility::cleanTransients( 
                'apf_'
            );            
            
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_request_cache(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
            );           
            $_oCacheTable->delete(
                // delete all rows by passing nothing.
            );    

            $_oProductTable    = new AmazonAutoLinks_DatabaseTable_product(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'product' ]
            );             
            $_oProductTable->delete(
                // delete all rows.
            ); 
            
            $oFactory->setSettingNotice( __( 'Caches have been cleared.', 'amazon-auto-links' ) );
        }
        /**
         * Clears expired caches.
         * @since       3
         * @return      void
         */
        private function _clearExpiredCaches( $oFactory ) {

            $_oCacheTable   = new AmazonAutoLinks_DatabaseTable_request_cache(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'request_cache' ]
            );           
            $_oCacheTable->deleteExpired();
            $_oProductTable = new AmazonAutoLinks_DatabaseTable_product(
                AmazonAutoLinks_Registry::$aDatabaseTables[ 'product' ]
            );             
            $_oProductTable->deleteExpired();
            
            // DELETE FROM table WHERE (col1,col2) IN ((1,2),(3,4),(5,6))
            $oFactory->setSettingNotice( __( 'Caches have been cleared.', 'amazon-auto-links' ) );
        }   
}