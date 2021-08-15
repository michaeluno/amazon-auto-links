<?php 
/**
	Admin Page Framework v3.8.32b02 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_FieldType_taxonomy extends AmazonAutoLinks_AdminPageFramework_FieldType_checkbox {
    public $aFieldTypeSlugs = array('taxonomy',);
    protected $aDefaultKeys = array('taxonomy_slugs' => 'category', 'height' => '250px', 'width' => null, 'max_width' => '100%', 'show_post_count' => true, 'attributes' => array(), 'select_all_button' => true, 'select_none_button' => true, 'label_no_term_found' => null, 'label_list_title' => '', 'query' => array('child_of' => 0, 'parent' => '', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false, 'hierarchical' => true, 'number' => '', 'pad_counts' => false, 'exclude' => array(), 'exclude_tree' => array(), 'include' => array(), 'fields' => 'all', 'slug' => '', 'get' => '', 'name__like' => '', 'description__like' => '', 'offset' => '', 'search' => '', 'cache_domain' => 'core',), 'queries' => array(), 'save_unchecked' => true,);
    protected function setUp() {
        new AmazonAutoLinks_AdminPageFramework_Form_View___Script_CheckboxSelector;
    }
    protected function getScripts() {
        $_aJSArray = json_encode($this->aFieldTypeSlugs);
        return parent::getScripts() . <<<JAVASCRIPTS
/* For tabs */
var enableAmazonAutoLinks_AdminPageFrameworkTabbedBox = function( nodeTabBoxContainer ) {
    jQuery( nodeTabBoxContainer ).each( function() {
        jQuery( this ).find( '.tab-box-tab' ).each( function( i ) {
            
            if ( 0 === i ) {
                jQuery( this ).addClass( 'active' );
            }
                
            jQuery( this ).on( 'click', function( e ){
                     
                // Prevents jumping to the anchor which moves the scroll bar.
                e.preventDefault();
                
                // Remove the active tab and set the clicked tab to be active.
                jQuery( this ).siblings( 'li.active' ).removeClass( 'active' );
                jQuery( this ).addClass( 'active' );
                
                // Find the element id and select the content element with it.
                var thisTab = jQuery( this ).find( 'a' ).attr( 'href' );
                active_content = jQuery( this ).closest( '.tab-box-container' ).find( thisTab ).css( 'display', 'block' ); 
                active_content.siblings().css( 'display', 'none' );
                
            });
        });     
    });
};        

