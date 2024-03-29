/**
 * @name Get New Templates
 * @version 1.0.0
 */
(function($){

    var doneDisableAutoload = false;

    // Load templates Ajax handling
    $( document ).ready( function() {

        if ( 'undefined' === typeof aalNewTemplates ) {
            console.log( 'The script cannot load because necessary data is not passed.' );
            return;
        }
        debugLog( aalNewTemplates.pluginName, aalNewTemplates );

        $( '.do-not-load' ).on( 'click', doAjaxDisableAutoload );

        var _button = $( '.button-container .load-button' );
        if ( _button.data( 'allowed' ) ) {
            _button.hide()
            _doAjaxForNewTemplates( _button );
            return;
        }
        _button.on( 'click', doAjaxForNewTemplates );
    } );

    function doAjaxDisableAutoload() {

        if ( doneDisableAutoload ) {
            return;
        }

        var _self = this;
        var _oSpinner   = $( '<img src="' + aalNewTemplates.spinnerURL + '" alt="Spinner" />' );
        _oSpinner.addClass( 'ajax-spinner' );
        jQuery.ajax( {
            type: 'post',
            dataType: 'json',
            url: aalNewTemplates.ajaxURL,
            data: {
                // Required
                action: aalNewTemplates.actionHookSuffix,  // WordPress action hook name which follows after `wp_ajax_`
                aal_nonce: aalNewTemplates.nonce,   // the nonce value set in template.php
                disableAutoload: true,
            },
            success: function ( response ) {
                if ( response.success ) {
                    $( _self ).append( '<span class="dashicons dashicons-yes"></span>' );
                    doneDisableAutoload = true;
                } else {
                    $( _self ).closest( '.template-list' ).append( '<div class="error amazon-auto-links-settings-notice-message amazon-auto-links-settings-notice-container notice">'
                        + '<p>'
                            + aalNewTemplates.labels.error
                        + '</p>'
                    );
                }
            },
            error: function( response ) {
                $( _self ).closest( '.template-list' ).append( '<div class="error amazon-auto-links-settings-notice-message amazon-auto-links-settings-notice-container notice">'
                    + '<p>'
                        + response.status + ' ' + response.statusText
                    + '</p>'
                );
            },
            complete: function() {
                _oSpinner.remove();
            }
        } ); // ajax
    }

    function doAjaxForNewTemplates() {
        _doAjaxForNewTemplates( this );
    }
    function _doAjaxForNewTemplates( button ) {

        var _button = $( button );

        var _oSpinner   = $( '<img src="' + aalNewTemplates.spinnerURL + '" alt="Spinner" />' );
        _oSpinner.addClass( 'ajax-spinner' );

        var _buttonContainer = _button.parent();
        _buttonContainer.append( _oSpinner );

        jQuery.ajax( {
            type: 'post',
            dataType: 'json',
            url: aalNewTemplates.ajaxURL,
            data: {
                // Required
                action: aalNewTemplates.actionHookSuffix,  // WordPress action hook name which follows after `wp_ajax_`
                aal_nonce: aalNewTemplates.nonce,   // the nonce value set in template.php
            },
            success: function ( response ) {
                if ( response.success ) {
                    var _result = $( response.result ).hide();
                    _button.closest( '.template-list' ).append( _result );
                    _result.fadeIn();

                    $( '.do-not-load' ).fadeIn().removeClass( 'hidden' );

                } else {
                    _button.closest( '.template-list' ).append( '<div class="error amazon-auto-links-settings-notice-message amazon-auto-links-settings-notice-container notice">'
                        + '<p>'
                            + aalNewTemplates.labels.error
                        + '</p>'
                    );
                }
            },
            error: function( response ) {
                _button.closest( '.template-list' ).append( '<div class="error amazon-auto-links-settings-notice-message amazon-auto-links-settings-notice-container notice">'
                    + '<p>'
                        + response.status + ' ' + response.statusText
                    + '</p>'
                );
            },
            complete: function() {
                _oSpinner.remove();
                _buttonContainer.detach();  // not remove() as the tooltip event is still bound and causes an error if removed
            }
        } ); // ajax

    }

    // Tooltips
    $( document ).ready( function() {

        $( '.button-container.has-tooltip' ).on( 'mouseover', function() {

            // Open the tooltip
            $( this ).pointer({
                pointerClass: 'aal-pointer',
                content: function() {
                    return '<p>' + $( this ).find( '.tooltip-content' ).html() + '</p>';
                },
                position: 'top',
                buttons: function() {},
                close: function() {}
            }).pointer( 'open' );

            // Handle toolitip closing
            var _self    = this;
            $( this ).add( '.aal-pointer' ).on( 'mouseleave', function(){

                var _selfMouseLeave = this;
                // Set a timeout for the tooltip to close, allowing us to clear this trigger if the mouse comes back over
                var _timeoutId = setTimeout(function(){
                    $( _self ).pointer( 'close' );
                    $( _self ).off( 'mouseleave' );
                    $( _selfMouseLeave ).off( 'mouseleave' );
                    $( _self ).off( 'mouseenter' );
                    $( _selfMouseLeave ).off( 'mouseenter' );
                }, 650 );
                $( _self ).data( 'timeoutId', _timeoutId );

            } );
            $( this ).add( '.aal-pointer' ).on( 'mouseenter', function(){
                clearTimeout( $( _self ).data('timeoutId' ) );
            });

            return false;

        } );

    });

    function debugLog( ...args ) {
        if ( ! parseInt( aalNewTemplates.debugMode ) ) {
            return;
        }
        console.log( ...args );
    }
}(jQuery));