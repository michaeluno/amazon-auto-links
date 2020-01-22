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

        $_mFilteringValue = $this->getElement( $aArguments, 0 );   // __call argument
        if ( 'wp_insert_post_data' !== $sFilterName ) {
            return $_mFilteringValue;
        }
        return $this->___getPostContentWithUnitOutput(
            $_mFilteringValue,  // ( array ) post content
            $this->getElementAsArray( $aArguments, 1 ) 
        );
        
    }
   
        /**
         * Handles static insertion for posts.
         * 
         * @remark      Only category taxonomy allow/deny check is supported. Other types post_tags and custom taxonomies are not supported yet.
         * @return      array
         */
        private function ___getPostContentWithUnitOutput( array $aPostContent, $aPostMeta=array() ) {

            if ( ! $this->___shouldStaticAutoInsert( $aPostContent, $aPostMeta ) ) {
                return $aPostContent;
            }

            /**
             * $aPostMeta structure
             *  [ID] => 278
             *  [post_category] => Array (
             *      [0] => 0
             *      [1] => 10
             *      [2] => 9
             *      [3] => 1
             *  )
             *  [tax_input] => Array(
             *      [post_tag] => test
             *  )
             */

            $aSubjectPostInfo = array(
                'post_id'   => $aPostMeta[ 'ID' ],
                'post_type' => $aPostContent[ 'post_type' ],
                'term_ids'  => $aPostMeta[ 'post_category' ],
            );

            $sPre  = '';
            $sPost = '';
            foreach( $this->aFilterHooks[ 'wp_insert_post_data' ] as $_iAutoInsertID ) {
                
                if ( ! $this->_isAutoInsertEnabledPage( $_iAutoInsertID, $aSubjectPostInfo ) ) {
                    continue;
                }

                $_aAutoInsertOptions = $this->aAutoInsertOptions[ $_iAutoInsertID ];        
                
                // position - above, below, or both,
                $_sPosition  = $_aAutoInsertOptions[ 'static_position' ];
                $_aArguments = array( 
                    'id' => $_aAutoInsertOptions[ 'unit_ids' ]
                );                       
                if ( 'above' === $_sPosition || 'both' === $_sPosition ) {
                    $sPre  .= AmazonAutoLinks( $_aArguments, false );
                }
                if ( 'below' === $_sPosition || 'both' === $_sPosition ) {
                    $sPost .= AmazonAutoLinks( $_aArguments, false );
                }
            
            }
            
            $aPostContent[ 'post_content' ] = $sPre . $aPostContent[ 'post_content' ] . $sPost;
                
            return $aPostContent;
            
        }

            /**
             * @since       3.4.11
             * @param       array   $aPostContent
             * @param       array   $aPostMeta
             * @return      boolean
             */
            private function ___shouldStaticAutoInsert( $aPostContent, $aPostMeta ) {

                // If it's auto-draft saving feature, do nothing.
                if ( $this->getElement( $aPostMeta, 'auto_draft' ) ) {
                    return false;
                }

                // If it is not an allowed post status, do not proceed.
                $_sNewPostStatus       = $this->getElement( $aPostContent, 'post_status' );
                $_aAllowedPostStatuses = array( 'publish', 'draft', 'pending' );
                if ( ! in_array( $_sNewPostStatus, $_aAllowedPostStatuses ) ) {
                    return false;
                }

                // The default disabled post types.
                $_aDisallowedPostTypes = array(
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ],
                    'revision',
                    'attachment',
                    'nav_menu_item'
                );
                if ( in_array( $aPostContent[ 'post_type' ], $_aDisallowedPostTypes ) ) {
                    return false;
                }

                // Check whether this is a first time of saving the post.
                // If it is an update, do not perform auto-insert.
                if(
                    $this->getElement( $aPostContent, 'post_date' )
                    !== $this->getElement( $aPostContent, 'post_modified' )
                ) {
                    return false;
                }

                return true;
            }
   
}
