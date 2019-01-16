/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */
(function($){
    $( document ).ready( function() {
        // `aal_ajax_unit_loading` is defined in PHP
        var _loadAmazonAutoLinksAjax = function() {
            jQuery( '.amazon-auto-links.aal-js-loading' ).each( function (index, value) {
                var _aData = {

                    // Required
                    action: aal_ajax_unit_loading.action_hook_suffix,
                    aal_ajax_unit_loading_security: aal_ajax_unit_loading.nonce,

                    // Unit arguments embedded in HTML data attributes
                    data: $( this ).data(),

                    // For the contextual unit type
                    post_id: aal_ajax_unit_loading.post_id,
                    page_type: aal_ajax_unit_loading.page_type,
                    author_name: aal_ajax_unit_loading.author_name,
                    term_id: aal_ajax_unit_loading.term_id,
                    REQUEST: aal_ajax_unit_loading.REQUEST,

                    // Not used at the moment
                    referrer: window.location.href,                 // the current page URL
                };
                var _oThis = this;
                $.ajax( {
                    type: 'POST',
                    async: true,
                    url: aal_ajax_unit_loading.ajax_url,
                    data: _aData,
                    success: function( response ) {
                        var _oReplacement = $( response );
                        $( _oThis ).replaceWith( _oReplacement );
                        _oReplacement.trigger( 'aal_ajax_loaded_unit', [] ); // @since 3.8.8
                    },
                    error: function (xhr) {
                        $( _oThis ).replaceWith( '<p>' + aal_ajax_unit_loading.messages.ajax_error + '<p>');
                    }
                });
            });
        }
        setTimeout( _loadAmazonAutoLinksAjax, 1000 );
    });
}(jQuery));
