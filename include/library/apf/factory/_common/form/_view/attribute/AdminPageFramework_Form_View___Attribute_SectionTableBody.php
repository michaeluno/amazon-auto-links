<?php
/*
 * Admin Page Framework v3.9.1b05 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___Attribute_SectionTableBody extends AmazonAutoLinks_AdminPageFramework_Form_View___Attribute_Base {
    public $sContext = 'section_table_content';
    protected function _getAttributes()
    {
        $_sCollapsibleType = $this->getElement($this->aArguments, array( 'collapsible', 'type' ), 'box');
        return array( 'class' => $this->getAOrB($this->aArguments[ '_is_collapsible' ], 'amazon-auto-links-collapsible-section-content' . ' ' . 'amazon-auto-links-collapsible-content' . ' ' . 'accordion-section-content' . ' ' . 'amazon-auto-links-collapsible-content-type-' . $_sCollapsibleType, null), );
    }
}
