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
 * Adds a form section to an in-page tab.
 * 
 * @since       3.3.0
 * @action      schedule        aal_action_event_convert_unit_options
 */
class AmazonAutoLinks_UnitOptionConverter_Setting_Tab_Convert extends AmazonAutoLinks_AdminPage_Section_Base {

    protected function _getArguments() {
        return array(
            'section_id'    => '_convert',
            'tab_slug'      => $this->sTabSlug,
            'title'         => __( 'Unit Options', 'amazon-auto-links' ),
            'description'   => array(
                __( 'Convert unit options with batch processing.', 'amazon-auto-links' ),
            ),
            'save'          => false,
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function _construct( $oFactory ) {
        
        add_filter( 
            "validation_" .  AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ] . '_' . 'unit_option_converter', 
            array( $this, 'replyToValidateInputs' ),
            10, 
            4 
        );
        
    }
    
    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        
        $_aTasks  = AmazonAutoLinks_WPUtility::getScheduledCronTasksByActionName(
            'aal_action_event_convert_unit_options'
        );

        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'              => '_show_unit_counts',
                'type'                  => '_show_unit_counts',
                'title'                 => __( 'Number of Units', 'amazon-auto-links' ),
                'save'                  => false,
                'after_fields'          => '<p>' 
                        . $this->_getUnitCount() 
                    . "</p>",
            ),
            array(
                'field_id'              => '_scheduled_tasks',
                'type'                  => '_scheduled_tasks',
                'save'                  => false,
                'title'                 => __( 'Remaining Scheduled Tasks', 'amazon-auto-links' ),
                'after_fields'          => '<p>' 
                        . count( $_aTasks )
                    . "</p>",
            ),            
            // array(
                // 'field_id'              => '_separator',
                // 'type'                  => '_separator',
                // 'show_title_column'     => false,
                // 'after_fields'          => '<hr />',
            // ),                        
            array(
                'field_id'              => 'unit_types',
                'type'                  => 'checkbox',
                'title'                 => __( 'Unit Types', 'amazon-auto-links' ),
                'label'                 => AmazonAutoLinks_PluginUtility::getUnitTypeLabels(),
                'select_all_button'     => true,
                'select_none_button'    => true,                  
                'default'               =>  array_fill_keys(
                    array_keys( AmazonAutoLinks_PluginUtility::getUnitTypeLabels() ),  // keys
                    true    // the value to fill
                ),
            ),                    
            array()
        );    

        
        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'              => '_submit_convert',
                'type'                  => 'submit',
                // 'show_title_column'     => false,
                'value'                 => __( 'Convert', 'amazon-auto-links' ),
                'label_min_width'       => '',
                'attributes'            => array(
                    // 'field' => array(
                        // 'style'     => 'float:right;',

                    // ),
                    'disabled'  => $_oOption->isAdvancedAllowed()
                        ? null
                        : 'disabled',                    
                    'title'     => $_oOption->isAdvancedAllowed()
                        ? __( 'Convert Options', 'amazon-auto-links' )
                        : __( 'Get Pro!', 'amazon-auto-links' ),
                ),
                'after_fieldset'        => $_oOption->isAdvancedAllowed()
                    ? ''
                    : "<p>"
                        . sprintf(
                            __( 'In order to use this feature, please consider purchasing <a href="%1$s" target="_blank">Pro</a>.', 'amazon-auto-links' ),
                            'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/'
                        )
                    . "</p>",
            )
        );
        
    }
        /**
         * @return      integer
         */
        private function _getUnitCount() {
                    
            $_aArguments = array(
                'post_type'      => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'post_staus'     => 'publish',
                'posts_per_page' => -1
            );
            $_oQuery = new WP_Query( $_aArguments );
            return $_oQuery->found_posts;
            
        }
        

    
    /**
     * Validates the submitted form data.
     * 
     * @since       3.3.0
     * @callback    filter      validation_{page slug}_{tab slug}
     */
    public function replyToValidateInputs( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {

        $_bVerified = true;
        $_aErrors   = array();

        $_aCheckedUnitTypes = array_filter( $aInputs[ $this->sSectionID ][ 'unit_types' ] );
        if ( empty( $_aCheckedUnitTypes ) ) {
            $_bVerified = false;
            $_aErrors[ $this->sSectionID ][ 'unit_types' ] = __( 'At least one item needs to be checked.', 'amazon-auto-links' );
        }
        
        $_oOption = AmazonAutoLinks_Option::getInstance();  
        if ( ! $_oOption->isAdvancedAllowed() ) {
            $oAdminPage->setSettingNotice( AmazonAutoLinks_PluginUtility::getUpgradePromptMessage() );
            return $aOldInputs;
        }
        
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }
        
        $_aUnitTypes = array_keys( 
            array_filter( $aInputs[ $this->sSectionID ][ 'unit_types' ] ) // drop non-true items
        );
        $aInputs      = $this->_getSanitizedInputs( $aInputs, $aOldInputs );
       
        $_iScheduled = $this->_scheduleTasks( $_aUnitTypes, $aInputs );
        
        $oAdminPage->setSettingNotice( 
            sprintf( 
                __( 'Scheduled %1$s unit(s) for the batch processing.', 'amazon-auto-links' ),
                $_iScheduled
            ),
            $_iScheduled
                ? 'updated'
                : 'error'
        );       
        return $aInputs;     
        
    }
    
        /**
         * @return      array
         */
        private function _getSanitizedInputs( $aInputs, $aOldInputs ) {
            
            $_oItemFormatValidator = new AmazonAutoLinks_FormValidator_ItemFormat( $aInputs, $aOldInputs );
            $aInputs = $_oItemFormatValidator->get();        
                                
            // Drop unnecessary items.
            unset( $aInputs[ '_convert' ] );
                          
            return $aInputs;
            
        }
        
        /**
         * 
         * @return      integer
         */
        private function _scheduleTasks( array $aUnitTypes, array $aInputs ) {
            
            $_iScheduledTasks = 0;
            $_aUnitIDs = $this->_getUnitIDsByUnitTypes(
                $aUnitTypes
            );
            foreach( $_aUnitIDs as $_iUnitID ) {
                $_bScheduled = $this->_scheduleEachTask( $_iUnitID, $aInputs );
                if ( $_bScheduled ) {
                    $_iScheduledTasks++;
                }
            }
            return $_iScheduledTasks;
        }
            /**
             * 
             * @action      schedule        aal_action_event_convert_unit_options
             * @return      boolean
             */
            private function _scheduleEachTask( $iUnitID, $aInputs ) {

                static $_iSecondOffset = 0;
                
                $_sActionName = 'aal_action_event_convert_unit_options';
                $_aArguments  = array(
                    $iUnitID,
                    $aInputs,
                );                
                if ( wp_next_scheduled( $_sActionName, $_aArguments ) ) {
                    return false; 
                }
                // Returns null or false.
                $_bCancelled = wp_schedule_single_event( 
                    time() + $_iSecondOffset, // now + offset
                    $_sActionName, // the AmazonAutoLinks_Event class will check this action hook and executes it with WP Cron.
                    $_aArguments // must be enclosed in an array.
                );          
                if ( false !== $_bCancelled ) {
                    $_iSecondOffset++;
                }
                return false === $_bCancelled
                    ? false
                    : true; // null is set when it is not cancelled
                    
            }
            
            /**
             * 
             * @return      array       an array holding found post ids.
             */
            private function _getUnitIDsByUnitTypes( array $aUnitTypes ) {
                
                $_aMetaQuery = array( 
                    'relation' => 'OR' 
                );
                foreach( $aUnitTypes as $_sUnitTYpe ) {
                    $_aMetaQuery[] = array(
                        'key'       => 'unit_type',
                        'value'     => $_sUnitTYpe,
                        'compare'   => '=',
                    );
                }
                $_aArguments = array(
                    'post_type'         => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    'fields'            => 'ids', // return only post ids
                    'post_staus'        => 'publish',
                    'posts_per_page'    => -1,
                    'meta_query'        => $_aMetaQuery,
                );
                $_oQuery = new WP_Query( $_aArguments );
                return $_oQuery->posts;
                
            }
            
}