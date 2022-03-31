<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_FieldType_table extends AmazonAutoLinks_AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array( 'table', );
    protected $aDefaultKeys = array( 'save' => false, 'data' => array(), 'stripe' => true, 'collapsible' => false, 'escape' => false, 'caption' => '', 'header' => array(), 'footer' => array(), 'sortable_column' => array(), );
    protected function getEnqueuingScripts()
    {
        return array( array( 'handle_id' => 'amazon-auto-links-field-type-table', 'src' => dirname(__FILE__) . '/js/table.bundle.js', 'in_footer' => true, 'dependencies' => array( 'jquery', 'jquery-ui-accordion', 'amazon-auto-links-script-form-main' ), 'translation_var' => 'AmazonAutoLinks_AdminPageFrameworkFieldTypeTable', 'translation' => array( 'fieldTypeSlugs' => $this->aFieldTypeSlugs, 'label' => array(), ), ), );
    }
    protected function getField($aField)
    {
        return $aField[ 'before_label' ] . $this->___getLabel($aField) . $aField[ 'after_label' ] . $aField[ 'before_input' ] . "<div class='table-container'>" . $this->___getTable($aField) . "</div>" . $aField[ 'after_input' ];
    }
    private function ___getLabel($aField)
    {
        if (! strlen($aField[ 'label' ])) {
            return '';
        }
        return "<div class='amazon-auto-links-input-label-container'>" . "<label for='" . esc_attr($aField[ 'input_id' ]) . "'>" . "<span " . $this->getLabelContainerAttributes($aField, 'amazon-auto-links-input-label-string') . ">" . $aField[ 'label' ] . "</span>" . "</label>" . "</div>";
    }
    private function ___getTable($aField)
    {
        $_aAttributes = $this->___getTableAttributesFormatted($aField);
        $_aFooter = 'USE_HEADER' === $aField[ 'footer' ] ? $aField[ 'header' ] : $aField[ 'footer' ];
        $_aCollapsible = $this->getAsArray($aField[ 'collapsible' ]);
        if (empty($_aCollapsible)) {
            return $this->getTableOfArray($this->getAsArray($aField[ 'data' ]), $_aAttributes, $aField[ 'header' ], $_aFooter, $aField[ 'escape' ], $aField[ 'caption' ]);
        }
        $_sCaption = $aField[ 'caption' ] ? $aField[ 'caption' ] : __('Set the caption with the <code>caption</code> argument.', 'amazon-auto-links');
        $_sContent = is_scalar($aField[ 'data' ]) ? "<div class='text-content'>{$aField[ 'data' ]}</div>" : $this->getTableOfArray($this->getAsArray($aField[ 'data' ]), $_aAttributes, $aField[ 'header' ], $_aFooter, $aField[ 'escape' ]);
        $_aCollapsible = $this->getAsArray($_aCollapsible) + array( 'active' => null );
        $_aCollapsible[ 'active' ] = is_numeric($_aCollapsible[ 'active' ]) ? ( integer ) $_aCollapsible[ 'active' ] : ($_aCollapsible[ 'active' ] ? 'true' : 'false');
        return "<div class='accordion-container' " . $this->getDataAttributes($_aCollapsible) . ">" . "<div class='accordion-title'><h4><span>{$_sCaption}</span></h4></div>" . "<div class='accordion-content'>{$_sContent}</div>" . "</div>";
    }
    private function ___getTableAttributesFormatted(array $aField)
    {
        $_aAttributes = $aField[ 'attributes' ];
        $this->setMultiDimensionalArray($_aAttributes, array( 'table', 'class' ), $this->getClassAttribute($this->getElementAsArray($_aAttributes, array( 'table', 'class' )), 'widefat fixed', $aField[ 'stripe' ] ? "striped " : ''));
        foreach ($this->getAsArray($aField[ 'sortable_column' ]) as $_iColumnIndex => $_bSortable) {
            if (empty($_bSortable)) {
                continue;
            }
            $this->setMultiDimensionalArray($_aAttributes, array( 'th', $_iColumnIndex, 'class' ), $this->getClassAttribute($this->getElementAsArray($_aAttributes, array( 'th', $_iColumnIndex, 'class' )), 'sortable-column'));
            $this->setMultiDimensionalArray($_aAttributes, array( 'td', $_iColumnIndex, 'class' ), $this->getClassAttribute($this->getElementAsArray($_aAttributes, array( 'th', $_iColumnIndex, 'class' )), 'sortable-column'));
        }
        return $_aAttributes;
    }
}
