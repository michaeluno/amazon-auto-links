/**
 * @name Opt Survey Plugin Deactivation
 * @version 1.0.0
 */
(function($){

  $( document ).ready( function() {
    if ( 'undefined' === typeof aalSurveyPluginDeactivation ) {
      return;
    }

    $( '#deactivate-amazon-auto-links' ).on( 'click', function( event ) {
      event.preventDefault();
      var _oBody = $( 'body' );
      var _self = this;
      var _oSpinner = $( '<img class="ajax-spinner aal-survey-spinner" src="' + aalSurveyPluginDeactivation.spinnerURL + '" alt="' + aalSurveyPluginDeactivation.label.loading + '" />' );

      // Tooltip Modal
      /// background
      var _oBackground = $( '<div class="tooltip-background"></div>   ' );
      _oBackground.css({
        position: 'fixed',
        width: '100%',
        height: '100%',
        'z-index': 1,
        left: 0,
        top: 0,
        overflow: 'auto',
            'background-color': 'rgba(0,0,0,0.4)',
      });
      _oBody.append( _oBackground );
        /// Tooltip window
        $( this ).aalSurveyTooltip({
          width: 480,
          oneOff: true,
          autoClose: false,
          noArrow: true,
          shown: true,
          position: {
            within: _oBody,
          },
          content: $( '#aal-survey-plugin-deactivation' ).html(),
          close: function(){
            _oBackground.remove();
            _oSpinner.remove();
            $( _self ).trigger( 'aal_survey_tooltip_closed', [] );
            $( _self ).aalPointer( 'destroy' );
          },
        });

        var _oTooltipContainer = $( '.amazon-auto-links-tooltip-survey-balloon' );

        // Closing handling
        _oBody.on( 'aal_survey_tooltip_closed', function( event ) {
            _oTooltipContainer.off( 'click', 'label' );
            _oTooltipContainer.off( 'click', 'button[data-action=cancel]' );
            _oTooltipContainer.off( 'click', 'button[data-action=deactivate]' );
            _oTooltipContainer.off( 'click', 'button[data-action=submit]' );
            _oTooltipContainer.off( 'change', 'input[type=radio]' );
            _oTooltipContainer.off( 'input', '*[data-required]' );
            _oTooltipContainer.off( 'focusout', '*[data-required]' );
            _oBody.off( 'aal_survey_tooltip_closed' );
        } );

        // Somehow clicking on the label tags closes the tooltip. So prevent the default behaviour and simulate it.
        _oTooltipContainer.on( 'click', 'label', function( e ) {
          e.preventDefault();
          $( this ).find( 'input[type=radio]' ).prop('checked', true )
            .trigger( 'change' );
          $( this ).find( 'input[type=checkbox]' ).each( function(){
            $( this ).prop( 'checked', ! $( this ).prop( 'checked' ) )
              .trigger( 'change' );
          } );
          $( this ).find( 'input[type=text],textarea' ).focus();
        } );

        // Button click handling
        _oTooltipContainer.on( 'click', 'button[data-action=cancel]', function( e ){
          e.preventDefault();
          $( _self ).aalPointer( 'close' );
        } );
        _oTooltipContainer.on( 'click', 'button[data-action=deactivate]', function( e ){
          e.preventDefault();
          $( _self ).aalPointer( 'close' );
          window.location.href = $( _self ).attr( 'href' );
        } );

        // Form Fields Handling
        _oTooltipContainer.on( 'change', 'input[type=radio]', function( e ) {
            // Swap the button
            _oTooltipContainer.find( 'button[data-action=deactivate]' ).hide();
            _oTooltipContainer.find( 'button[data-action=submit]' )
                .addClass( 'button-primary' )
                .show();

            // Anonymous feedback field
            _oTooltipContainer.find( '.extra-field' ).show();

            // Show/hide sub-fields
            $( this ).closest( '.wp-pointer-content' ).find( '.sub-field' ).hide()
                .find( '*[data-required]' ).removeAttr( 'required' );
            var _oSubRequired = $( this ).closest( 'p' ).find( '.sub-field' ).show()
                .find( '*[data-required]' ).attr( 'required', 'required' );

            if ( _oSubRequired.length && ! _oSubRequired.val() ) {
                _oTooltipContainer.find( 'button[data-action=submit]' ).attr( 'disabled', 'disabled' );
            } else {
                _oTooltipContainer.find( 'button[data-action=submit]' ).removeAttr( 'disabled' );
            }

        } );
        /// When the required field is filled, allow the submit button
        _oTooltipContainer.on( 'input', '*[data-required]', function( e ) {
          if ( $.trim( $( this ).val() ) ) {
            _oTooltipContainer.find( 'button[data-action=submit]' ).removeAttr( 'disabled' );
          } else {
            _oTooltipContainer.find( 'button[data-action=submit]' ).attr( 'disabled', 'disabled' );
          }
        } );
        /// When the user leaves a required field, mark the label as red
        _oTooltipContainer.on( 'focusout', '*[data-required]', function( e ){
          if ( ! $.trim( $( this ).val() ) ) {
            $( this ).closest( 'label' ).css( { color: '#d63638' } );
          }
        } );

        // Form submit handling
        _oTooltipContainer.on( 'click', 'button[data-action=submit]', function( e ){
          e.preventDefault();

          // Check required fields
          var _oRequired      = _oTooltipContainer.find( 'input[type=text][required=required],textarea[required=required],input[type=checkbox][required=required]' );
          var _oRequiredLabel = _oRequired.closest( 'label' );
          if ( _oRequiredLabel.length && ! $.trim( _oRequired.val() ) ) {
            _oRequiredLabel.css( 'color', '#d63638' );
            if ( ! _oRequiredLabel.find( '.dashicons-warning' ).length ) {
              _oRequiredLabel.prepend( '<span class="dashicons dashicons-warning"></span>' );
            }
            _oRequired.focus();
            _oTooltipContainer.find( 'button[data-action=submit]' ).attr( 'disabled', 'disabled' );
            return false;
          }

          // Send
          var _oIcon = '<span class="icon-smile dashicons dashicons-smiley"></span>';
          _oTooltipContainer.find( '.tooltip-footer .right' ).prepend( _oIcon );
          _oTooltipContainer.find( '.tooltip-footer .right' ).prepend( _oSpinner );
          $.ajax( {
            type: 'post',
            dataType: 'json',
            url: aalSurveyPluginDeactivation.ajaxURL,
            data: {
              action: aalSurveyPluginDeactivation.actionHookSuffix,   // WordPress action hook name which follows after `wp_ajax_`
              aal_nonce: aalSurveyPluginDeactivation.nonce,
              form: _oTooltipContainer.find( 'form' ).serializeArray(),
            },
            success: function ( response ) {},
            error: function( response ) {},
            complete: function() {
              _oSpinner.remove();
              $( _self ).aalPointer( 'close' );
              window.location.href = $( _self ).attr( 'href' );
            }
          } ); // ajax

        } );

      return false;
    });

  });   // document.ready()
}(jQuery));