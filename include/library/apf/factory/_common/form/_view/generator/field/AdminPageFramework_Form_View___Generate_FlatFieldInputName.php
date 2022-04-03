<?php
/*
 * Admin Page Framework v3.9.1b05 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___Generate_FlatFieldInputName extends AmazonAutoLinks_AdminPageFramework_Form_View___Generate_FieldInputName {
    public function get()
    {
        $_sIndex = $this->getAOrB('0' !== $this->sIndex && empty($this->sIndex), '', "|{$this->sIndex}");
        return $this->_getFiltered($this->_getFlatFieldName() . $_sIndex);
    }
}
