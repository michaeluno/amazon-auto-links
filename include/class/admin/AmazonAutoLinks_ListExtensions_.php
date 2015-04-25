<?php
/**
 * Lists plugin extensions.
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2015, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
*/
abstract class AmazonAutoLinks_ListExtensions_ {

    // Container arrays
    protected $arrFeedItems = array();    // stores fetched feed items.
    
    // Objects
    protected $oFeed;    // stores the feed object. 
    
    // Properties
    protected $strTransientPrefix = 'AAL_';
    
    
    public function fetchFeed( $vURLs, $numItems=0, $fCacheRenew=false ) {
        
        $arrURLs  = is_array( $vURLs ) 
            ? $vURLs 
            : ( array ) $vURLs;
        $strURLID = md5( serialize( $arrURLs ) );
        
        if ( ! isset( $this->arrFeedItems[ $strURLID ] ) && $fCacheRenew == false ) {
            $this->arrFeedItems[ $strURLID ] = ( array ) AmazonAutoLinks_WPUtilities::getTransient( $this->strTransientPrefix . $strURLID );
            unset( $this->arrFeedItems[ $strURLID ][0] );    // casting array causes the 0 key,
        }
            
        // If it's out of stock, fill the array by fetching the feed.
        if ( empty( $this->arrFeedItems[ $strURLID ] ) ) {    
                        
            // When an array of urls is passed to the Simple Pie's set_feed_url() method, the memory usage increases largely.
            // So fetch the feeds one by one per url and store the output into an array.
            foreach( $arrURLs as $strURL ) {
                                
                $oFeed = $this->getFeedObj( $strURL, null, $fCacheRenew ? 0 : 3600 );
                
                // foreach ( $oFeed->get_items( 0, $numItems * 3 ) as $item ) does not change the memory usage
                foreach ( $oFeed->get_items() as $oItem ) {
                    $this->arrFeedItems[ $strURLID ][ $oItem->get_title() ] = array( 
                        'strContent'        => $oItem->get_content(),
                        'strDescription'    => $oItem->get_description(),
                        'strTitle'          => $oItem->get_title(),
                        'strDate'           => $oItem->get_title(),
                        'strAuthor'         => $oItem->get_date( 'j F Y, g:i a' ),
                        'strLink'           => $oItem->get_permalink(),    // get_link() may be used as well        
                    );
                }
                
                // For PHP below 5.3 to release the memory.
                $oFeed->__destruct(); // Do what PHP should be doing on it's own.
                unset( $oFeed ); 
                
            }
        
            // This life span should be little longer than the feed cache life span, which is 1700.
            AmazonAutoLinks_WPUtilities::setTransient( $this->strTransientPrefix . $strURLID, $this->arrFeedItems[ $strURLID ], 1800 );    // 30 minutes    
            
        }
        
        $arrOut = $this->arrFeedItems[ $strURLID ];
        if ( $numItems  ) {
            array_splice( $arrOut, $$numItems );
        }
            
        return $arrOut;
        
    }
    
