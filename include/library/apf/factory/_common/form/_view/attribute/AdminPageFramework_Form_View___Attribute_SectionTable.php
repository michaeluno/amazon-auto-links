<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___Attribute_SectionTable extends AmazonAutoLinks_AdminPageFramework_Form_View___Attribute_Base {
    public $sContext = 'section_table';
    protected function _getAttributes()
    {
        return array( 'id' => 'section_table-' . $this->aArguments[ '_tag_id' ], 'class' => $this->getClassAttribute('form-table', 'amazon-auto-links-section-table'), );
    }
}
