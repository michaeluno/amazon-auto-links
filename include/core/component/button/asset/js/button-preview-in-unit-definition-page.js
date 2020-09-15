/**
 * @name Button Preview in Unit Definition Page
 * @version 1.0.0
 */
(function($){

    var debugMode   = 'undefined' !== typeof aalButtonPreview.debugMode ? aalButtonPreview.debugMode : 0;

    /**
     * Global function called from an iframe document.
     *
     * @remark  it seems whenever the iframe contents are modified, the child script calls this function.
     */
    aalSetButtonPreviewIframeStyle = function( iWidth, iHeight ) {

        var _oFrameContainer = $( '.iframe-button-preview-container' );
        _oFrameContainer.each( function() {
            var _oIframe = $( this ).find( 'iframe' );
            _oIframe.width( iWidth ).height( iHeight )
                .css({
                    'width':        iWidth + 'px',
                    'height':       iHeight + 'px',
                    'display':      'block',
                    'margin':       '0 auto',
                    'transform':    'translateZ(0)',
                    'min-width':    '116px',
                });
            _oIframe.contents().find( 'head' )
                .append( $( "<style type='text/css'>  html, body{ background-color: unset !important; } </style>"));
        } );

    };

    /**
     * @var aalButtonPreview {debugMode:{},activeButtons:{}}
     */
    $( document ).ready( function() {

        if ( 'undefined' === typeof aalButtonPreview ) {
            return false;
        }
        if ( debugMode ) {
            console.log( 'Amazon Auto Links Button Preview Script', aalButtonPreview );
        }

        var _setPreviewButton = function( iButtonID, oSelect ) {

            iButtonID = parseInt( iButtonID );
            var _oButton = oSelect.closest( 'fieldset' )
                .find( '.amazon-auto-links-button' );

            if( 'undefined' === typeof aalButtonPreview.activeButtons[ iButtonID ] ) {
                if ( debugMode ) {
                    console.log( 'the button label does not exists. button ID', iButtonID, 'active buttons', aalButtonPreview.activeButtons );
                }
                return;
            }

            var _oButtonContainer = $( _oButton ).parent();
            var _sButtonLabel     = oSelect.closest( '.amazon-auto-links-section-table' ).find( 'input.override-button-label[type=checkbox]' ).prop( 'checked' )
                ? oSelect.closest( '.amazon-auto-links-section-table' ).find( 'input.button-label[type=text]' ).val()
                : aalButtonPreview.activeButtons[ iButtonID ];

            // <div> type button
            if ( iButtonID ) {
                $( _oButton ).attr('class', 'amazon-auto-links-button amazon-auto-links-button-' + iButtonID );
                ___setButtonLabel_div( _sButtonLabel, oSelect );

                // Make sure the button container is visible. By default it is hidden for widget forms.
                _oButtonContainer.show();
                _oButtonContainer.siblings( '.iframe-button-preview-container' ).css({ // hide
                   'position':  'absolute',
                    'top':      '-9999px',
                    'z-depth':  -100,
                });
                return;
            }

            // the <button> type
            _oButtonContainer.hide();
            ___setButtonLabel_iframe( _sButtonLabel, oSelect );
            _oButtonContainer.siblings( '.iframe-button-preview-container' )
                .css({ 'position': 'static', 'top': 0, 'z-depth': 1, }); // show

        };

        // Initially set the preview and  the button select change.
        var _oButtonSelect = $( '.button-select-row' ).find( 'select' ); // the select tag
        _oButtonSelect.change( function() {
            _setPreviewButton( $( this ).val(), $( this ) );
        } ).trigger( 'change' );

        // When the Override Button Label option is toggled, update the label.
        $( 'input.override-button-label[type=checkbox]' ).change( function() {
            if ( ! $( this ).prop( 'checked' ) ) {
                ___revertButtonLabels( _oButtonSelect );
                return;
            }
            ___setButtonLabelByInput(
                $( this ).closest( '.amazon-auto-links-section-table' )
                .find( 'input.button-label[type=text]' ).first()
            );
        } );

        // Override the button label when the Button Label field is entered.
        $( 'input.button-label[type=text]' ).change( function(){
            var _oOverrideLabel = $( this ).closest( '.amazon-auto-links-section-table' )
                .find( 'input.override-button-label[type=checkbox]' );
            if ( ! _oOverrideLabel.prop( 'checked' ) ) {
                return;
            }
            ___setButtonLabelByInput( this );
        } );

        /**
         * On contrast to `aalSetButtonPreviewIframeStyle()`, this one only gets called once.
         */
        $( '.iframe-button-preview-container' ).find( 'iframe' ).on( 'load', function() {

            // The initial button label handling is done too early before the iframe contents are loaded when the override option is enabled
            // so here we trigger the event so that the button label will be updated.
            $( 'input.override-button-label[type=checkbox]' ).trigger( 'change' );

        });


        function ___revertButtonLabels( oButtonSelect ) {
            ___setButtonLabel_iframe( aalButtonPreview.activeButtons[ 0 ], oButtonSelect );
            $.each( aalButtonPreview.activeButtons, function( __iButtonID, __sButtonLabel ) {
                if ( 0 === __iButtonID ) {
                    return true; // skip
                }
                ___setButtonLabel_div( __sButtonLabel, oButtonSelect );
                return false;  // break
            } );
        }

        /**
         * Sets the button label by the user input.
         * @param oButtonLabelInput
         * @private
         */
        function ___setButtonLabelByInput( oButtonLabelInput ) {
            if ( 'undefined' === typeof oButtonLabelInput ) {
                return;
            }
            ___setButtonLabel( $( oButtonLabelInput ).val(), oButtonLabelInput );
        }

        /**
         * Sets the button label of a given string.
         * @param   string sLabel
         * @param   object oElem an element in the same section with the button preview
         * @private
         */
        function ___setButtonLabel( sLabel, oElem ) {
            ___setButtonLabel_div( sLabel, oElem );
            ___setButtonLabel_iframe( sLabel, oElem );
        }
            function ___setButtonLabel_div( sLabel, oElem ) {
                $( oElem ).closest( '.amazon-auto-links-section-table' )  // climb up to the section container
                    .find( '.amazon-auto-links-button' )
                    .text( sLabel );
                }
            function ___setButtonLabel_iframe( sLabel, oElem ) {
                var _oButton = $( oElem ).closest( '.amazon-auto-links-section-table' )
                    .find( 'iframe' )
                    .contents()
                    .find( 'button.amazon-auto-links-button' );
                _oButton.text( sLabel );
                if ( 'undefined' === typeof _oButton[ 0 ] ) {
                    return;
                }
                if ( 'undefined' === typeof _oButton[ 0 ].offsetWidth ) {
                    return;
                }
                aalSetButtonPreviewIframeStyle( _oButton[ 0 ].offsetWidth, _oButton[ 0 ].offsetHeight );
            }

    } ); // document ready



}( jQuery ));