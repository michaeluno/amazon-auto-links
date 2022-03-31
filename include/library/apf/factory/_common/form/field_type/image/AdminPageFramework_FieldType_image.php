<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_FieldType_image extends AmazonAutoLinks_AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array( 'image', );
    protected $aDefaultKeys = array( 'attributes_to_store' => array(), 'show_preview' => true, 'allow_external_source' => true, 'attributes' => array( 'input' => array( 'size' => 40, 'maxlength' => 400, ), 'button' => array( ), 'remove_button' => array( ), 'preview' => array(), ), );
    protected function setUp()
    {
        $this->enqueueMediaUploader();
    }
    protected function getEnqueuingScripts()
    {
        return array( array( 'handle_id' => 'amazon-auto-links-field-type-image', 'src' => dirname(__FILE__) . '/js/image.bundle.js', 'in_footer' => true, 'dependencies' => array( 'jquery', 'amazon-auto-links-script-form-main' ), 'translation_var' => 'AmazonAutoLinks_AdminPageFrameworkImageFieldType', 'translation' => array( 'fieldTypeSlugs' => $this->aFieldTypeSlugs, 'referer' => 'admin_page_framework', 'hasMediaUploader' => function_exists('wp_enqueue_media'), 'label' => array( 'uploadImage' => $this->oMsg->get('upload_image'), 'useThisImage' => $this->oMsg->get('use_this_image'), 'insertFromURL' => $this->oMsg->get('insert_from_url'), ), ), ), );
    }
    protected function getField($aField)
    {
        $_iCountAttributes = count($this->getElementAsArray($aField, 'attributes_to_store'));
        $_sImageURL = $this->___getTheSetImageURL($aField, $_iCountAttributes);
        $_aBaseAttributes = $this->___getBaseAttributes($aField);
        $_aUploadButtonAttributes = $this->getElementAsArray($aField, array( 'attributes', 'button' )) + $_aBaseAttributes;
        $_aRemoveButtonAttributes = $this->getElementAsArray($aField, array( 'attributes', 'remove_button' )) + $_aBaseAttributes;
        $_bIsLabelSet = isset($_aRemoveButtonAttributes[ 'data-label' ]) && $_aRemoveButtonAttributes[ 'data-label' ];
        $_aRemoveButtonAttributes = $this->_getFormattedRemoveButtonAttributesByType($aField[ 'input_id' ], $_aRemoveButtonAttributes, $_bIsLabelSet, strtolower($this->getFirstElement($this->aFieldTypeSlugs)));
        return $aField[ 'before_label' ] . "<div class='amazon-auto-links-input-label-container amazon-auto-links-input-container {$aField[ 'type' ]}-field'>" . "<label for='{$aField[ 'input_id' ]}'>" . $aField[ 'before_input' ] . $this->getAOrB($aField[ 'label' ] && ! $aField[ 'repeatable' ], "<span " . $this->getLabelContainerAttributes($aField, 'amazon-auto-links-input-label-string') . ">" . $aField[ 'label' ] . "</span>", '') . "<input " . $this->getAttributes($this->___getImageInputAttributes($aField, $_iCountAttributes, $_sImageURL, $_aBaseAttributes)) . " />" . $this->_getUploaderButtonHTML($aField[ 'input_id' ], $_aUploadButtonAttributes, ! empty($aField[ 'repeatable' ]), $aField[ 'allow_external_source' ]) . $this->_getRemoveButtonHTMLByType($aField[ 'input_id' ], $_aRemoveButtonAttributes, strtolower($this->getFirstElement($this->aFieldTypeSlugs))) . $aField[ 'after_input' ] . "<div class='repeatable-field-buttons'></div>" . $this->getExtraInputFields($aField) . "</label>" . "</div>" . $aField[ 'after_label' ] . $this->_getPreviewContainer($aField, $_sImageURL, $this->getElementAsArray($aField, array( 'attributes', 'preview' )) + $_aBaseAttributes);
    }
    private function ___getBaseAttributes(array $aField)
    {
        $_aBaseAttributes = $aField[ 'attributes' ] + array( 'class' => null );
        unset($_aBaseAttributes[ 'input' ], $_aBaseAttributes[ 'button' ], $_aBaseAttributes[ 'preview' ], $_aBaseAttributes[ 'name' ], $_aBaseAttributes[ 'value' ], $_aBaseAttributes[ 'type' ], $_aBaseAttributes[ 'remove_button' ]);
        return $_aBaseAttributes;
    }
    private function ___getTheSetImageURL(array $aField, $iCountAttributes)
    {
        $_sCaptureAttribute = $this->getAOrB($iCountAttributes, 'url', '');
        return $_sCaptureAttribute ? $this->getElement($aField, array( 'attributes', 'value', $_sCaptureAttribute ), '') : $aField[ 'attributes' ][ 'value' ];
    }
    private function ___getImageInputAttributes(array $aField, $iCountAttributes, $sImageURL, array $aBaseAttributes)
    {
        return array( 'name' => $aField[ 'attributes' ][ 'name' ] . $this->getAOrB($iCountAttributes, '[url]', ''), 'value' => $sImageURL, 'type' => 'text', 'data-show_preview' => $aField[ 'show_preview' ], ) + $aField[ 'attributes' ][ 'input' ] + $aBaseAttributes;
    }
    protected function getExtraInputFields(array $aField)
    {
        $_aOutputs = array();
        foreach ($this->getElementAsArray($aField, 'attributes_to_store') as $sAttribute) {
            $_aOutputs[] = "<input " . $this->getAttributes(array( 'id' => "{$aField[ 'input_id' ]}_{$sAttribute}", 'type' => 'hidden', 'name' => "{$aField[ '_input_name' ]}[{$sAttribute}]", 'disabled' => $this->getAOrB(isset($aField[ 'attributes' ][ 'disabled' ]) && $aField[ 'attributes' ][ 'disabled' ], 'disabled', null), 'value' => $this->getElement($aField, array( 'attributes', 'value', $sAttribute ), ''), )) . "/>";
        }
        return implode(PHP_EOL, $_aOutputs);
    }
    protected function _getPreviewContainer($aField, $sImageURL, $aPreviewAtrributes)
    {
        if (! $aField[ 'show_preview' ]) {
            return '';
        }
        $sImageURL = esc_url($this->getResolvedSRC($sImageURL, true));
        return "<div " . $this->getAttributes(array( 'id' => "image_preview_container_{$aField[ 'input_id' ]}", 'class' => 'image_preview ' . $this->getElement($aPreviewAtrributes, 'class', ''), 'style' => $this->getAOrB($sImageURL, '', "display: none; ") . $this->getElement($aPreviewAtrributes, 'style', ''), ) + $aPreviewAtrributes) . ">" . "<img src='{$sImageURL}' " . "id='image_preview_{$aField[ 'input_id' ]}' " . "/>" . "</div>";
    }
    protected function _getUploaderButtonScript($sInputID, $abRepeatable, $bExternalSource, array $aButtonAttributes)
    {
        $_bRepeatable = ! empty($abRepeatable);
        $_sButtonHTML = '"' . $this->_getUploaderButtonHTML($sInputID, $aButtonAttributes, $_bRepeatable, $bExternalSource) . '"';
        $_sRepeatable = $this->getAOrB($_bRepeatable, 'true', 'false');
        $_bExternalSource = $this->getAOrB($bExternalSource, 'true', 'false');
        $_sScript = <<<JAVASCRIPTS
if(0===jQuery('a#select_image_{$sInputID}').length){jQuery('input#{$sInputID}').after($_sButtonHTML)}
jQuery(document).ready(function(){setAmazonAutoLinks_AdminPageFrameworkImageUploader('{$sInputID}','true'==='{$_sRepeatable}','true'==='{$_bExternalSource}')})
JAVASCRIPTS;
        return "<script type='text/javascript' class='amazon-auto-links-image-uploader-button'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>". PHP_EOL;
    }
    protected function _getUploaderButtonHTML($sInputID, array $aButtonAttributes, $bRepeatable, $bExternalSource)
    {
        $_bIsLabelSet = isset($aButtonAttributes[ 'data-label' ]) && $aButtonAttributes[ 'data-label' ];
        $_aAttributes = $this->_getFormattedUploadButtonAttributes($sInputID, $aButtonAttributes, $_bIsLabelSet, $bRepeatable, $bExternalSource);
        return "<a " . $this->getAttributes($_aAttributes) . ">" . ($_bIsLabelSet ? $_aAttributes[ 'data-label' ] : (strrpos($_aAttributes[ 'class' ], 'dashicons') ? '' : $this->oMsg->get('select_image'))) ."</a>";
    }
    protected function _getFormattedUploadButtonAttributes($sInputID, array $aButtonAttributes, $_bIsLabelSet, $bRepeatable, $bExternalSource)
    {
        $_aAttributes = array( 'id' => "select_image_{$sInputID}", 'href' => '#', 'data-input_id' => $sInputID, 'data-repeatable' => ( string ) ( boolean ) $bRepeatable, 'data-uploader_type' => ( string ) function_exists('wp_enqueue_media'), 'data-enable_external_source' => ( string ) ( boolean ) $bExternalSource, ) + $aButtonAttributes + array( 'title' => $_bIsLabelSet ? $aButtonAttributes[ 'data-label' ] : $this->oMsg->get('select_image'), 'data-label' => null, );
        $_aAttributes[ 'class' ] = $this->getClassAttribute('select_image button button-small ', $this->getAOrB(trim($aButtonAttributes[ 'class' ]), $aButtonAttributes[ 'class' ], $this->getAOrB($_bIsLabelSet, '', $this->getAOrB($bRepeatable, $this->___getDashIconSelectorsBySlug('images-alt2'), $this->___getDashIconSelectorsBySlug('format-image')))));
        return $_aAttributes;
    }
    protected function _getRemoveButtonScript($sInputID, array $aButtonAttributes, $sType='image')
    {
        if (! function_exists('wp_enqueue_media')) {
            return '';
        }
        $_sButtonHTML = '"' . $this->_getRemoveButtonHTMLByType($sInputID, $aButtonAttributes, $sType) . '"';
        $_sScript = <<<JAVASCRIPTS
if(0===jQuery('a#remove_{$sType}_{$sInputID}').length){jQuery('input#{$sInputID}').after($_sButtonHTML)}
JAVASCRIPTS;
        return "<script type='text/javascript' class='amazon-auto-links-{$sType}-remove-button'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>". PHP_EOL;
    }
    protected function _getRemoveButtonHTMLByType($sInputID, array $aButtonAttributes, $sType='image')
    {
        $_bIsLabelSet = isset($aButtonAttributes[ 'data-label' ]) && $aButtonAttributes[ 'data-label' ];
        $_aAttributes = $this->_getFormattedRemoveButtonAttributesByType($sInputID, $aButtonAttributes, $_bIsLabelSet, $sType);
        return "<a " . $this->getAttributes($_aAttributes) . ">" . ($_bIsLabelSet ? $_aAttributes[ 'data-label' ] : $this->getAOrB(strrpos($_aAttributes[ 'class' ], 'dashicons'), '', 'x')) . "</a>";
    }
    protected function _getFormattedRemoveButtonAttributesByType($sInputID, array $aButtonAttributes, $_bIsLabelSet, $sType='image')
    {
        $_aAttributes = array( 'id' => "remove_{$sType}_{$sInputID}", 'href' => '#', 'data-input_id' => $sInputID, ) + $aButtonAttributes + array( 'title' => $_bIsLabelSet ? $aButtonAttributes[ 'data-label' ] : $this->oMsg->get('remove_value'), );
        $_aAttributes[ 'class' ] = $this->getClassAttribute("remove_value remove_{$sType} button button-small", $this->getAOrB(trim($aButtonAttributes[ 'class' ]), $aButtonAttributes[ 'class' ], $this->getAOrB($_bIsLabelSet, '', $this->___getDashIconSelectorsBySlug('dismiss'))));
        return $_aAttributes;
    }
    private function ___getDashIconSelectorsBySlug($sDashIconSlug)
    {
        static $_bDashIconSupported;
        $_bDashIconSupported = isset($_bDashIconSupported) ? $_bDashIconSupported : version_compare($GLOBALS[ 'wp_version' ], '3.8', '>=');
        return $this->getAOrB($_bDashIconSupported, "dashicons dashicons-{$sDashIconSlug}", '');
    }
}