    protected function getFeedObj( $arrUrls, $numItem=0, $numCacheDuration=3600 ) {    // 60 seconds * 60 = 1 hour, 1800 = 30 minutes
        
        // Reuse the object that already exists. This conserves the memory usage.
        $this->oFeed = isset( $this->oFeed ) 
            ? $this->oFeed 
            : new AmazonAutoLinks_SimplePie();
        $oFeed = $this->oFeed; 
        
        // Set sort type.
        $oFeed->set_sortorder( 'date' );

        // Set urls
        $oFeed->set_feed_url( $arrUrls );    
        if ( $numItem ) {
            $oFeed->set_item_limit( $numItem );    
        }
        
        // This should be set after defining $urls
        $oFeed->set_cache_duration( $numCacheDuration );    
        
        $oFeed->set_stupidly_fast( true );
        
        // If the cache lifetime is explicitly set to 0, do not trigger the background renewal cache event
        if ( 0 == $numCacheDuration ) {
            $oFeed->setBackground( true );    // setting it true will be considered the background process; thus, it won't trigger the renewal event.
        }
        
        // set_stupidly_fast() disables this internally so turn it on manually because it will trigger the custom sort method
        $oFeed->enable_order_by_date( true );    
        $oFeed->init();            
        return $oFeed;
        
    }    
    
    
    public function printColumnOutput( $arrItems ) {
        echo $this->getColumnOutput( $arrItems );        
    }
    
    
    protected $arrColumnOption = array (
        'strClassAttr'          => 'amazon_auto_links_multiple_columns',
        'strClassAttrGroup'     => 'amazon_auto_links_multiple_columns_box',
        'strClassAttrRow'       => 'amazon_auto_links_multiple_columns_row',
        'strClassAttrCol'       => 'amazon_auto_links_multiple_columns_col',
        'strClassAttrFirstCol'  => 'amazon_auto_links_multiple_columns_first_col',
    );    
    /**
     * 
     * @remark      this will be modified as the items get rendered
     */
    protected $arrColumnInfoDefault = array (   
        'fIsRowTagClosed'       => false,
        'numCurrRowPos'         => 0,
        'numCurrColPos'         => 0,
    );    
    public function getColumnOutput( $arrItems, $intMaxCols=4 ) {
        
        $arrOutput = array();
        $this->arrColumnInfo = $this->arrColumnInfoDefault;    // initialize
        foreach( $arrItems as $strTitle => $arrItem ) {
            
            // Increment the position
            $this->arrColumnInfo['numCurrColPos']++;
            
            // Enclose the item buffer into the item container
            $strItem = '<div class="' . $this->arrColumnOption['strClassAttrCol'] 
                . ' amazon_auto_links_col_element_of_' . $intMaxCols . ' '
                . ' amazon_auto_links_extension '
                . ( ( $this->arrColumnInfo['numCurrColPos'] == 1 ) ?  $this->arrColumnOption['strClassAttrFirstCol']  : '' )
                . '"'
                . '>' 
                . '<div class="amazon_auto_links_extension_item">' 
                    . "<h4>{$arrItem['strTitle']}</h4>"
                    . $arrItem['strDescription'] 
                    . "<div class='get-now'>"
                        . "<a href='{$arrItem['strLink']}' target='_blank' rel='nofollow'>" 
                            . "<input class='button button-secondary' type='submit' value='" . esc_attr( __( 'Get it Now', 'amazon-auto-links' ) ) . "' />"
                        . "</a>"
                   . "</div>"
                . '</div>'
                . '</div>';    
                
            // If it's the first item in the row, add the class attribute. 
            // Be aware that at this point, the tag will be unclosed. Therefore, it must be closed somewhere. 
            if ( $this->arrColumnInfo['numCurrColPos'] == 1 ) 
                $strItem = '<div class="' . $this->arrColumnOption['strClassAttrRow']  . '">' . $strItem;
        
            // If the current column position reached the set max column, increment the current position of row
            if ( $this->arrColumnInfo['numCurrColPos'] % $intMaxCols == 0 ) {
                $this->arrColumnInfo['numCurrRowPos']++;        // increment the row number
                $this->arrColumnInfo['numCurrColPos'] = 0;        // reset the current column position
                $strItem .= '</div>';  // close the section(row) div tag
                $this->arrColumnInfo['fIsRowTagClosed'] =     True;
            }        
            
            $arrOutput[] = $strItem;
        
        }
        
        // if the section(row) tag is not closed, close it
        if ( ! $this->arrColumnInfo['fIsRowTagClosed'] ) $arrOutput[] .= '</div>';    
        $this->arrColumnInfo['fIsRowTagClosed'] = true;
        
        // enclose the output in the group tag
        $strOut = '<div class="' . $this->arrColumnOption['strClassAttr'] . ' '
                .  $this->arrColumnOption['strClassAttrGroup'] . ' '
                . '"'
                // . ' style="min-width:' . 200 * $intMaxCols . 'px;"'
                . '>'
                . implode( '', $arrOutput )
                . '</div>';
        
        return '<div class="amazon_auto_links_extension_container">' . $strOut . '</div>';        
        
    }
}