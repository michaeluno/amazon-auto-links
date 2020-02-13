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
 * Lists responsive columns.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2020, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 * @since       3       Chnaged the name from `AmazonAutoLinks_ListExtensions`
*/
class AmazonAutoLinks_Column extends AmazonAutoLinks_WPUtility {
   
    /**
     * Stores fetched feed items.
     * 
     */
    public $aItems = array();    
    
    /**
     * The number of columns.
     */
    public $iMaxCols = 4;
    
    public $sSelectorPrefix = 'amazon_auto_links_';
    
    /**
     * Stores column options.
     */
    protected $aColumnOptions = array (
        'class_selector'                => 'columns',
        'class_selector_group'          => 'columns_box',
        'class_selector_row'            => 'columns_row',
        'class_selector_column'         => 'columns_col',
        'class_selector_first_column'   => 'columns_first_col',
    );    
    
    /**
     * 
     * @remark      this will be modified as the items get rendered
     */
    protected $_aColumnStateDefault = array(
        'is_row_tag_closed'      => false,
        'number_current_row_pos' => 0,
        'number_current_col_pos' => 0,
    );        
            
    /**
     * Sets up properties.
     */
    public function __construct( $aItems, $iMaxCols=4, $sClassSelectorPrefix='' ) {
        
        $this->aItems          = $this->getAsArray( $aItems );
        $this->iMaxCols        = $iMaxCols;
        $this->sSelectorPrefix = $sClassSelectorPrefix
            ? $sClassSelectorPrefix
            : $this->sSelectorPrefix;
        
        // Format
        foreach( $this->aColumnOptions as &$_sOption ) {
            $_sOption = $this->sSelectorPrefix . $_sOption;
        }
        
    }
    
    /**
     * 
     * @return      string
     */
    public function getCSS() {
        
return <<<CSS

/* Extension Listing Table */
.{$this->sSelectorPrefix}columns_container{
    padding-right: 30px;
    padding-left: 10px;
    margin-top: 10px;
    text-align: center;
}

.{$this->sSelectorPrefix}columns {
    padding: 4px;
    line-height: 1.5em;
}
.{$this->sSelectorPrefix}columns_first_col {
    margin-left: 0px;
    clear: left;
}
/*  SECTIONS  ============================================================================= */
.{$this->sSelectorPrefix}columns_row {
    clear: both;
    padding: 0px;
    margin: 0px;
}
/*  GROUPING  ============================================================================= */
.{$this->sSelectorPrefix}columns_box:before,
.{$this->sSelectorPrefix}columns_box:after {
    content:"";
    display:table;
}
.{$this->sSelectorPrefix}columns_box:after {
    clear:both;
}
.{$this->sSelectorPrefix}columns_box {
    float: none;
    width: 100%;        
    zoom:1; /* For IE 6/7 (trigger hasLayout) */
}
/*  GRID COLUMN SETUP   ==================================================================== */
.{$this->sSelectorPrefix}columns_col {
    display: block;
    float:left;
    margin: 1% 0 1% 1.6%;
}
.{$this->sSelectorPrefix}columns_col:first-child { margin-left: 0; } /* all browsers except IE6 and lower */
/*  REMOVE MARGINS AS ALL GO FULL WIDTH AT 800 PIXELS */
@media only screen and (max-width: 800px) {
    .{$this->sSelectorPrefix}columns_col { 
        margin: 1% 0 1% 0%;
    }
}
/*  GRID OF TWO   ============================================================================= */
.{$this->sSelectorPrefix}col_element_of_1 {
    width: 100%;
}
.{$this->sSelectorPrefix}col_element_of_2 {
    width: 49.2%;
}
.{$this->sSelectorPrefix}col_element_of_3 {
    width: 32.2%; 
}
.{$this->sSelectorPrefix}col_element_of_4 {
    width: 23.8%;
}
.{$this->sSelectorPrefix}col_element_of_5 {
    width: 18.72%;
}
.{$this->sSelectorPrefix}col_element_of_6 {
    width: 15.33%;
}
.{$this->sSelectorPrefix}col_element_of_7 {
    width: 12.91%;
}
.{$this->sSelectorPrefix}col_element_of_8 {
    width: 11.1%; 
}
.{$this->sSelectorPrefix}col_element_of_9 {
    width: 9.68%; 
}
.{$this->sSelectorPrefix}col_element_of_10 {
    width: 8.56%; 
}
.{$this->sSelectorPrefix}col_element_of_11 {
    width: 7.63%; 
}
.{$this->sSelectorPrefix}col_element_of_12 {
    width: 6.86%;
}

/*  GO FULL WIDTH AT LESS THAN 800 PIXELS */
@media only screen and (max-width: 800px) {
    .{$this->sSelectorPrefix}col_element_of_2,
    .{$this->sSelectorPrefix}col_element_of_3,
    .{$this->sSelectorPrefix}col_element_of_4,
    .{$this->sSelectorPrefix}col_element_of_5,
    .{$this->sSelectorPrefix}col_element_of_6,
    .{$this->sSelectorPrefix}col_element_of_7,
    .{$this->sSelectorPrefix}col_element_of_8,
    .{$this->sSelectorPrefix}col_element_of_9,
    .{$this->sSelectorPrefix}col_element_of_10,
    .{$this->sSelectorPrefix}col_element_of_11,
    .{$this->sSelectorPrefix}col_element_of_12
    {    width: 49.2%;  }            
}
CSS;
            
    }
    
