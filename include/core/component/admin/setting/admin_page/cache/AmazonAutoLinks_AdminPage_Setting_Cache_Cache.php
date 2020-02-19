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
 * Adds the 'Cache' form section to the 'Cache' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Cache_Cache extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @since   3.11.1
     * @return array
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'cache',
            'title'         => __( 'Caches', 'amazon-auto-links' ),
            // 'description'   => array(
                // __( 'Set the criteria to filter fetched items.', 'amazon-auto-links' ),
            // ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function _construct( $oFactory ) {}

    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {
        
        $_oProductTable    = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_iProductCount    = $_oProductTable->getTotalItemCount();
        $_iExpiredProducts = $_oProductTable->getExpiredItemCount();
        
        $_oCacheTable      = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_iRequestCount    = $_oCacheTable->getTotalItemCount();
        $_iExpiredRequests = $_oCacheTable->getExpiredItemCount();

        $_bCacheWritable   = wp_is_writable( WP_CONTENT_DIR );;

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
// From 3.12.0 not implemented yet
/*            array(
                'field_id'          => 'caching_method',
                'type'              => 'revealer',
                'select_type'       => 'radio',
                'title'             => __( 'Cache Method', 'amazon-auto-links' ),
                'capability'        => 'manage_options',
                'label' => array(
                    'database'   => __( 'Database', 'amazon-auto-links' ) . ' - ' . __( 'store caches in the database.', 'amazon-auto-links' ) . '<br />',
                    'file'       => __( 'File', 'amazon-auto-links' ) . ' - ' . __( 'store caches in a directory as actual files.', 'amazon-auto-links' ) . '<br />',
                ),
                'selectors'         => array(
                    'database'   => '.caching_method_database',
                    'file'       => '.caching_method_file',
                ),
                'description'       => array(
                    $_bCacheWritable
                        ? null
                        : "<span class='field-error'>" . __( 'The File caching type cannot be set as the <strong>wp-content</strong> directory is not writable.' ) . "</span>"
                ),
                'default' => 'database',
                'attributes' => array(
                    // 'disabled' => 'disabled',
                    'file' => array(
                        'disabled' => $_bCacheWritable ? null : 'disabled',
                    ),
                    'input' => array(
                        'disabled' => 'disabled',
                    ),
                ),
            ),*/
            array( 
                'field_id'        => '_table_sizes',
                'title'           => __( 'Sizes', 'amazon-auto-links' ),
                'content' => ''
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong>: ' 
                            . $_oProductTable->getTableSize()
                    . '</p>'
                    . "<p style='margin-bottom: 1em;'>" 
                        . '<strong>' . __( 'HTTP Requests', 'amazon-auto-links' ) . '</strong>: '
                            . $_oCacheTable->getTableSize()
                    . '</p>',
                'class'         => array(
                    'fieldrow'  => 'caching_method_database',
                ),
            ),
            array(
                'field_id'        => 'submit_clear_all_caches_types',
                'type'            => 'checkbox',
                'save'            => false,
                'title'           => __( 'Clear Caches', 'amazon-auto-links' ),
                'label'           => array(
                    'products'    => '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong>: '
                        . sprintf( __( '%1$s item(s).', 'amazon-auto-links' ), $_iProductCount ),
                    'http_requests' => '<strong>' . __( 'HTTP Requests', 'amazon-auto-links' ) . '</strong>: '
                        . sprintf( __( '%1$s item(s).', 'amazon-auto-links' ), $_iRequestCount ),
                ),
                'class'         => array(
                    'fieldrow'  => 'caching_method_database',
                ),
            ),
            array( 
                'field_id'        => 'submit_clear_all_caches',
                'type'            => 'submit',
                'save'            => false,
                'label_min_width' => 0,
                'label'           => __( 'Clear', 'amazon-auto-links' ),
                'attributes'      => array(
                    'class' => 'button button-secondary',
                ),
                'class'         => array(
                    'fieldrow'  => 'caching_method_database',
                ),
            ),
            array(
                'field_id'        => 'submit_clear_expired_caches_types',
                'type'            => 'checkbox',
                'save'            => false,
                'title'           => __( 'Clear Expired Caches', 'amazon-auto-links' ),
                'label'           => array(
                    'products'    => '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong>: '
                        . sprintf( __( '%1$s item(s).', 'amazon-auto-links' ), $_iExpiredProducts ),
                    'http_requests' => '<strong>' . __( 'HTTP Requests', 'amazon-auto-links' ) . '</strong>: '
                        . sprintf( __( '%1$s item(s).', 'amazon-auto-links' ), $_iExpiredRequests ),
                ),
                'class'         => array(
                    'fieldrow'  => 'caching_method_database',
                ),
            ),
            array( 
                'field_id'        => 'submit_clear_expired_caches',
                'type'            => 'submit',
                'save'            => false,
                'label_min_width' => 0,
                'label'           => __( 'Clear', 'amazon-auto-links' ),
                'attributes'      => array(
                    'class' => 'button button-secondary',
                ),
                'class'         => array(
                    'fieldrow'  => 'caching_method_database',
                ),
            ),
            array(
                'field_id'          => 'compress',
                'type'              => 'checkbox',
                'title'             => __( 'Compress', 'amazon-auto-links' ),
                'capability'        => 'manage_options',
                'label'             => __( 'Compress caches', 'amazon-auto-links' ),
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
                'title'             => __( 'Cache Clean-up Interval', 'amazon-auto-links' ),
                'tip'               => __( 'With this periodic check, expired cache items will be automatically deleted. If the overall cache size exceeds the set amount, they will be deleted from old.', 'amazon-auto-links' ),
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
                'after_fieldset' => $this->___getCacheCleanupScheduledTime(),
            ),
            array(
                'field_id'  => 'table_size',
                'title'     => __( 'Maximum Overall Cache Sizes', 'amazon-auto-links' ),
                'content'   => array(
                    array(
                        'label'     => __( 'Products', 'amazon-auto-links' ),
                        'field_id'  => 'products',
                        'type'      => 'number',
                        'after_input' => ' mb',
                    ),
                    array(
                        'label'     => __( 'HTTP Requests', 'amazon-auto-links' ),
                        'field_id'  => 'requests',
                        'type'      => 'number',
                        'after_input' => ' mb',
                    ),
                ),
                'description'   => __( 'Leave it blank for unlimited.', 'amazon-auto-links' ),
            )
        );    
    }

        /**
         * @return string
         * @since   3.8.12
         */
        private function ___getCacheCleanupScheduledTime() {

            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_biNextScheduledCheck = wp_next_scheduled( 'aal_action_delete_expired_caches', array() );
            $_iLastRunTime  = ( integer ) $_oOption->get( array( 'cache', 'cache_removal_event_last_run_time' ) );
            $_sLastRunTime  = __( 'Last Run', 'amazon-auto-links' ) . ': ';
            $_sLastRunTime .= $_iLastRunTime
                ? $this->getSiteReadableDate( $_iLastRunTime , get_option( 'date_format' ) . ' g:i a', true )
                : __( 'n/a', 'amazon-auto-links' );
            $_sOutput = false === $_biNextScheduledCheck
                ? "<div>"
                        . "<p class='field-error'>* "
                            . __( 'The periodic check of cache removal is not scheduled.', 'amazon-auto-links' ) . ' '
                            . __( 'It could be a WP Cron issue. Please consult the site administrator.', 'amazon-auto-links' ) . ' '
                            . __( 'If this is left unfixed, caches will not be cleared.', 'amazon-auto-links' )
                        . "</p>"
                        . "<p>" . $_sLastRunTime . "</p>"
                    . "</div>"
                : "<div>"
                        . "<p>"
                            . sprintf(
                                __( 'Next scheduled at %1$s.', 'amazon-auto-links' ),
                                $this->getSiteReadableDate( $_biNextScheduledCheck , get_option( 'date_format' ) . ' g:i a', true )
                            )
                        . "</p>"
                        . "<p>" . $_sLastRunTime . "</p>"
                    . "</div>";
            return $_sOutput;

        }
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3
     * @param       array       $aInputs        The user set form section values.
     * @return      array
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    

        $_aErrors   = array();
        
        if ( 'submit_clear_all_caches' === $aSubmitInfo[ 'field_id' ] ) {
            $_aErrors = $this->___clearAllCaches( $oAdminPage, $aInputs );

        }
        if ( 'submit_clear_expired_caches' === $aSubmitInfo[ 'field_id' ] ) {
            $_aErrors = $this->___clearExpiredCaches( $oAdminPage, $aInputs );
        }
                
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( count( $_aErrors ) ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }

        // Keep the cache data base table size
        AmazonAutoLinks_PluginUtility::truncateCacheTablesBySize(
            $oAdminPage->oUtil->getElement( $aInputs, array( 'table_size', 'products' ), '' ),
            $oAdminPage->oUtil->getElement( $aInputs, array( 'table_size', 'requests' ), '' )
        );

        // If the interval for deleting expired caches changes, update the scheduled task.
        $this->___rescheduleTaskOfDeletingExpiredCaches( $aInputs, $aOldInputs, $oAdminPage );
        
        return $aInputs;     
        
    }

        /**
         * Update the scheduled task if the interval for deleting expired caches changes, 
         * @since       3.4.0
         * @return      void
         */
        private function ___rescheduleTaskOfDeletingExpiredCaches( $aInputs, $aOldInputs, $oAdminPage ) {

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
         * @return      array   field error array
         */
        private function ___clearAllCaches( $oFactory, $aInputs ) {

            $this->___deleteUnitStatusOfAllUnits();

            // Clear transients.
            AmazonAutoLinks_WPUtility::cleanTransients( AmazonAutoLinks_Registry::TRANSIENT_PREFIX );
            AmazonAutoLinks_WPUtility::cleanTransients( 'apf_' );

            $_bProducts = $this->getElement( $aInputs, array( 'submit_clear_all_caches_types', 'products' ) );
            if ( $_bProducts ) {
                $_oProductTable    = new AmazonAutoLinks_DatabaseTable_aal_products;
                $_oProductTable->delete(); // delete all rows.
            }

            $_bHTTPRequests = $this->getElement( $aInputs, array( 'submit_clear_all_caches_types', 'http_requests' ) );
            if ( $_bHTTPRequests ) {
                $_oCacheTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
                $_oCacheTable->delete();  // delete all rows by passing nothing.
            }

            if ( $_bProducts || $_bHTTPRequests ) {
                $oFactory->setSettingNotice( __( 'Caches have been cleared.', 'amazon-auto-links' ), 'updated' );
                return array();
            }

            return array(
                $this->sSectionID => array(
                    'submit_clear_all_caches_types' => __( 'Select at least one.', 'amazon-auto-links' ),
                )
            );

        }
            /**
             * @since       3.7.7
             */
            private function ___deleteUnitStatusOfAllUnits() {

                    $_sPostType = AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ];
                    $_sPosts    = $GLOBALS[ 'wpdb' ]->posts;
                    $_sPostMeta = $GLOBALS[ 'wpdb' ]->postmeta;
                    $GLOBALS[ 'wpdb' ]->get_results(
                        "DELETE {$_sPostMeta} "
                        . "FROM {$_sPostMeta} "
                        . "INNER JOIN {$_sPosts} ON ( {$_sPosts}.ID = {$_sPostMeta}.post_id ) "
                        . "WHERE 1=1 "
                        . "AND ( 
                          {$_sPostMeta}.meta_key = '_error'
                        ) "
                        . "AND {$_sPosts}.post_type = '{$_sPostType}' "
                        . "AND ( "
                            . "({$_sPosts}.post_status <> 'trash' "
                            . "AND {$_sPosts}.post_status <> 'auto-draft')"
                        . ") "
                    );

            }
        /**
         * Clears expired caches.
         * @since       3
         * @return      array   A field error array.
         */
        private function ___clearExpiredCaches( $oFactory, $aInputs ) {

            $_bProducts = $this->getElement( $aInputs, array( 'submit_clear_expired_caches_types', 'products' ) );
            if ( $_bProducts ) {
                $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
                $_oProductTable->deleteExpired();
            }

            $_bHTTPRequests = $this->getElement( $aInputs, array( 'submit_clear_expired_caches_types', 'http_requests' ) );
            if ( $_bHTTPRequests ) {
                $_oCacheTable   = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
                $_oCacheTable->deleteExpired();
            }

            if ( $_bProducts || $_bHTTPRequests ) {
                $oFactory->setSettingNotice( __( 'Caches have been cleared.', 'amazon-auto-links' ), 'updated' );
                return array();
            }

            return array(
                $this->sSectionID => array(
                    'submit_clear_expired_caches_types' => __( 'Select at least one.', 'amazon-auto-links' ),
                )
            );
            
        }   
        
}