<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_FieldType_section_title extends AmazonAutoLinks_AdminPageFramework_FieldType_text {
    public $aFieldTypeSlugs = array( 'section_title', );
    protected $aDefaultKeys = array( 'label_min_width' => 30, 'attributes' => array( 'size' => 20, 'maxlength' => 100, ), );
    protected function getField($aField)
    {
        $aField[ 'attributes' ] = array( 'type' => 'text' ) + $aField[ 'attributes' ];
        return parent::getField($aField);
    }
}
