<?php
/**
 * Deals with the plugin admin pages.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.5
 * 
 */
abstract class AmazonAutoLinks_AdminPage_Extension extends AmazonAutoLinks_AdminPage_Help {

    /*
     * Extension page
     */ 
    public function do_before_extensions() {    // do_before_ + page slug
        $this->showPageTitle( false );
    }
    public function do_extensions_get_extensions() {
                
        $oExtensionLoader = new AmazonAutoLinks_Extensions();
        $arrFeedItems = $oExtensionLoader->fetchFeed( 'http://feeds.feedburner.com/MiunosoftAmazonAutoLinksExtension' );
        if ( empty( $arrFeedItems ) ) {
            echo "<h3>" . __( 'No extension has been found.', 'amazon-auto-links' ) . "</h3>";
            return;
        }
        
        $arrOutput = array();
        $intMaxCols = 4;
        $this->arrColumnInfo = $this->arrColumnInfoDefault;
        foreach( $arrFeedItems as $strTitle => $arrItem ) {
            
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
                    . "<div class='get-now'><a href='{$arrItem['strLink']}' target='_blank' rel='nofollow'>" 
                        . "<input class='button button-secondary' type='submit' value='" . __( 'Get it Now', 'amazon-auto-links' ) . "' />"
                    . "</a></div>"
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
        
        echo '<div class="amazon_auto_links_extension_container">' . $strOut . '</div>';
        
    }
    
        
}