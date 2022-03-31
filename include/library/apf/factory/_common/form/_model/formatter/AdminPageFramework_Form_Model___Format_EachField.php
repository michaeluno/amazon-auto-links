<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_Model___Format_EachField extends AmazonAutoLinks_AdminPageFramework_Form_Model___Format_FormField_Base {
    public static $aStructure = array( '_is_sub_field' => false, '_index' => 0, '_is_multiple_fields' => false, '_saved_value' => null, '_is_value_set_by_user' => false, '_field_container_id' => '', '_input_id_model' => '', '_input_name_model' => '', '_input_name_flat' => '', '_fields_container_id' => '', '_fieldset_container_id' => '', '_field_object' => null, '_parent_field_object' => null, );
    public $aField = array();
    public $isIndex = 0;
    public $aCallbacks = array();
    public $aFieldTypeDefinition = array( 'aDefaultKeys' => array( 'class' => null, ), );
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aField, $this->isIndex, $this->aCallbacks, $this->aFieldTypeDefinition, );
        $this->aField = $_aParameters[ 0 ];
        $this->isIndex = $_aParameters[ 1 ];
        $this->aCallbacks = $_aParameters[ 2 ];
        $this->aFieldTypeDefinition = $this->getAsArray($_aParameters[ 3 ]);
    }
    public function get()
    {
        $_aField = $this->aField + self::$aStructure;
        $_aField[ '_is_sub_field' ] = is_numeric($this->isIndex) && 0 < $this->isIndex;
        $_aField[ '_index' ] = $this->isIndex;
        $_oInputTagIDGenerator = new AmazonAutoLinks_AdminPageFramework_Form_View___Generate_FieldInputID($_aField, $this->isIndex, $this->aCallbacks[ 'hfID' ]);
        $_aField[ 'input_id' ] = $_oInputTagIDGenerator->get();
        $_oFieldInputNameGenerator = new AmazonAutoLinks_AdminPageFramework_Form_View___Generate_FieldInputName($_aField, $this->getAOrB($_aField[ '_is_multiple_fields' ], $this->isIndex, ''), $this->aCallbacks[ 'hfInputName' ]);
        $_aField[ '_input_name' ] = $_oFieldInputNameGenerator->get();
        $_oFieldFlatInputName = new AmazonAutoLinks_AdminPageFramework_Form_View___Generate_FlatFieldInputName($_aField, $this->getAOrB($_aField[ '_is_multiple_fields' ], $this->isIndex, ''), $this->aCallbacks[ 'hfInputNameFlat' ]);
        $_aField[ '_input_name_flat' ] = $_oFieldFlatInputName->get();
        $_aField[ '_field_container_id' ] = "field-{$_aField[ 'input_id' ]}";
        $_aField[ '_fields_container_id' ] = "fields-{$this->aField[ 'tag_id' ]}";
        $_aField[ '_fieldset_container_id' ] = "fieldset-{$this->aField[ 'tag_id' ]}";
        $_aField = $this->uniteArrays($_aField, array( 'attributes' => array( 'id' => $_aField[ 'input_id' ], 'name' => $_aField[ '_input_name' ], 'value' => $_aField[ 'value' ], 'type' => $_aField[ 'type' ], 'disabled' => null, 'data-id_model' => $_aField[ '_input_id_model' ], 'data-name_model' => $_aField[ '_input_name_model' ], ) ), ( array ) $this->aFieldTypeDefinition[ 'aDefaultKeys' ]);
        $_aField[ 'attributes' ][ 'class' ] = 'widget' === $_aField[ '_structure_type' ] && is_callable($this->aCallbacks[ 'hfClass' ]) ? call_user_func_array($this->aCallbacks[ 'hfClass' ], array( $_aField[ 'attributes' ][ 'class' ] )) : $_aField[ 'attributes' ][ 'class' ];
        $_aClassSelectors = $_aField[ 'class' ];
        unset($_aClassSelectors[ 'fieldrow' ], $_aClassSelectors[ 'fieldset' ], $_aClassSelectors[ 'fields' ], $_aClassSelectors[ 'field' ]);
        $_aField[ 'attributes' ][ 'class' ] = $this->getClassAttribute($_aField[ 'attributes' ][ 'class' ], $this->dropElementsByType($_aClassSelectors));
        $_aField[ '_field_object' ] = new AmazonAutoLinks_AdminPageFramework_ArrayHandler($_aField);
        return $_aField;
    }
}
