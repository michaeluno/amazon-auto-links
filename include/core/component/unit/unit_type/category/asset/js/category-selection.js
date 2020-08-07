(function($){

    var aCaches       = [];
    var bProcessing   = false;
    var sCurrentURL   = '';
    var bWarnedAddingCategories = false;
    var oSelectArrow;
    var bShowArrows   = true;

    $( document ).ready( function() {

        handleGuidingArrows();

        // First retrieve the category list and hook the click event on the category list
        handleCategoryList( aalCategorySelection.rootURL );
        handleExistentCategoryItems();

        // Button Events
        handleButton_AddCategory();
        handleButton_AddExcludingCategory();
        handleButton_RemoveChecked();

        // Debugging
        if ( aalCategorySelection.debugMode ) {
            console.log( 'Amazon Auto Links - arguments', aalCategorySelection );
        }


    });

    // Unit preview event
    $( document ).on( "aalUnitPreview", {}, renderUnitPreview );

    function handleGuidingArrows() {
        bShowArrows  = parseInt( aalCategorySelection.postID ) ? false : true;
        oSelectArrow = $( '#arrow-select' );
        oSelectArrow.detach(); // this arrow element is inside the div element overridden by the Ajax response so evacuate.
    }

        /**
         * Called after the main dynamic elements (breadcrumb, category list, category preview) are rendered.
         * There are three arrows:
         * 1. Create
         * 2. Select
         * 3. Add
         * @private
         */
        function ___setArrows() {

            // For the edit screen, do not show them.
            if ( ! bShowArrows ) {
                return;
            }

            // If any categories are already added
            var _bCategoriesAdded = $( '#selected-categories' ).find( 'input[tupe=checkbox]' ).length ? true : false;

            // 1: the create arrow
            if ( _bCategoriesAdded ) {

                // 1: the create arrow
                $( '#arrow-create' ).show();

                // 2: the select arrow
                oSelectArrow.hide();

                // 3: the add arrow
                $( '#arrow-add' ).show();

                return;
            }

            // At this point, no categories are added yet.

            // For the root category (when the page is loaded)
            if ( aalCategorySelection.rootURL === sCurrentURL ) {
                // 2: the select arrow
                $( '#category-list' ).prepend( oSelectArrow.show() );

                // 3: the add arrow
                $( '#arrow-add' ).hide();
                return;
            }

            // At this point, it is a sub-category.
            oSelectArrow.hide();
            $( '#arrow-add' ).show();

        }

    function handleButton_RemoveChecked() {
        $( '#button-remove-checked' ).click( function( event ) {
            event.preventDefault();

            // Check if any added category checkbox is checked.
            var _oCheckedItems = $( '#selected-categories' ).find( 'input:checked' );
            if ( ! _oCheckedItems.length ) {
                alert( aalCategorySelection.translation.category_not_selected );
                return false;
            }
            ___removeSelectedCategories( _oCheckedItems );

        } );
    }
        function ___removeSelectedCategories( oItems ) {

            oItems.closest( '.added-category, .excluding-category' ).remove();

            // If there is none, show the default warning.
            var _oAddedCategories = $( '#added-categories');
            if ( ! _oAddedCategories.find( '.added-category' ).length ) {
                _oAddedCategories.find( '.no-categories-added' ).show();
            }
            var _oExcludingCategories = $( '#excluding-categories');
            if ( ! _oExcludingCategories.find( '.excluding-category' ).length ) {
                _oExcludingCategories.find( '.no-categories-added' ).show();
            }

            // Case: no category exists so the Create/Save button should be disabled
            if ( ! $( '#selected-categories' ).find( 'input[type=checkbox]' ).length ) {

                $( '#button-save-unit' ).attr( 'disabled', 'disabled' );

                // The guide arrow may need to appear or disappear.
                if ( bShowArrows ) {
                    $( '#arrow-create' ).hide();
                    $( '#arrow-add' ).show();
                }

            }


        }

    function handleButton_AddExcludingCategory() {
        $( '#button-add-excluding-category' ).click( function( event ){
            event.preventDefault();
            // If the current URL is not set or the cache is not set,
            if ( 'undefined' === typeof sCurrentURL ) {
                return;
            }
            if ( 'undefined' === typeof aCaches[ sCurrentURL ] ) {
                return;
            }
            var _oCache     = aCaches[ sCurrentURL ];
            var _sFor       = _oCache.checkbox_added.find( 'label' ).attr( 'for' );

            // Case: the subject category is already added.
            var _oDuplicate = $( '#excluding-categories' ).find( 'label[for=' + _sFor + ']' );
            if ( _oDuplicate.length ) {
                alert( aalCategorySelection.translation.already_added );
                return false;
            }

            // Case: the subject category is added to the other category section.
            var _oDuplicate = $( '#added-categories' ).find( 'label[for=' + _sFor + ']' );
            if ( _oDuplicate.length ) {
                ___removeSelectedCategories( _oDuplicate );
            }

            ___addCategory( $( '#excluding-categories' ), _oCache.checkbox_excluded );
            return false;
        } );
    }

    function handleButton_AddCategory() {
        $( '#button-add-category' ).click( function( event ){
            event.preventDefault();
            // If the current URL is not set or the cache is not set,
            if ( 'undefined' === typeof sCurrentURL ) {
                return;
            }
            if ( 'undefined' === typeof aCaches[ sCurrentURL ] ) {
                return;
            }

            var _oCache     = aCaches[ sCurrentURL ];
            var _sFor       = _oCache.checkbox_added.find( 'label' ).attr( 'for' );

            // Case: the subject category is already added.
            var _oDuplicate = $( '#added-categories' ).find( 'label[for=' + _sFor + ']' );
            if ( _oDuplicate.length ) {
                alert( aalCategorySelection.translation.already_added );
                return false;
            }

            // Case: the subject category is added to the other category section.
            var _oDuplicate = $( '#excluding-categories' ).find( 'label[for=' + _sFor + ']' );
            if ( _oDuplicate.length ) {
                ___removeSelectedCategories( _oDuplicate );
            }

            ___addCategory( $( '#added-categories' ), _oCache.checkbox_added );
            return false;
        } );
    }
        function ___addCategory( oContainer, oInsert ) {

            oContainer.find( '.no-categories-added' ).hide(); // hide the default warning message

            // jQuery seems to handle duplicate prevention but changes the order of added items
            // so check if it is added previously.

            var _sFor       = oInsert.find( 'label' ).attr( 'for' );
            if ( oContainer.find( 'label[for=' + _sFor + ']' ).length ) {
                return; // already added
            }

            // Check if it's too many categories
            var _iMaxNumCat    = parseInt( aalCategorySelection.maxNumberOfCategories );
            var _iCurrentAdded = oContainer.find( 'input[type=checkbox]' ).length;
            if ( _iCurrentAdded === _iMaxNumCat && ! bWarnedAddingCategories ) { // if not yet warned,
                bWarnedAddingCategories = true;
                alert( aalCategorySelection.translation.too_many_categories );
            }

            // Finally, add it.
            /// for cases that the category is re-added after the user once removed it.
            oInsert.find( 'input[type=checkbox]' ).attr( "checked", false );

            /// when the Added Categories item once removed, the event binding gets lost. So re-bind the click event.
            oInsert.find( 'a' ).unbind( 'click' ).click( ___handleCategoryLinkEvents );
            oContainer.append( oInsert );

            // Enable the Remove Checkbox and Create buttons
            $( '#button-remove-checked' ).removeAttr( 'disabled' );
            $( '#button-save-unit' ).removeAttr( 'disabled' );

            // Guiding arrow: Create
            if ( bShowArrows ) {
                $( '#arrow-create' ).show();
                $( '#arrow-add, #arrow-select' ).hide();
            }

        }

    /**
     * For editing category selection, the category items already rendered do ot have event binding.
     * So take care of those.
     */
    function handleExistentCategoryItems() {
        var _oContainers = $( '#added-categories, #excluding-categories' );
        _oContainers.find( 'a' ).click( ___handleCategoryLinkEvents );
    }

    /**
     * Handles the rendering and event binding for the category list.
     *
     * Called
     * - when the script loads
     * - from a click event of the reload button
     * - from a click event of the category links
     *
     * @param sURL
     */
    function handleCategoryList( sURL, bReload ) {

        if ( bProcessing ) {
            return;
        }

        // Update the current URL. This is needed for cases that a cache is loaded and the current URL should be updated to that cache URL.
        sCurrentURL = sURL;

        // Use cache if available
        if ( 'undefined' !== typeof aCaches[ sURL ] ) {
            ___renderOutputs( sURL, aCaches[ sURL ] );
            $( document ).trigger( "aalUnitPreview", [ sURL ] );
            return;
        }

        // Show a spinner
        var _oSpinner   = $( '<span class="ajax-spinner"><img src="' + aalCategorySelection.spinnerURL + '" /></span>' );
        $( '#category-select-title' ).append( _oSpinner );

        // Background processing
        bProcessing = true;
        jQuery.ajax( {
            type: 'post',
            dataType: 'json',
            url: aalCategorySelection.ajaxURL,
            // async: false,   // to prevent the user click on the other lists while loading
            // Data set to $_POSt and $_REQUEST
            data: {
                // Required
                action: aalCategorySelection.action_hook_suffix_category_list,  // WordPress action hook name which follows after `wp_ajax_`
                aal_nonce: aalCategorySelection.nonce,   // the nonce value set in template.php

                transientID: aalCategorySelection.transientID,
                postID: aalCategorySelection.postID,
                selected_url: sURL,   // the link url that the user clicks on the link of the category list. Here it is empty because of the first time of loading
                reload: ( typeof bReload !== 'undefined' && bReload ) ? 1 : 0,    // whether the request if from the Reload button. In this case, the previous cache should be cleared upon a new request.
            },
            success: function ( response ) {

                if ( ! response.success ) {

                    // For the first time loading
                    $( '.now-loading-category-list' ).replaceWith( '' );

                    // For the second time or more
                    $( '.response-error' ).remove(); // clear the previous error.
                    $( '#category-select-breadcrumb' ).after( '<p class="response-error"><span class="warning">'  + response.result + '</span></p>' );

                    // Reload button event
                    $( '.button-reload' ).click( function( event ) {
                        event.preventDefault();
                        handleCategoryList( sURL, true ); // recursive call
                        return false;
                    } );
                    return;

                }

                // At this point, the request succeeded
                ___renderOutputs(
                    sURL,
                    {
                        'breadcrumb':        $( '<span>' + response.result.breadcrumb + '</span>' ),
                        'category_list':     $( response.result.category_list ),
                        'selected_url':      response.result.selected_url,
                        'checkbox_added':    $( '<p class="added-category">' + response.result.checkbox_added + '</p>' ),
                        'checkbox_excluded': $( '<p class="excluding-category">' + response.result.checkbox_excluded + '</p>' ),
                        'category_preview':  $( '<div>' + response.result.category_preview + '</div>' ),
                        // 'unit_preview':      $( '<div>' + response.result.unit_preview + '</div>' ),
                    }
                );

                // Remove previous errors.
                $( '.response-error' ).remove();

            }, // success:
            error: function( response ) {
                console.log( 'error', response );
                $( '.now-loading-breadcrumb' ).html( '<p class="response-error">' + response.responseText + '</p>' );
            },
            complete: function() {

                _oSpinner.remove();
                bProcessing = false;

            }
        } ); // ajax - category preview and category list

        // Trigger the unit preview event
        $( document ).trigger( "aalUnitPreview", [ sURL ] );

    }  // handleCategoryList()
        /**
         * Renders the following elements:
         * - breadcrumb
         * - category list
         * - category preview
         * @param sURL
         * @param oResponse
         * @private
         */
        function ___renderOutputs( sURL, oResponse ) {

            // Set the outputs.
            $( '#category-select-breadcrumb' ).html( oResponse[ 'breadcrumb' ] )
                .append( '<span id="current-url" class="hidden">' + sURL + '</span>' );
            $( '#category-list' ).html( oResponse[ 'category_list' ] );
            $( '#category-preview' ).html( oResponse[ 'category_preview' ] );

            // Cache if not cached yet.
            if ( 'undefined' === typeof aCaches[ sURL ] ) {
                // Cache the response
                aCaches[ sURL ] = oResponse;
            }

            // (Re)Bind click event to the newly loaded link elements.
            oResponse[ 'category_list' ].find( 'a' ).unbind( 'click' ).click( ___handleCategoryLinkEvents );
            oResponse[ 'checkbox_added' ].find( 'a' ).unbind( 'click' ).click( ___handleCategoryLinkEvents );
            oResponse[ 'checkbox_excluded' ].find( 'a' ).unbind( 'click' ).click( ___handleCategoryLinkEvents );

            // Enable the `Add Category` button.
            $( '#button-add-category' ).removeAttr( 'disabled' );

            // Enable the `Add Excluding Category` button.
            $( '#button-add-excluding-category' ).attr( 'disabled', 'disabled' );
            $( '#selected-categories' ).find( 'label' ).each( function( iIndex ){
                // If the fetched response breadcrumb does not contain the substring of the string of the parsed item, skip.
                if ( -1 === oResponse[ 'breadcrumb' ].text().indexOf( $( this ).text() ) ) {
                    return true; // continue
                }
                // At this point, it is a sub-category of another category.
                $( '#button-add-excluding-category' ).removeAttr( 'disabled' );
                return false;
            } );

            // Handle guiding arrow visibilities.
            ___setArrows();

        }
            /**
             *
             * @private
             */
            function ___handleCategoryLinkEvents( event ) {
                event.preventDefault();

                // Case: when this is the breadcrumb link, the sibling checkbox should be checked.
                // There are cases of the sidebar category links and in that case, do nothing.
                var _oCheckbox = $( this ).parent().find( 'input[type=checkbox]' )
                if ( _oCheckbox.length ) {
                    _oCheckbox.attr( 'checked', ! _oCheckbox.attr( 'checked' ) ); // toggle the checked status
                }

                // Check if it is the top level (root) category.
                // Amazon has an alias URL such as `https://www.amazon.com/Best-Sellers/zgb` for `https://www.amazon.com/gp/bestsellers/`
                var _bIsRoot = 'category-list' === $( this ).parent().parent().parent().attr( 'id' );
                var _sURL    = _bIsRoot
                    ? aalCategorySelection.rootURL // use the originally passed URL from the PHP caller script.
                    : $( this ).attr( 'data-url' );

                handleCategoryList( _sURL );
                return false;
            }

    /**
     * Renders the unit preview.
     * @param event
     * @param sURL
     * @param arg2
     */
    function renderUnitPreview( event, sURL ) {

        // For unit previews
        var _oSpinner   = $( '<span class="ajax-spinner"><img src="' + aalCategorySelection.spinnerURL + '" /></span>' );
        $( '#unit-preview-title' ).append( _oSpinner );
        jQuery.ajax( {
            type: 'post',
            dataType: 'json',
            url: aalCategorySelection.ajaxURL,
            data: {
                // Required
                action: aalCategorySelection.action_hook_suffix_unit_preview,  // WordPress action hook name which follows after `wp_ajax_`
                aal_nonce: aalCategorySelection.nonce,   // the nonce value set in template.php
                transientID: aalCategorySelection.transientID,
                postID: aalCategorySelection.postID,
                urls_added: ___getURLsAdded( sURL ),
                urls_excluded: ___getURLsExcluded(),
            },
            success: function ( response ) {

                if ( ! response.success ) {

                    // @todo insert the error in the unit preview area
                    $( '#unit-preview' ).html( $( '<div>' + '<p class="response-error">' + response.result + '</p>' + '</div>' ) );
                    return;

                }

                // At this point, the request succeeded

                if ( sURL !== sCurrentURL ) {
                    // If the user clicks on another category link, do nothing.
                    return;
                }
                // Set output
                $( '#unit-preview' ).html( $( '<div>' + response.result.unit_preview + '</div>' ) );

            }, // success:
            error: function( response ) {
                // Show errors for the unit preview in the unit preview section
                $( '#unit-preview' ).html( $( '<div>' + '<p class="response-error">' + response.responseText + '</p>' + '</div>' ) );
            },
            complete: function() {
                _oSpinner.remove();
            }
        } ); // ajax - unit preview

    }
        function ___getURLsAdded( sCurrentlyDisplayingURL ) {
            var _aURLs  = [ sCurrentlyDisplayingURL ];
            $( '#added-categories' ).find( 'a' ).each( function( iIndex ) {
                _aURLs.push( $( this ).attr( 'data-url' ) );
            } );
            return _aURLs;
        }
        function ___getURLsExcluded() {
            var _aURLs  = [];
            $( '#excluding-categories' ).find( 'a' ).each( function( iIndex ) {
                _aURLs.push( $( this ).attr( 'data-url' ) );
            } );
            return _aURLs;
        }

}(jQuery));