jQuery( document ).ready( function() {
         
    enableAmazonAutoLinks_AdminPageFrameworkTabbedBox( jQuery( '.tab-box-container' ) );

    /* The repeatable event */
    jQuery().registerAmazonAutoLinks_AdminPageFrameworkCallbacks( {
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
                           
            // Update attributes.            
            oCloned.find( 'div, li.category-list' ).incrementAttribute(
                'id', // attribute name
                aModel[ 'incremented_from' ], // index incremented from
                aModel[ 'id' ] // digit model
            );
            oCloned.find( 'label' ).incrementAttribute(
                'for', // attribute name
                aModel[ 'incremented_from' ], // index incremented from
                aModel[ 'id' ] // digit model
            );            
            oCloned.find( 'li.tab-box-tab a' ).incrementAttribute(
                'href', // attribute name
                aModel[ 'incremented_from' ], // index incremented from
                aModel[ 'id' ] // digit model
            );                 
            
            // Initialize
            enableAmazonAutoLinks_AdminPageFrameworkTabbedBox( oCloned.find( '.tab-box-container' ) );
            
        },
    },
    {$_aJSArray}
    );
});     
JAVASCRIPTS;
        
    }
    protected function getStyles() {
        return ".amazon-auto-links-field .taxonomy-checklist li { margin: 8px 0 8px 20px; }.amazon-auto-links-field div.taxonomy-checklist {padding: 8px 0 8px 10px;margin-bottom: 20px;}.amazon-auto-links-field .taxonomy-checklist ul {list-style-type: none;margin: 0;}.amazon-auto-links-field .taxonomy-checklist ul ul {margin-left: 1em;}.amazon-auto-links-field .taxonomy-checklist-label {white-space: nowrap; }.amazon-auto-links-field .tab-box-container.categorydiv {max-height: none;}.amazon-auto-links-field .tab-box-tab-text {display: inline-block;font-size: 13px;font-size: smaller;padding: 2px;}.amazon-auto-links-field .tab-box-tabs {line-height: 12px;margin-bottom: 0;}.amazon-auto-links-field .tab-box-tabs .tab-box-tab.active {display: inline;border-color: #dfdfdf #dfdfdf #fff;margin-bottom: 0px;padding-bottom: 4px;background-color: #fff;}.amazon-auto-links-field .tab-box-container { position: relative; width: 100%; clear: both;margin-bottom: 1em;}.amazon-auto-links-field .tab-box-tabs li a { color: #333; text-decoration: none; }.amazon-auto-links-field .tab-box-contents-container {padding: 0 0 0 1.8em;padding: 0.55em 0.5em 0.55em 1.8em;border: 1px solid #dfdfdf; background-color: #fff;}.amazon-auto-links-field .tab-box-contents { overflow: hidden; overflow-x: hidden; position: relative; top: -1px; height: 300px;}.amazon-auto-links-field .tab-box-content { display: none; overflow: auto; display: block; position: relative; overflow-x: hidden;}.amazon-auto-links-field .tab-box-content .taxonomychecklist {margin-right: 3.2em;}.amazon-auto-links-field .tab-box-content:target, .amazon-auto-links-field .tab-box-content:target, .amazon-auto-links-field .tab-box-content:target { display: block; }.amazon-auto-links-field .tab-box-content .select_all_button_container, .amazon-auto-links-field .tab-box-content .select_none_button_container{display: inline-block;margin-top: 0.8em;}.amazon-auto-links-field .taxonomychecklist .children {margin-top: 6px;margin-left: 1em;}";
    }
    protected function getIEStyles() {
        return ".tab-box-content { display: block; }.tab-box-contents { overflow: hidden;position: relative; }b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }";
    }
    protected function getField($aField) {
        $aField['label_no_term_found'] = $this->getElement($aField, 'label_no_term_found', $this->oMsg->get('no_term_found'));
        $_aTabs = array();
        $_aCheckboxes = array();
        foreach ($this->getAsArray($aField['taxonomy_slugs']) as $_isKey => $_sTaxonomySlug) {
            $_aAssociatedDataAttributes = $this->_getDataAttributesOfAssociatedPostTypes($_sTaxonomySlug, $this->_getPostTypesByTaxonomySlug($_sTaxonomySlug));
            $_aTabs[] = $this->_getTaxonomyTab($aField, $_isKey, $_sTaxonomySlug, $_aAssociatedDataAttributes);
            $_aCheckboxes[] = $this->_getTaxonomyCheckboxes($aField, $_isKey, $_sTaxonomySlug, $_aAssociatedDataAttributes);
        }
        return "<div id='tabbox-{$aField['field_id']}' class='tab-box-container categorydiv' style='max-width:{$aField['max_width']};'>" . "<ul class='tab-box-tabs category-tabs'>" . implode(PHP_EOL, $_aTabs) . "</ul>" . "<div class='tab-box-contents-container'>" . "<div class='tab-box-contents' style='height: {$aField['height']};'>" . implode(PHP_EOL, $_aCheckboxes) . "</div>" . "</div>" . "</div>";
    }
    private function _getPostTypesByTaxonomySlug($sTaxonomySlug) {
        $_oTaxonomy = get_taxonomy($sTaxonomySlug);
        return $_oTaxonomy->object_type;
    }
    private function _getDataAttributesOfAssociatedPostTypes($sTaxonomySlusg, $aPostTypes) {
        return array('data-associated-with' => $sTaxonomySlusg, 'data-associated-post-types' => implode(',', $aPostTypes) . ',',);
    }
    private function _getTaxonomyCheckboxes(array $aField, $sKey, $sTaxonomySlug, $aAttributes) {
        $_aTabBoxContainerArguments = array('id' => "tab_{$aField['input_id']}_{$sKey}", 'class' => 'tab-box-content', 'style' => $this->getInlineCSS(array('height' => $this->getAOrB($aField['height'], $this->getLengthSanitized($aField['height']), null), 'width' => $this->getAOrB($aField['width'], $this->getLengthSanitized($aField['width']), null),)),) + $aAttributes;
        return "<div " . $this->getAttributes($_aTabBoxContainerArguments) . ">" . $this->getElement($aField, array('before_label', $sKey)) . "<div " . $this->getAttributes($this->_getCheckboxContainerAttributes($aField)) . ">" . "</div>" . "<ul class='list:category taxonomychecklist form-no-clear'>" . $this->_getTaxonomyChecklist($aField, $sKey, $sTaxonomySlug) . "</ul>" . "<!--[if IE]><b>.</b><![endif]-->" . $this->getElement($aField, array('after_label', $sKey)) . "</div><!-- tab-box-content -->";
    }
    private function _getTaxonomyChecklist($aField, $sKey, $sTaxonomySlug) {
        return wp_list_categories(array('walker' => new AmazonAutoLinks_AdminPageFramework_WalkerTaxonomyChecklist, 'taxonomy' => $sTaxonomySlug, '_name_prefix' => is_array($aField['taxonomy_slugs']) ? "{$aField['_input_name']}[{$sTaxonomySlug}]" : $aField['_input_name'], '_input_id_prefix' => $aField['input_id'], '_attributes' => $this->getElementAsArray($aField, array('attributes', $sKey)) + $aField['attributes'], '_selected_items' => $this->_getSelectedKeyArray($aField['value'], $sTaxonomySlug), 'echo' => false, 'show_post_count' => $aField['show_post_count'], 'show_option_none' => $aField['label_no_term_found'], 'title_li' => $aField['label_list_title'], '_save_unchecked' => $aField['save_unchecked'],) + $this->getAsArray($this->getElement($aField, array('queries', $sTaxonomySlug), array()), true) + $aField['query']);
    }
    private function _getSelectedKeyArray($vValue, $sTaxonomySlug) {
        $_aSelected = $this->getElementAsArray($this->getAsArray($vValue), array($sTaxonomySlug));
        return array_keys($_aSelected, true);
    }
    private function _getTaxonomyTab($aField, $sKey, $sTaxonomySlug, $aAttributes) {
        $_aLiAttribues = array('class' => 'tab-box-tab',) + $aAttributes;
        return "<li " . $this->getAttributes($_aLiAttribues) . ">" . "<a href='#tab_{$aField['input_id']}_{$sKey}'>" . "<span class='tab-box-tab-text'>" . $this->_getLabelFromTaxonomySlug($sTaxonomySlug) . "</span>" . "</a>" . "</li>";
    }
    private function _getLabelFromTaxonomySlug($sTaxonomySlug) {
        $_oTaxonomy = get_taxonomy($sTaxonomySlug);
        return isset($_oTaxonomy->label) ? $_oTaxonomy->label : '';
    }
    }
    