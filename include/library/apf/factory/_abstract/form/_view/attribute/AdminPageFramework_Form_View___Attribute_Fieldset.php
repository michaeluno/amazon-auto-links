<?php
class AmazonAutoLinks_AdminPageFramework_Form_View___Attribute_Fieldset extends AmazonAutoLinks_AdminPageFramework_Form_View___Attribute_FieldContainer_Base {
    public $sContext = 'fieldset';
    protected function _getAttributes() {
        return array('id' => $this->sContext . '-' . $this->aArguments['tag_id'], 'class' => 'amazon-auto-links-' . $this->sContext, 'data-field_id' => $this->aArguments['tag_id'],);
    }
}