    /**
     * Generates the column output.
     * 
     * @return      string
     */
    public function get() {
        return $this->_getColumnOutput(
            $this->aItems,
            $this->iMaxCols
        );
    }
        /**
         * @return      string
         */
        protected function _getColumnOutput( $aItems, $iMaxCols ) {
            
            // Initialize
            $_aColumnState = $this->_aColumnStateDefault;    
            
            $_aOutput = array();
            foreach( $aItems as $_aItem ) {
                
                // Increment the position
                $_aColumnState[ 'number_current_col_pos' ]++;
                
                // Enclose the item buffer into the item container
                $_aAttributes_Div = array(
                    'class' => $this->aColumnOptions[ 'class_selector_column' ] 
                        . ' ' . $this->sSelectorPrefix . 'col_element_of_' . $iMaxCols
                        . ( 
                            1 === $_aColumnState[ 'number_current_col_pos' ]
                                ? ' ' .  $this->aColumnOptions[ 'class_selector_first_column' ] 
                                : ''
                        ),
                );
                $_sItem = '<div ' . $this->generateAttributes( $_aAttributes_Div ) . '>'
                        . '<div class="' . $this->sSelectorPrefix . 'item">' 
                            . $_aItem[ 'description' ]
                        . '</div>'
                    . '</div>';    
                    
                // If it's the first item in the row, add the class attribute. 
                // Be aware that at this point, the tag will be unclosed. Therefore, it must be closed somewhere. 
                if ( 1 === $_aColumnState[ 'number_current_col_pos' ] ) {
                    $_sItem = '<div class="' . $this->aColumnOptions[ 'class_selector_row' ]  . '">' 
                        . $_sItem;
                }
            
                // If the current column position reached the set max column, increment the current position of row
                if ( $_aColumnState[ 'number_current_col_pos' ] % $iMaxCols == 0 ) {
                    $_aColumnState[ 'number_current_row_pos' ]++;        // increment the row number
                    $_aColumnState[ 'number_current_col_pos' ] = 0;        // reset the current column position
                    $_sItem .= '</div>';  // close the section(row) div tag
                    $_aColumnState['is_row_tag_closed'] = true;
                }        
                
                $_aOutput[] = $_sItem;
            
            }
            
            // if the section(row) tag is not closed, close it
            if ( ! $_aColumnState[ 'is_row_tag_closed' ] ) {
                $_aOutput[] .= '</div>';    
            }
            $_aColumnState[ 'is_row_tag_closed' ] = true;
            
            $_aAttributes_Container = array(
                'class' => $this->sSelectorPrefix . 'columns_container',
            );
            $_aAttributes_Group = array(
                'class' => $this->aColumnOptions[ 'class_selector' ] . ' '
                    . $this->aColumnOptions[ 'class_selector_group' ],
            );                            
            return '<div ' . $this->generateAttributes( $_aAttributes_Container ) . '>' 
                    . '<div ' . $this->generateAttributes( $_aAttributes_Group ) . '>'
                        . implode( '', $_aOutput )
                    . '</div>'
                . '</div>';        
            
        }
        
}