<?php
class AmazonAutoLinks_AdminPageFramework_Form_View___Generate_FlatFieldInputName extends AmazonAutoLinks_AdminPageFramework_Form_View___Generate_FieldInputName {
    public function get() {
        $_sIndex = $this->getAOrB('0' !== $this->sIndex && empty($this->sIndex), '', "|{$this->sIndex}");
        return $this->_getFiltered($this->_getFlatFieldName() . $_sIndex);
    }
}