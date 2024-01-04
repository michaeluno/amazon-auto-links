<?php
/*
 * Admin Page Framework v3.9.2b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2023, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_Model___FieldTypeRegistration extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public function __construct(array $aFieldTypeDefinition, $sStructureType)
    {
        $this->_initialize($aFieldTypeDefinition, $sStructureType);
    }
    private function _initialize($aFieldTypeDefinition, $sStructureType)
    {
        if (is_callable($aFieldTypeDefinition[ 'hfFieldSetTypeSetter' ])) {
            call_user_func_array($aFieldTypeDefinition[ 'hfFieldSetTypeSetter' ], array( $sStructureType ));
        }
        if (is_callable($aFieldTypeDefinition[ 'hfFieldLoader' ])) {
            call_user_func_array($aFieldTypeDefinition[ 'hfFieldLoader' ], array());
        }
    }
}
