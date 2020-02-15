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
 * @since       3
 * @action      schedule        aal_action_event_convert_template_options
 */
class AmazonAutoLinks_ToolAdminPage_Tool_TemplateOptionConverter_Format extends AmazonAutoLinks_AdminPage_Section_Base {
    
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
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        
        $_aTasks  = AmazonAutoLinks_WPUtility::getScheduledCronTasksByActionName(
            'aal_action_event_convert_template_options'
        );

        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'              => '_show_unit_counts',
                'type'                  => '_show_unit_counts',
                'title'                 => __( 'Number of Units', 'amazon-auto-links' ),
                'after_fields'          => '<p>' 
                        . $this->_getUnitCount() 
                    . "</p>",
            ),
            array(
                'field_id'              => '_scheduled_tasks',
                'type'                  => '_scheduled_tasks',
                'title'                 => __( 'Remaining Scheduled Tasks', 'amazon-auto-links' ),
                'after_fields'          => '<p>' 
                        . count( $_aTasks )
                    . "</p>",
            ),            
            array(
                'field_id'              => '_separator',
                'type'                  => '_separator',
                'show_title_column'     => false,
                'after_fields'          => '<hr />',
            ),                        
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
        // @deprecated 4.0.0 To support default item format options for each template
        // $_oFields = new AmazonAutoLinks_FormFields_Unit_Template;
        $_oFields = new AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport; // 4.0.0+
        $_aFields = $_oFields->get();
        foreach( $_aFields as $_aField ) {
            $oFactory->addSettingFields(
                $sSectionID, // the target section id            
                $_aField
            );
        }
        
        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'              => '_submit_convert',
                'type'                  => 'submit',
                // 'show_title_column'     => false,
                'value'                 => __( 'Convert', 'amazon-auto-links' ),
                'label_min_width'       => '',
                'attributes'            => array(
                    'field' => array(
                        'style'     => 'float:right;',

                    ),
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
            ),
            array()
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
     * @since       3
     */
    public function validate( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();

        $_aCheckedUnitTypes = array_filter( $aInput[ 'unit_types' ] );
        if ( empty( $_aCheckedUnitTypes ) ) {
            $_bVerified = false;
            $_aErrors[ $this->sSectionID ][ 'unit_types' ] = __( 'At least one item needs to be checked.', 'amazon-auto-links' );
        }
        
        $_oOption = AmazonAutoLinks_Option::getInstance();  
        if ( ! $_oOption->isAdvancedAllowed() ) {
            $oAdminPage->setSettingNotice( AmazonAutoLinks_PluginUtility::getUpgradePromptMessage() );
            return $aOldInput;
        }
        
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInput;
        }
        
        $_aUnitTypes = array_keys( 
            array_filter( $aInput[ 'unit_types' ] ) // drop non-true items
        );
        $aInput      = $this->_getSanitizedInputs( $aInput );
       
        $_iScheduled = $this->_scheduleTasks( $_aUnitTypes, $aInput );
        
        $oAdminPage->setSettingNotice( 
            sprintf( 
                __( 'Scheduled %1$s unit(s) for the batch processing.', 'amazon-auto-links' ),
                $_iScheduled
            ),
            $_iScheduled
                ? 'updated'
                : 'error'
        );       
        return $aInput;     
        
    }
    
        /**
         * @return      array
         */
        private function _getSanitizedInputs( $aInput ) {
            
            $_oItemFormatValidator = new AmazonAutoLinks_FormValidator_ItemFormat( $aInputs, $aOldInputs );
            $aInputs = $_oItemFormatValidator->get();        
                                
            // Drop unnecessary items.
            unset(
                $aInput[ '_submit_convert' ],
                $aInput[ '_show_unit_counts' ],
                $aInput[ '_scheduled_tasks' ],
                $aInput[ '_separator' ],
                $aInput[ 'unit_types' ]
            );
                          
            return $aInput;
            
        }
        
        /**
         * 
         * @return      integer
         */
        private function _scheduleTasks( array $aUnitTypes, array $aInput ) {
            
            $_iScheduledTasks = 0;
            $_aUnitIDs = $this->_getUnitIDsByUnitTypes(
                $aUnitTypes
            );
            foreach( $_aUnitIDs as $_iUnitID ) {
                $_bScheduled = $this->_scheduleEachTask( $_iUnitID, $aInput );
                if ( $_bScheduled ) {
                    $_iScheduledTasks++;
                }
            }
            return $_iScheduledTasks;
        }
            /**
             * 
             * @action      schedule        aal_action_event_convert_template_options
             * @return      boolean
             */
            private function _scheduleEachTask( $iUnitID, $aInput ) {

                static $_iSecondOffset = 0;
                
                $_sActionName = 'aal_action_event_convert_template_options';
                $_aArguments  = array(
                    $iUnitID,
                    $aInput,
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