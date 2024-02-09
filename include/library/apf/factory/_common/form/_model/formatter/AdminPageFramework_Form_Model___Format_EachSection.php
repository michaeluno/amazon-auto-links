<?php
/*
 * Admin Page Framework v3.9.2b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2023, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_Model___Format_EachSection extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public static $aStructure = array( '_count_subsections' => 0, '_is_first_index' => false, '_is_last_index' => false, '_index' => null, '_is_collapsible' => false, '_tag_id' => '', '_tag_id_model' => '', '_sections_id' => '', );
    public $aSection = array();
    public $iIndex = null;
    public $aSubSections = array();
    public $sSectionsID = '';
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aSection, $this->iIndex, $this->aSubSections, $this->sSectionsID, );
        $this->aSection = $_aParameters[ 0 ];
        $this->iIndex = $_aParameters[ 1 ];
        $this->aSubSections = $_aParameters[ 2 ];
        $this->sSectionsID = $_aParameters[ 3 ];
    }
    public function get()
    {
        $_aSection = $this->aSection + self::$aStructure;
        $_aSection[ '_index' ] = $this->iIndex;
        $_aSection[ '_count_subsections' ] = count($this->aSubSections);
        $_aSection[ '_is_first_index' ] = $this->isFirstElement($this->aSubSections, $this->iIndex);
        $_aSection[ '_is_last_index' ] = $this->isLastElement($this->aSubSections, $this->iIndex);
        $_aSection[ '_is_collapsible' ] = $_aSection[ 'collapsible' ] && 'section' === $_aSection[ 'collapsible' ][ 'container' ];
        $_aSection[ '_tag_id' ] = 'section-' . $_aSection[ 'section_id' ] . '__' . $this->iIndex;
        $_aSection[ '_tag_id_model' ] = 'section-' . $_aSection[ 'section_id' ] . '__' . '___i___';
        return $_aSection;
    }
}
