/**
 * @name Opt Survey Permission
 * @version 1.0.0
 */
(function($){

    $( document ).ready( function() {
        if ( 'undefined' === typeof aalOptSurveyPermission ) {
            return;
        }
        $( 'button.button-opt-survey-permission' ).click( function( event ) {
            event.preventDefault();
            var _oSpinner   = $( '<img class="ajax-spinner test-web-page-dumper-spinner" src="' + aalOptSurveyPermission.spinnerURL + '" alt="Spinner" />' );
            _oSpinner.css({
                'vertical-align': 'middle',
                'margin': '0 0.6em',
            });
            $( this ).parent().after( _oSpinner );

            var _self = this;
            var _oContainerLabel  = $( _self ).closest( 'label' );
            var _oContainerNotice = $( _self ).closest( '.notice' );
            jQuery.ajax( {
                type: 'post',
                dataType: 'json',
                url: aalOptSurveyPermission.ajaxURL,
                data: {
                    // Required
                    action: aalOptSurveyPermission.actionHookSuffix,  // WordPress action hook name which follows after `wp_ajax_`
                    aal_nonce: aalOptSurveyPermission.nonce,          // the nonce value set in template.php
                    allowed: $(_self).data('answer') ? 1 : 0,
                },
                success: function ( response ) {
                    var _oIcon = $( '<span class="dashicons"></span>' );
                    _oIcon.css({
                        'vertical-align': 'middle',
                        'margin': '0 0.2em',
                        'color': '#00a32a'
                    });
                    var _oMessage = $( "<span>" + response.result + "</span>" );

                    if ( response.success ) {
                        _oIcon.addClass( $(_self).data( 'answer' )
                            ? 'dashicons-smiley'
                            : 'dashicons-yes'
                        );
                        _oIcon.attr( 'title', response.result );
                        _oContainerLabel.after( _oIcon );
                        _oContainerLabel.remove();
                        _oIcon.after( _oMessage );
                        setTimeout( function(){
                            _oContainerNotice.fadeOut( 1200 );
                        }, 2000 );
                        return;
                    }
                    _oIcon.addClass( 'dashicons-no' );
                    _oIcon.css({
                        'color': '#d63638',
                    });
                    _oIcon.attr( 'title', response.result );
                    _oContainerLabel.after( _oIcon );
                    _oContainerLabel.remove();
                    _oMessage.css( {
                        'color': '#d63638',
                    } );
                    _oIcon.after( _oMessage );

                },
                error: function( response ) {
                    var _oIcon = $( '<span class="dashicons"></span>' );
                    _oIcon.css({
                        'vertical-align': 'middle',
                        'margin': '0 0.2em',
                        'color': '#d63638'
                    });
                    _oIcon.addClass( 'dashicons-no' );
                    _oIcon.attr( 'title', response.status + ' ' + response.statusText );
                    _oContainerLabel.after( _oIcon );
                    _oContainerLabel.remove();
                    var _oMessage = $( "<span>" + response.status + ' ' + response.statusText + "</span>" );
                    _oMessage.css( {
                        'color': '#d63638',
                    } );
                    $( _oIcon ).after( _oMessage );

                },
                complete: function() {
                    _oSpinner.remove();
                }
            } ); // ajax
            return false; // do not click
        });

    });
}(jQuery));