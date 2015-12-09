<?php
abstract class AmazonAutoLinks_AdminPageFramework_Form_View___Generate_Field_Base extends AmazonAutoLinks_AdminPageFramework_Form_View___Generate_Section_Base {
    protected function _isSectionSet() {
        return isset($this->aArguments['section_id']) && $this->aArguments['section_id'] && '_default' !== $this->aArguments['section_id'];
    }
}