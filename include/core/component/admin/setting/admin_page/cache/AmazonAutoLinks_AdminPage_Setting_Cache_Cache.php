<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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
        
        $_oProductTable    = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_iProductCount    = $_oProductTable->getTotalItemCount();
        $_iExpiredProducts = $_oProductTable->getExpiredItemCount();
        
        $_oCacheTable      = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_iRequestCount    = $_oCacheTable->getTotalItemCount();
        $_iExpiredRequests = $_oCacheTable->getExpiredItemCount();
        
        $oFactory->addSettingFields(
            $sSectionID, // the target section id   
            array( 
                'field_id'        => '_table_sizes',
                'title'           => __( 'Sizes', 'amazon-auto-links' ),
                'content' => ''
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong>: ' 
                            . $_oProductTable->getTableSize()
                    . '</p>'
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Requests', 'amazon-auto-links' ) . '</strong>: ' 
                            . $_oCacheTable->getTableSize()
                    . '</p>',
            ),                   
            array( 
                'field_id'        => 'submit_clear_all_caches',
                'type'            => 'submit',
                'title'           => __( 'Clear All Caches', 'amazon-auto-links' ),
                'label_min_width' => 0,
                'label'           => __( 'Clear', 'amazon-auto-links' ),
                'attributes'      => array(
                    'class' => 'button button-secondary',
                ),
                'before_fieldset' => ''
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong>: ' 
                        . sprintf( __( '%1$s item(s).', 'amazon-auto-links' ), $_iProductCount )
                    . '</p>'
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Requests', 'amazon-auto-links' ) . '</strong>: ' 
                        . sprintf( __( '%1$s item(s).', 'amazon-auto-links' ), $_iRequestCount )
                    . '</p>',
            ),            
            array( 
                'field_id'        => 'submit_clear_expired_caches',
                'type'            => 'submit',
                'title'           => __( 'Clear Expired Caches', 'amazon-auto-links' ),
                'label_min_width' => 0,
                'label'           => __( 'Clear', 'amazon-auto-links' ),
                'attributes'      => array(
                    'class' => 'button button-secondary',
                ),
                'before_fieldset' => ''
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong>: ' 
                        . sprintf( __( '%1$s item(s).', 'amazon-auto-links' ), $_iExpiredProducts )
                    . '</p>'
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Requests', 'amazon-auto-links' ) . '</strong>: ' 
                        . sprintf( __( '%1$s item(s).', 'amazon-auto-links' ), $_iExpiredRequests )
                    . '</p>',                
            ),
            array(
                'field_id'          => 'caching_mode',
                'type'              => 'radio',
                'title'             => __( 'Caching Mode', 'amazon-auto-links' ),
                'capability'        => 'manage_options',
                'label' => array(
                    'normal'        => __( 'Normal', 'amazon-auto-links' ) . ' - ' . __( 'relies on WP Cron to renew caches.', 'amazon-auto-links' ) . '<br />',
                    'intense'       => __( 'Intense', 'amazon-auto-links' ) . ' - ' . __( 'relies on the plugin caching method to renew caches.', 'amazon-auto-links' ) . '<br />',
                ),
                'tip'               => __( 'The intense mode should only be enabled when the normal mode does not work.', 'amazon-auto-links' ),
                'default' => 'normal',
            ),
            array(
                'field_id'          => 'expired_cache_removal_interval',
                'type'              => 'size',
                'title'             => __( 'Interval for Removing Expired Caches', 'amazon-auto-links' ),
                'capability'        => 'manage_options',
                'units'             => array(
                    3600     => __( 'hour(s)', 'amazon-auto-links' ),
                    86400    => __( 'day(s)', 'amazon-auto-links' ),
                    604800   => __( 'week(s)', 'amazon-auto-links' ),
                ),
                'attributes'        => array(
                    'size'      => array(
                        'step' => 0.1
                    ),
                ),
                'default'           => array(
                    'size'      => 7,
                    'unit'      => 'day'
                ),            
            )
        );    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3
     * @param       array       $aInputs        The user set form section values.
     * @return      array
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        
        if ( 'submit_clear_all_caches' === $aSubmitInfo[ 'field_id' ] ) {
            $this->_clearAllCaches( $oAdminPage );
            return $aOldInputs;            
        }
        if ( 'submit_clear_expired_caches' === $aSubmitInfo[ 'field_id' ] ) {
            $this->_clearExpiredCaches( $oAdminPage );
            return $aOldInputs;
        }
                
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }
        
        // If the interval for deleting expired caches changes, update the scheduled task.
        $this->_rescheduleTaskOfDeletingExpiredCaches( $aInputs, $aOldInputs, $oAdminPage );        
        
        return $aInputs;     
        
    }
        /**
         * Update the scheduled task if the interval for deleting expired caches changes, 
         * @since       3.4.0
         * @return      void
         */
        private function _rescheduleTaskOfDeletingExpiredCaches( $aInputs, $aOldInputs, $oAdminPage ) {

            $_aNewExpiredDeleteInterval = $oAdminPage->oUtil->getElement( $aInputs, array( 'expired_cache_removal_interval' ) );
            $_aOldExpiredDeleteInterval = $oAdminPage->oUtil->getElement( $aOldInputs, array( 'expired_cache_removal_interval' ) );
            if ( $_aNewExpiredDeleteInterval === $_aOldExpiredDeleteInterval ) {
                return;
            }          
            
            // At this point, the user changed the value.
            $_aArguments = array();
            $_iInterval  = ( integer ) $oAdminPage->oUtil->getElement( $_aNewExpiredDeleteInterval, array( 'size' ), 7 )
                * ( integer ) $oAdminPage->oUtil->getElement( $_aNewExpiredDeleteInterval, array( 'unit' ), 86400 );
                
            /// Remove the previously set WP-Cron action.
            wp_clear_scheduled_hook( 'aal_action_delete_expired_caches', $_aArguments );
            
            /// Schedule
            wp_schedule_single_event( 
                time() + $_iInterval, // now + interval
                'aal_action_delete_expired_caches', // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                $_aArguments // must be enclosed in an array.
            );            
            
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
            
            $_oCacheTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
            $_oCacheTable->delete(
                // delete all rows by passing nothing.
            );    

            $_oProductTable    = new AmazonAutoLinks_DatabaseTable_aal_products;
            $_oProductTable->delete(
                // delete all rows.
            ); 
            
            $oFactory->setSettingNotice( __( 'Caches have been cleared.', 'amazon-auto-links' ), 'updated' );
        }
        /**
         * Clears expired caches.
         * @since       3
         * @return      void
         */
        private function _clearExpiredCaches( $oFactory ) {

            AmazonAutoLinks_PluginUtility::deleteExpiredTableItems();
            $oFactory->setSettingNotice( __( 'Caches have been cleared.', 'amazon-auto-links' ), 'updated' );
            
        }   
        
}