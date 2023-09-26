/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name Ajax Unit Loading
 * @version 1.1.0
 */
(function($){
    $( document ).ready( function() {

        var _loadAmazonAutoLinksAjax = function() {
            jQuery( '.amazon-auto-links.aal-js-loading' ).each( function (index, value) {
                var _aData = {

                    // Required
                    action: aalAjaxUnitLoading.actionHookSuffix,
                    aalAjaxUnitLoading_security: aalAjaxUnitLoading.nonce,
                    aal_nonce: aalAjaxUnitLoading.nonce,   // the nonce value

                    // Unit arguments embedded in HTML data attributes
                    data: $( this ).data(),

                    // For the contextual unit type
                    post_id: aalAjaxUnitLoading.post_id,
                    page_type: aalAjaxUnitLoading.page_type,
                    author_name: aalAjaxUnitLoading.author_name,
                    term_id: aalAjaxUnitLoading.term_id,
                    REQUEST: aalAjaxUnitLoading.REQUEST,

                    // Not used at the moment
                    referrer: window.location.href,                 // the current page URL
                };
                var _oThis = this;

                var _oSpinner = $( '<img src="' + aalAjaxUnitLoading.spinnerURL + '" />' );
                _oSpinner.css( { margin: '0 0.5em', 'vertical-align': 'middle', 'display': 'inline-block' } );
                $( this ).find( '.now-loading-placeholder' ).append( _oSpinner );

                $.ajax( {
                    type: 'post',
                    dataType: 'json',
                    async: true,
                    url: aalAjaxUnitLoading.ajaxURL,
                    data: _aData,
                    success: function( response ) {

                        if ( response.success ) {
                            var _oReplacement = $( response.result );
                            $( _oThis ).replaceWith( _oReplacement );
                            _oReplacement.trigger( 'aal_ajax_loaded_unit', [] ); // @since 3.8.8
                        } else {
                            $( _oThis ).replaceWith( '<p>' + response.result + '<p>');
                        }

                    },
                    error: function (xhr) {
                        $( _oThis ).replaceWith( '<p>' + aalAjaxUnitLoading.messages.ajax_error + '<p>');
                    },
                    complete: function() {
                        _oSpinner.remove();
                    }
                });
            });
        }; // _loadAmazonAutoLinksAjax
        setTimeout( _loadAmazonAutoLinksAjax, parseInt( aalAjaxUnitLoading.delay ) );
    });
}(jQuery));