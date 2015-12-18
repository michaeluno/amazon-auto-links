<?php
/**
 Admin Page Framework v3.7.5b01 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/amazon-auto-links>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AmazonAutoLinks_AdminPageFramework_FieldType_media extends AmazonAutoLinks_AdminPageFramework_FieldType_image {
    public $aFieldTypeSlugs = array('media',);
    protected $aDefaultKeys = array('attributes_to_store' => array(), 'show_preview' => true, 'allow_external_source' => true, 'attributes' => array('input' => array('size' => 40, 'maxlength' => 400,), 'button' => array(), 'remove_button' => array(), 'preview' => array(),),);
    protected function getScripts() {
        return $this->_getScript_MediaUploader("admin_page_framework") . PHP_EOL . $this->_getScript_RegisterCallbacks();
    }
    protected function _getScript_RegisterCallbacks() {
        $_aJSArray = json_encode($this->aFieldTypeSlugs);
        return <<<JAVASCRIPTS
jQuery( document ).ready( function(){
            
    jQuery().registerAmazonAutoLinks_AdminPageFrameworkCallbacks( {    
        /**
         * The repeatable field callback for the add event.
         * 
         * @param object    node
         * @param string    the field type slug
         * @param string    the field container tag ID
         * @param integer   the caller type. 1 : repeatable sections. 0 : repeatable fields.
         */     
        added_repeatable_field: function( oCloned, sFieldType, sFieldTagID, iCallType ) {
            
            // Return if it is not the type.
            if ( oCloned.find( '.select_media' ).length <= 0 ) {
                return;
            }
            
            // Update attributes.
            
            // Repeatable Sections
            if ( 1 === iCallType ) {
                var _oSectionsContainer     = jQuery( oCloned ).closest( '.amazon-auto-links-sections' );
                var _iSectionIndex          = _oSectionsContainer.attr( 'data-largest_index' );
                var _sSectionIDModel        = _oSectionsContainer.attr( 'data-section_id_model' );
                jQuery( oCloned ).find( '.select_media' ).incrementAttribute(
                    'id', // attribute name
                    _iSectionIndex, // increment from
                    _sSectionIDModel // digit model
                );                                  
            } 
            // Repeatable fields
            else {
                var _oFieldContainer    = oCloned.closest( '.amazon-auto-links-field' );
                var _oFieldsContainer   = jQuery( oCloned ).closest( '.amazon-auto-links-fields' );
                var _iFieldIndex        = Number( _oFieldsContainer.attr( 'data-largest_index' ) - 1 );
                var _sFieldTagIDModel   = _oFieldsContainer.attr( 'data-field_tag_id_model' );                
                jQuery( oCloned ).find( '.select_media' ).incrementAttribute(
                    'id', // attribute name
                    _iFieldIndex, // increment from
                    _sFieldTagIDModel // digit model
                );                
            }
            
            // Bind the event.
            var _oMediaInput = jQuery( oCloned ).find( '.media-field input' );
            if ( _oMediaInput.length <= 0 ) {
                return true;
            }
            setAmazonAutoLinks_AdminPageFrameworkMediaUploader( 
                _oMediaInput.attr( 'id' ), 
                true, 
                jQuery( oCloned ).find( '.select_media' ).attr( 'data-enable_external_source' ) 
            );
       
        }

    },
    {$_aJSArray}
    );
});
JAVASCRIPTS;
        
    }
    private function _getScript_MediaUploader($sReferrer) {
        $_sThickBoxTitle = esc_js($this->oMsg->get('upload_file'));
        $_sThickBoxButtonUseThis = esc_js($this->oMsg->get('use_this_file'));
        $_sInsertFromURL = esc_js($this->oMsg->get('insert_from_url'));
        if (!function_exists('wp_enqueue_media')) {
            return <<<JAVASCRIPTS
                    /**
                     * Bind/rebinds the thickbox script the given selector element.
                     * The fMultiple parameter does not do anything. It is there to be consistent with the one for the WordPress version 3.5 or above.
                     */
                    setAmazonAutoLinks_AdminPageFrameworkMediaUploader = function( sInputID, fMultiple, fExternalSource ) {
                        jQuery( '#select_media_' + sInputID ).unbind( 'click' ); // for repeatable fields
                        jQuery( '#select_media_' + sInputID ).click( function() {
                            var sPressedID = jQuery( this ).attr( 'id' );
                            window.sInputID = sPressedID.substring( 13 ); // remove the select_media_ prefix and set a property to pass it to the editor callback method.
                            window.original_send_to_editor = window.send_to_editor;
                            window.send_to_editor = hfAmazonAutoLinks_AdminPageFrameworkSendToEditorMedia;
                            var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
                            tb_show( '{$_sThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$sReferrer}&amp;button_label={$_sThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
                            return false; // do not click the button after the script by returning false.     
                        });    
                    }     
                                                    
                    var hfAmazonAutoLinks_AdminPageFrameworkSendToEditorMedia = function( sRawHTML, param ) {

                        var sHTML = '<div>' + sRawHTML + '</div>'; // This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
                        var src = jQuery( 'a', sHTML ).attr( 'href' );
                        var classes = jQuery( 'a', sHTML ).attr( 'class' );
                        var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : ''; // attachment ID    
                    
                        // If the user wants to save relavant attributes, set them.
                        var sInputID = window.sInputID;
                        jQuery( '#' + sInputID ).val( src ); // sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
                        jQuery( '#' + sInputID + '_id' ).val( id );     
                            
                        // restore the original send_to_editor
                        window.send_to_editor = window.original_send_to_editor;
                        
                        // close the thickbox
                        tb_remove();    

                    }
JAVASCRIPTS;
            
        }
        return <<<JAVASCRIPTS
                // Global Function Literal 
                /**
                 * Binds/rebinds the uploader button script to the specified element with the given ID.
                 */     
                setAmazonAutoLinks_AdminPageFrameworkMediaUploader = function( sInputID, fMultiple, fExternalSource ) {

                    var _bEscaped = false;
                    var _oMediaUploader;
                    
                    jQuery( '#select_media_' + sInputID ).unbind( 'click' ); // for repeatable fields
                    jQuery( '#select_media_' + sInputID ).click( function( e ) {
                
                        // Reassign the input id from the pressed element ( do not use the passed parameter value to the caller function ) for repeatable sections.
                        var sInputID = jQuery( this ).attr( 'id' ).substring( 13 ); // remove the select_image_ prefix and set a property to pass it to the editor callback method.

                        window.wpActiveEditor = null;     
                        e.preventDefault();
                        
                        // If the uploader object has already been created, reopen the dialog
                        if ( 'object' === typeof _oMediaUploader ) {
                            _oMediaUploader.open();
                            return;
                        }     
                        
                        // Store the original select object in a global variable
                        oAmazonAutoLinks_AdminPageFrameworkOriginalMediaUploaderSelectObject = wp.media.view.MediaFrame.Select;
                        
                        // Assign a custom select object.
                        wp.media.view.MediaFrame.Select = fExternalSource ? getAmazonAutoLinks_AdminPageFrameworkCustomMediaUploaderSelectObject() : oAmazonAutoLinks_AdminPageFrameworkOriginalMediaUploaderSelectObject;
                        _oMediaUploader = wp.media({
                            title:      fExternalSource
                                ? '{$_sInsertFromURL}'
                                : '{$_sThickBoxTitle}',
                            button:     {
                                text: '{$_sThickBoxButtonUseThis}'
                            },
                            multiple:   fMultiple, // Set this to true to allow multiple files to be selected
                            metadata:   {},
                        });
            
                        // When the uploader window closes, 
                        _oMediaUploader.on( 'escape', function() {
                            _bEscaped = true;
                            return false;
                        });    
                        _oMediaUploader.on( 'close', function() {

                            var state = _oMediaUploader.state();
                            
                            // Check if it's an external URL
                            if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) {

                                // 3.4.2+ Somehow the image object breaks when it is passed to a function or cloned or enclosed in an object so recreateing it manually.
                                var _oMedia = {}, _sKey;
                                for ( _sKey in state.props.attributes ) {
                                    _oMedia[ _sKey ] = state.props.attributes[ _sKey ];
                                }      
                                
                            }
                            
                            // If the image variable is not defined at this point, it's an attachment, not an external URL.
                            if ( typeof( _oMedia ) !== 'undefined'  ) {
                                setMediaPreviewElementWithDelay( sInputID, _oMedia );
                            } else {
                                
                                var _oNewField;
                                _oMediaUploader.state().get( 'selection' ).each( function( oAttachment, iIndex ) {

                                    var _oAttributes = oAttachment.hasOwnProperty( 'attributes' )
                                        ? oAttachment.attributes
                                        : {};                                    
                                    
                                    if( 0 === iIndex ){    
                                        // place first attachment in field
                                        setMediaPreviewElementWithDelay( sInputID, _oAttributes );
                                        return true;
                                    } 
                                        
                                    var _oFieldContainer    = 'undefined' === typeof _oNewField 
                                        ? jQuery( '#' + sInputID ).closest( '.amazon-auto-links-field' ) 
                                        : _oNewField;
                                    _oNewField              = jQuery( this ).addAmazonAutoLinks_AdminPageFrameworkRepeatableField( _oFieldContainer.attr( 'id' ) );
                                    var sInputIDOfNewField  = _oNewField.find( 'input' ).attr( 'id' );
                                    setMediaPreviewElementWithDelay( sInputIDOfNewField, _oAttributes );
                                
                                });     
                                
                            }
                            
                            // Restore the original select object.
                            wp.media.view.MediaFrame.Select = oAmazonAutoLinks_AdminPageFrameworkOriginalMediaUploaderSelectObject;    
                            
                        });
                        
                        // Open the uploader dialog
                        _oMediaUploader.open();     
                        return false;       
                    });    
                
                
                    var setMediaPreviewElementWithDelay = function( sInputID, oImage, iMilliSeconds ) {
                        
                        iMilliSeconds = 'undefiend' === typeof iMilliSeconds ? 100 : iMilliSeconds;
                        setTimeout( function (){
                            if ( ! _bEscaped ) {
                                setMediaPreviewElement( sInputID, oImage );
                            }
                            _bEscaped = false;
                        }, iMilliSeconds );
                        
                    }
                    
                }   

                /**
                 * Removes the set values to the input tags.
                 * 
                 * @since   3.2.0
                 */
                removeInputValuesForMedia = function( oElem ) {

                    var _oImageInput = jQuery( oElem ).closest( '.amazon-auto-links-field' ).find( '.media-field input' );                  
                    if ( _oImageInput.length <= 0 )  {
                        return;
                    }
                    
                    // Find the input tag.
                    var _sInputID = _oImageInput.first().attr( 'id' );
                    
                    // Remove the associated values.
                    setMediaPreviewElement( _sInputID, {} );
                    
                }
                
                /**
                 * Sets the preview element.
                 * 
                 * @since   3.2.0   Changed the scope to global.
                 */                
                setMediaPreviewElement = function( sInputID, oSelectedFile ) {
                                
                    // If the user want the attributes to be saved, set them in the input tags.
                    jQuery( '#' + sInputID ).val( oSelectedFile.url ); // the url field is mandatory so  it does not have the suffix.
                    jQuery( '#' + sInputID + '_id' ).val( oSelectedFile.id );     
                    jQuery( '#' + sInputID + '_caption' ).val( jQuery( '<div/>' ).text( oSelectedFile.caption ).html() );     
                    jQuery( '#' + sInputID + '_description' ).val( jQuery( '<div/>' ).text( oSelectedFile.description ).html() );     
                    
                }                 
JAVASCRIPTS;
        
    }
    protected function getStyles() {
        return <<<CSSRULES
/* Media Uploader Button */
.amazon-auto-links-field-media input {
    margin-right: 0.5em;
    vertical-align: middle;    
}
@media screen and (max-width: 782px) {
    .amazon-auto-links-field-media input {
        margin: 0.5em 0.5em 0.5em 0;
    }
}     
.select_media.button.button-small,
.remove_media.button.button-small
{     
    vertical-align: middle;
}
.remove_media.button.button-small {
    margin-left: 0.2em;
}            
CSSRULES;
        
    }
    protected function _getPreviewContainer($aField, $sImageURL, $aPreviewAtrributes) {
        return "";
    }
    protected function _getUploaderButtonScript($sInputID, $bRpeatable, $bExternalSource, array $aButtonAttributes) {
        $_sButtonHTML = '"' . $this->_getUploaderButtonHTML_Media($sInputID, $aButtonAttributes, $bExternalSource) . '"';
        $_sScript = <<<JAVASCRIPTS
if ( jQuery( 'a#select_media_{$sInputID}' ).length == 0 ) {
    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
}
jQuery( document ).ready( function(){     
    setAmazonAutoLinks_AdminPageFrameworkMediaUploader( '{$sInputID}', '{$bRpeatable}', '{$bExternalSource}' );
});
JAVASCRIPTS;
        return "<script type='text/javascript' class='amazon-auto-links-media-uploader-button'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>" . PHP_EOL;
    }
    private function _getUploaderButtonHTML_Media($sInputID, array $aButtonAttributes, $bExternalSource) {
        $_bIsLabelSet = isset($aButtonAttributes['data-label']) && $aButtonAttributes['data-label'];
        $_aAttributes = $this->_getFormattedUploadButtonAttributes_Media($sInputID, $aButtonAttributes, $_bIsLabelSet, $bExternalSource);
        return "<a " . $this->getAttributes($_aAttributes) . ">" . $this->getAOrB($_bIsLabelSet, $_aAttributes['data-label'], $this->getAOrB(strrpos($_aAttributes['class'], 'dashicons'), '', $this->oMsg->get('select_file'))) . "</a>";
    }
    private function _getFormattedUploadButtonAttributes_Media($sInputID, array $aButtonAttributes, $_bIsLabelSet, $bExternalSource) {
        $_aAttributes = array('id' => "select_media_{$sInputID}", 'href' => '#', 'data-uploader_type' => ( string )function_exists('wp_enqueue_media'), 'data-enable_external_source' => ( string )( bool )$bExternalSource,) + $aButtonAttributes + array('title' => $_bIsLabelSet ? $aButtonAttributes['data-label'] : $this->oMsg->get('select_file'), 'data-label' => null,);
        $_aAttributes['class'] = $this->getClassAttribute('select_media button button-small ', $this->getAOrB(trim($aButtonAttributes['class']), $aButtonAttributes['class'], $this->getAOrB(!$_bIsLabelSet && version_compare($GLOBALS['wp_version'], '3.8', '>='), 'dashicons dashicons-portfolio', '')));
        return $_aAttributes;
    }
}