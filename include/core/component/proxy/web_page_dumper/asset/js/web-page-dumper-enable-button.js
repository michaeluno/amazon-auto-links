/**
 * @name Web Page Dumper Enable Button
 * @version 1.0.0
 */
(function($){

    $( document ).ready( function() {
        if ( 'undefined' === typeof aalWebPageDumperEnable ) {
            return;
        }
 
        $( '.web-page-dumper-action[data-action]' ).click( function( event ) {
            event.preventDefault();
            var _oSpinner   = $( '<img class="ajax-spinner test-web-page-dumper-spinner" src="' + aalWebPageDumperEnable.spinnerURL + '" alt="Spinner" />' );
            _oSpinner.css({
                'vertical-align': 'middle',
                'margin': '0 0.6em',
            });
            $( this ).after( _oSpinner );

            var _self = this;
            var _oContainerNotice = $( _self ).closest( '.notice' );
            jQuery.ajax( {
                type: 'post',
                dataType: 'json',
                url: aalWebPageDumperEnable.ajaxURL,
                data: {
                    // Required
                    action: aalWebPageDumperEnable.actionHookSuffix,  // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalWebPageDumperEnable.nonce,   // the nonce value set in template.php
                    enable: 1
                },
                success: function ( response ) {
                    var _oIcon = $( '<span class="dashicons"></span>' );
                    _oIcon.css({
                        'vertical-align': 'middle',
                        'margin': '0 0.4em',
                        'color': '#00a32a'
                    });
                    if ( response.success ) {
                        _oIcon.addClass( 'dashicons-yes-alt' );
                        _oIcon.attr( 'title', response.result );
                        $( _self ).after( _oIcon );
                        $( _self ).remove();
                        // If the user is on the HTTP Proxies tab,
                        $( 'input[type=checkbox][name$="\\[web_page_dumper\\]\\[enable\\]"]' ).prop('checked', true );
                        setTimeout( function(){
                            _oContainerNotice.fadeOut( 1000 );
                        }, 3000 );
                        return;
                    }
                    _oIcon.addClass( 'dashicons-no' );
                    _oIcon.css({
                        'color': '#d63638',
                    });
                    _oIcon.attr( 'title', response.result );
                    $( _self ).after( _oIcon );
                    var _oMessage = $( "<span>" + response.result + "</span>" );
                    _oMessage.css( {
                        'color': '#d63638',
                    } );
                    $( _oIcon ).after( _oMessage );
                    $( _self ).remove();
                },
                error: function( response ) {
                    var _oIcon = $( '<span class="dashicons"></span>' );
                    _oIcon.css({
                        'vertical-align': 'middle',
                        'margin': '0 0.4em',
                        'color': '#d63638'
                    });
                    _oIcon.addClass( 'dashicons-no' );
                    _oIcon.attr( 'title', response.status + ' ' + response.statusText );
                    $( _self ).after( _oIcon );
                    var _oMessage = $( "<span>" + response.status + ' ' + response.statusText + "</span>" );
                    _oMessage.css( {
                        'color': '#d63638',
                    } );
                    $( _oIcon ).after( _oMessage );
                    $( _self ).remove();
                },
                complete: function() {
                    _oSpinner.remove();
                }
            } ); // ajax

            return false; // do not click
        });

    });
}(jQuery));