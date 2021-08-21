(function($){

    $( document ).ready( function(){
        
        if ( 'undefined' === typeof AmazonAutoLinks_AdminPageFrameworkSelect2FieldType ) {
            return;
        }
        debugLog( AmazonAutoLinks_AdminPageFrameworkSelect2FieldType );

        /**
         * Initialize select2 elements.
         */
        $( 'select[data-type=select2]' ).each( function () {
            initializeSelect2( this );
        });
        
    
        $().registerAmazonAutoLinks_AdminPageFrameworkCallbacks( {
            /**
             * Called when a field of this field type gets repeated.
             */
            repeated_field: function( oCloned, aModel ) {
                                
                oCloned.find( '.select2-container' ).remove();                  
                oCloned.find( 'select[data-type=select2]' ).each( function () {
                    $( this ).removeAttr( 'data-select2-id tabindex aria-hidden' );                         
                    $( this ).removeClass( 'select2-hidden-accessible' );
                    $( this ).find( 'option' ).removeAttr( 'data-select2-id' );            
                    initializeSelect2( this );
                });
                
            },
        },
        [ 'select2' ]    // subject field type slugs
        );

    });

    /**
     * Initialize elements with the given container node.
     *
     * @since       3.8.7
     * @param       oNode       The target select tag DOM node object.
     */
    function initializeSelect2( oNode ) {

        var _oSelect2Target = $( oNode );

        /**
         * Construct options.
         */
        var _aOptions       = _oSelect2Target.data();
        if ( _aOptions[ 'search_callback' ] ) {

            _aOptions = $.extend(
                {           // defaults
                    minimumInputLength: 2,
                    ajax: {
                        delay: 250,
                        cache: true,
                    },
                },
                _aOptions,  // user inputs
                {
                    ajax: {
                        url: AmazonAutoLinks_AdminPageFrameworkSelect2FieldType.ajaxURL,
                        dataType: 'json',
                        type: 'POST',   // as `page` query key conflicts with page slug, do not use `GET`.
                        data: function (params) {
                            params.page = params.page || 1;
                            return {
                                // Query Parameters
                                action:             'dummy_select2_field_type_action',
                                q:                  params.term, // search term
                                page:               params.page, // pagination number
                                doing_select2_ajax: AmazonAutoLinks_AdminPageFrameworkSelect2FieldType.nonce, // ensure it is called from here
                                field_id:           _oSelect2Target.data( 'field_id' ), // will be checked in the background
                                section_id:         _oSelect2Target.data( 'section_id' ), // will be checked in the background
                            };
                        },
                    },
                } // overriding values
            ); // end of extend

        }

        if ( _aOptions[ 'new_tag_callback' ] ) {

            /**
             * Called right before finishing creating a new tag.
             * To cancel, return void.
             *
             * When called from an AJAX search result, the `page` will be available.
             * When the user hit the token separator key such as `,`, the property will be missing.
             */
            _aOptions[ 'createTag' ] = function( obj ) {

                /**
                 * Sanitize user inputs.
                 * Must trim the word because `word` and `word ` will create the same tag `word`.
                 */
                var _sTerm = $.trim( obj.term );

                /**
                 * Check duplicates.
                 */
                var _bFoundDuplicates = false;
                _oSelect2Target.find( 'option:selected' ).each( function( iIndex, value ){
                    if ( $( this ).text() === _sTerm ) {
                        _bFoundDuplicates = true;
                        return false; // break
                    }
                } );
                if ( _bFoundDuplicates ) {
                    // If the user selects a tag from a suggester list, do not add a tag .
                    if ( obj.page ) {
                        return;
                    }
                    // If the user pressed the token separator, show the tag first and remove it in the `select2:select` event.
                    // Otherwise, the input gets stuck.
                    return {
                        id:   '__' + _sTerm + '__',   // for a temporary id
                        text: _sTerm,
                        isDuplicate: true,
                        disabled: true,
                    };
                }

                /**
                 * Performs a new tag AJAX request.
                 */
                $.ajax( {
                    type: 'POST',
                    url: AmazonAutoLinks_AdminPageFrameworkSelect2FieldType.ajaxURL,
                    data: {
                        action: 'dummy_select2_field_type_action',
                        tag: _sTerm,
                        doing_select2_ajax: AmazonAutoLinks_AdminPageFrameworkSelect2FieldType.nonce, // ensure it is called from here
                        field_id:           _oSelect2Target.data( 'field_id' ), // will be checked in the background
                        section_id:         _oSelect2Target.data( 'section_id' ), // will be checked in the background
                    },
                    error: function() {
                        showDecayingError( _oSelect2Target.parent().get( 0 ), 'Ajax request failed' );
                    },
                    success: function( data ) {

                        if ( data.error ) {
                            showDecayingError( _oSelect2Target.parent().get( 0 ), data.error );
                            return;
                        }
                        if ( data.note ) {
                            debugLog( data.note );
                        }

                        // First, release the lock so that the values will be avaiable.
                        var _oOptionTags = _oSelect2Target.find( 'option[value=\"' + '__' + data.text + '__' + '\"]' );
                            _oOptionTags.removeAttr( 'disabled' );

                        /**
                         * Retrieve the selected IDs.
                         *
                         * `_oSelect2Target.val()` also does the job but it is not updated realtime.
                         * For accurate results, parse items each.
                         */
                        var _aSelectedValues = [];
                        _oSelect2Target.find( 'option:selected' ).each( function( iIndex ){
                            _aSelectedValues.push( $( this ).val() );
                        } );

                        // Replace the temporarily set tag name with the value of ID.
                        var _isIndex = _getIndexByValue( '__' + data.text + '__', _aSelectedValues );
                        if ( null !== _isIndex ) {
                            _aSelectedValues[ _isIndex ] = data.id.toString();

                            // Add HTML option to select field
                            $( '<option value=\"' + data.id + '\">' + data.text + '</option>' )
                               .appendTo( _oSelect2Target );

                            _oSelect2Target.val( _aSelectedValues ).trigger( 'change' );

                        }

                    },
                    dataType: 'json',
                });

                return {

                    text: _sTerm,

                    // for a temporary id, adding the prefix and suffix of `__` to make it distinctive
                    // so that it will be obvious that is pending to be validated.
                    id:   '__' + _sTerm + '__',

                    // Flag a new tag to be referred from a callback
                    isNewFlag: true,

                    // Not setting `disable` here but in the `select2:select` event
                    // because this will disable the selection on UI as well.
                    // disabled: false,

                };
            };

        }

        /**
         * Adjust field element width.
         *
         * When the drop-down list width is set, if the parent container element widths are small,
         * the width on drop-down list does take effect.
         */
        if ( _aOptions[ 'width' ] && 'auto' !== _aOptions[ 'width' ] ) {
            var _oFieldContainer = _oSelect2Target.closest( '.amazon-auto-links-field-select2' );
            _oFieldContainer.css( 'width', _aOptions[ 'width' ] );
            _oFieldContainer.children( '.amazon-auto-links-select-label' )
                .css( 'width', '100%' );
            _oFieldContainer.children( '.amazon-auto-links-select-label' )
                .children( 'label' )
                .children( '.amazon-auto-links-input-container' )
                .css( 'width', '100%' );
            _aOptions[ 'width' ] = '100%';
        }

        // Icons
        if ( _oSelect2Target.data( 'hasIcons' ) ) {
            _aOptions.templateResult    = templateResultForIcon;
            _aOptions.templateSelection = templateResultForIcon;
        }

        /**
         * Initialization
         */
        _oSelect2Target.select2( _aOptions );

        // _oSelect2Target.trigger( 'select2:select' );
        /**
         * Ajax handling.
         *
         * For Ajax based fields, the selected text and their associated ids must be stored.
         * Otherwise, in the next page load, the text(label) in the drop-down list cannot be displayed.
         */
        if ( _aOptions[ 'search_callback' ] ) {

            /**
             * Set initial values.
             */
            var _oInputForEncoded = _oSelect2Target.closest( '.amazon-auto-links-field' )
                .children( 'input[data-encoded]' ).first();
            var _sData = _oInputForEncoded.val();
            if ( _sData ) {
                $.each( $.parseJSON( _sData ), function( iIndex, aItem ){
                    var _oOptionTag = $( '<option selected>' + aItem[ 'text' ] + '</option>' )
                        .val( aItem[ 'id' ] );
                    _oSelect2Target.append( _oOptionTag );
                } );
            }

            /**
             * When the user selects an item, set a JSON encoded string to a hidden input with the key of `encoded`.
             *
             * Deselect items with the value of `__string__` as these are pending for update via Ajax.
             * And if the user saves the form with these items, the saved values messes up with IDs and dummy index.
             */
            _oSelect2Target.on( 'change', function( event ){

                /**
                 * Construct the data to store as JSON. Get the values (id and text) of each option.
                 * $( this ) will be the `<select>` element.
                 */
                var _aText   = [];
                var _aValues = [];
                $( this ).find( 'option:selected' ).each( function( index ){

                    var _sID   = $( this ).val();
                    var _sText = $( this ).text();

                    // Ignore pending items.
                    if ( _isItemPending( _sID ) ) {
                        return true;
                    }

                    // Check duplicated items,
                    if ( -1 !== $.inArray( _sText, _aText ) ) {
                        $( this ).removeAttr( 'selected' );
                        return true;
                    }

                    _aText.push( _sText );
                    _aValues.push( {
                        id: _sID,
                        text: _sText,    // the label
                    } );

                } );

                // Set the encoded value.
                $( this ).closest( '.amazon-auto-links-field' )
                    .children( 'input[data-encoded]' ).first()
                    .val( JSON.stringify( _aValues ) );

            } );
        }

        if ( _aOptions[ 'new_tag_callback' ] ) {

            /**
             * Handles removing duplicate tags.
             */
            _oSelect2Target.on( 'select2:select', function( event ) {

                // Check the flag inserted in the `createTag` callback.
                if ( event.params.data.isDuplicate ) {
                    $( this ).find( 'option[value=\"' + '__' + event.params.data.text + '__' + '\"]' )
                        .removeAttr( 'selected' );
                    return;
                }

                /**
                 * Temporarily disable the subject tag.
                 *
                 * So when the form is submitted, pending items won't be sent.
                 */
                $( this ).find( 'option[value=\"' + '__' + event.params.data.text + '__' + '\"]' )
                    .attr( 'disabled', 'disabled' );

            } );

        }

        // On change handling
        _oSelect2Target.on('select2:select', onSelect2Select );
        toggleElementVisibility( _oSelect2Target );

    }

    function onSelect2Select( event ) {
        toggleElementVisibility( event.target );
    }
    function toggleElementVisibility( subject ) {

        var _sTargetSelector = $( subject ).children( 'option:selected' ).data( 'toggle' );
        var _bGlobal         = $( subject ).data( 'global' );
        var _sSelectors      = $( subject ).data( 'selectors' );

        // If the `selector` field argument is not set, the data 'selectors' is not set. In that case, no need to take action.
        if ( ! _sSelectors ) {
            return;
        }

        // Hide all the subject elements first
        var _oAllSubjects    = _bGlobal
            ? $( _sSelectors )
            : $( subject ).closest( '.amazon-auto-links-section' ).find( _sSelectors );
        _oAllSubjects.hide();

        // Get the revealing elements
        var _oElementToReveal       = _bGlobal
            ? $( _sTargetSelector )
            : $( subject ).closest( '.amazon-auto-links-section' ).find( _sTargetSelector );

        if ( ! _oElementToReveal.length  ) {
            return;
        }

        // The <select> type supports `undefined` to unselect all.
        if ( 'undefined' === _sTargetSelector ) {
            return;
        }

        // Reveal the element
        _oElementToReveal.fadeIn();

    }

    function templateResultForIcon( state ) {
        var _oIconDefinition = $( state.element ).closest( '.amazon-auto-links-field' ).find( '.custom-icon-definition[data-value=' + state.id + ']' );
        if ( _oIconDefinition.length ) {
            return $( "<span class='select2-custom-icon-container'><span class='select2-custom-icon'>" + _oIconDefinition.html() + "</span><span class='select2-custom-icon-label'>" + state.text + "</span></span>" ) ;
        }
        return state.text;
    }

        /**
         * Checks whether a given string is of a pending item.
         *
         * A pending item here refers to a string in a form `__string__`.
         * This is a custom ID for pending items set in the `createTag` callback function of the `ajax` select2 argument.
         *
         * @return      boolean
         */
        function _isItemPending( isIndex ) {
            var _bHasSuffix = isIndex.lastIndexOf( '__', 0) === 0;
            var _bHasPrefix = isIndex.indexOf( '__' ) === 0;
            return ( _bHasSuffix && _bHasPrefix );
        }

        /**
         * Search the index (key) in a plain object.
         */
        function _getIndexByValue( sSearch, oObject ) {
            var _nsiResult = null;
            $.each( oObject, function( isKey, value ) {
                if ( value === sSearch ) {
                    _nsiResult = isKey;
                    return false;
                }
            } );
            return _nsiResult;
        }

        function _getNumberOfValues( sSearch, oObject ){
            var _iCount = 0;
            $.each( oObject, function( isKey, value ) {
                if ( value === sSearch ) {
                    _iCount++;
                }
            } );
            return _iCount;
        }

        /**
         * Returns an array with the key of an ID and the value of a name of option tag elements.
         */
        function _getSelectedNames( oSelectNode ) {
            var _aSelection = $( oSelectNode ).val();
            var _aSelectedNames = {};
            $.each( _aSelection, function( iIndex, isValue ){
                _aSelectedNames[ isValue ] = $( oSelectNode ).find( 'option[value=\"' + isValue + '\"]').text();
            } );
            return _aSelectedNames;
        }

    function debugLog( ...message ) {
        if ( ! parseInt( AmazonAutoLinks_AdminPageFrameworkSelect2FieldType.debugMode ) ) {
            return;
        }
        console.log( 'APF Select2: ', ...message );
    }

    /**
     * Shows an error message that disappears in given milliseconds.
     */
    function showDecayingError( oNode, sMessage, iMilliseconds ) {

        iMilliseconds = 'undefined' === typeof iMilliseconds ? 4000 : parseInt( iMilliseconds );
        var _oError = $( '<div class=\"error notice is-dismissible\"><p>' + sMessage + '</p></div>' );
        _oError.appendTo( $( oNode ) )
            .delay( iMilliseconds ).fadeOut( 'slow' );
        setTimeout( function() {
            _oError.remove();
        }, iMilliseconds*1 + 2000 );

    }

})(jQuery);