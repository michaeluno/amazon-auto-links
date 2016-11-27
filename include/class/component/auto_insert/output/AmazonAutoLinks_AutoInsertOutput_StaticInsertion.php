<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Inserts product links into the pre-defined area of page contents for the back-end.
 * 
 * @package     Amazon Auto Links
 * @since       3.4.10
*/
class AmazonAutoLinks_AutoInsertOutput_StaticInsertion extends AmazonAutoLinks_AutoInsertOutput_Base {
    
    /**
     * @since       3.4.10
     * @return      boolean
     */
    protected function _shouldProceed() {
        if ( ! is_admin() ) {
            return false;
        }
        return parent::_shouldProceed();
    }
    
    /**
     * @return      string
     */
    protected function _getFiltersApplied( $sFilterName, $aArguments ) {

        $_aPostContent = $this->getElement( $aArguments, 0 );   // __call argument
        if ( 'wp_insert_post_data' !== $sFilterName ) {
            return $_aPostContent;
        }
        return $this->___getPostContentWithUnitOutput( 
            $_aPostContent, 
            $this->getElementAsArray( $aArguments, 1 ) 
        );
        
    }
   
        /**
         * Handles static insertion for posts.
         * 
         * @remark            Only category taxonomy allow/deny check is supported. Other types post_tags and custom taxonomies are not supported yet.
         */
        private function ___getPostContentWithUnitOutput( array $aPostContent, $aPostMeta=array() ) {

            // if the publish key exists, it means it is an update
            if ( 'Update' === $this->getElement( $aPostMeta, 'save' ) ) {
                return $aPostContent;
            }
            
            // If it's auto-draft saving feature, do nothing.
            if ( 'publish' !== $this->getElement( $aPostContent, 'post_status' ) ) {
                return $aPostContent;
            }
        
            // The default disabled post types.
            if ( in_array( $aPostContent[ 'post_type' ], array( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ], AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ], 'revision', 'attachment', 'nav_menu_item' ) )  ) {
                return $aPostContent;
            }

            /*    $aPostMeta structure
                [ID] => 278
                [post_category] => Array (
                    [0] => 0
                    [1] => 10
                    [2] => 9
                    [3] => 1
                )
                [tax_input] => Array(
                    [post_tag] => test
                )
            */  

            $aSubjectPostInfo = array(
                'post_id'   => $aPostMeta[ 'ID' ],
                'post_type' => $aPostContent[ 'post_type' ],
                'term_ids'  => $aPostMeta[ 'post_category' ],
            );

            $sPre  = '';
            $sPost = '';
            foreach( $this->aFilterHooks[ 'wp_insert_post_data' ] as $iAutoInsertID ) {
                
                if ( ! $this->_isAutoInsertEnabledPage( $iAutoInsertID, $aSubjectPostInfo ) ) {
                    continue;
                }

                $aAutoInsertOptions = $this->aAutoInsertOptions[ $iAutoInsertID ];        
                
                // position - above, below, or both,
                $sPosition   = $aAutoInsertOptions[ 'static_position' ];
                $_aArguments = array( 
                    'id' => $aAutoInsertOptions[ 'unit_ids' ]
                );                       
                if ( 'above' === $sPosition || 'both' === $sPosition ) {
                    $sPre  .= AmazonAutoLinks( $_aArguments, false );
                }
                if ( 'below' === $sPosition || 'both' === $sPosition ) {
                    $sPost .= AmazonAutoLinks( $_aArguments, false );
                }
            
            }
            
            $aPostContent[ 'post_content' ] = $sPre . $aPostContent[ 'post_content' ] . $sPost;
                
            return $aPostContent;
            
        }
   
}
