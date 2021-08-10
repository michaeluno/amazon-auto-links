/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * @name Chart Loader
 * @version 1.0.1
 */
(function($){

    var _oCanvasContainer;
    var _oChartElement;
    var _oChart;

    $( document ).ready( function() {

        if ( 'undefined' === typeof aalChartJSLoader ) {
            console.log( 'Amazon Auto Links', 'Chart Loader', 'The script dat is not passed.' );
            return;
        }
        if ( aalChartJSLoader.debugMode ) {
            console.log( 'Amazon Auto Links', 'Chart Loader', aalChartJSLoader );
        }

        _oChartElement = $( '#' + aalChartJSLoader.chartID );
        _oChart        = getChartCreated( _oChartElement, aalChartJSLoader.logX, aalChartJSLoader.logY, aalChartJSLoader.total );

        $( '.chart_option_update' ).click( updatedChartOptions );

        // When the browser shrinks, the canvas does not resize by itself, so cover that.
        _oCanvasContainer = _oChartElement.parent();
        $( window ).resize( debounce( resizeCanvasContainer ) );  // window resize

    }); // document ready

    function updatedChartOptions( event ) {
        event.preventDefault();
        var _isStartTime = $( 'input.from' ).val();
        var _isEndTime   = $( 'input.to' ).val();
        var _oThis       = $( this );
        $.ajax( {
            type: "post",
            dataType: 'json',
            async: true,
            cache: true,
            url: aalChartJSLoader.ajaxURL,
            data: {
                action: aalChartJSLoader.actionHookSuffix,   // WordPress action hook name which follows after `wp_ajax_`
                aal_nonce: aalChartJSLoader.nonce,   // the nonce value set in template.php
                locale: $( '.locales select' ).val(),
                startTime: _isStartTime,
                endTime: _isEndTime,
            },

            // Custom properties
            spinnerImage: $( '<img class="aal-spinner" src="' + aalChartJSLoader.spinnerURL + '" />' ),

            // Callbacks
            beforeSend: function() {
                this.spinnerImage.css( { 'vertical-align': 'middle', 'display': 'inline-block', 'height': 'auto', 'margin-left': '0.5em' } );
                _oThis.closest( '.amazon-auto-links-input-label-container' ).append( this.spinnerImage );
                    // .closest( '.amazon-auto-links-fieldrow' )
                    // .find( '.amazon-auto-links-field-title' )
                    // .append( this.spinnerImage );
            },
            success: function ( response ) {

                if ( ! response.success ) {
                    return;
                }
                _oChart.destroy();
                _oChart = getChartCreated(
                    _oChartElement,
                    response.result.date,
                    response.result.count,
                    response.result.total,
                );
                // _debugLog( response );
                // if ( response.success ) {
                //     $( 'select[name=unit_default\\[language\\]]' ).html( response.result.language );
                //     $( 'select[name=unit_default\\[preferred_currency\\]]' ).html( response.result.currency );
                // } else {
                //     _debugLog( 'Ajax response error', response );
                // }
            },
            error: function( response ) {
                // _debugLog( 'Ajax response error', response );
            },
            complete: function() {

                $( '.aal-spinner' ).remove();

            }
        } ); // ajax()
        return false; // prevent click
    }

    /**
     * Actually, resize the canvas container is enough.
     * Called when the browser window is resized.
     * @param event
     */
    function resizeCanvasContainer( event ) {

        var _iOverlap = _getSideMetaBoxOverlapWidth();
        if ( ! _iOverlap ) {
            _oChart.canvas.parentNode.style.width = 'auto';
            return;
        }
        var _iNewContainerWidth = _oCanvasContainer.width() - ( _iOverlap + 10 );
        _oCanvasContainer.width( _iNewContainerWidth );

    }
        function _getSideMetaBoxOverlapWidth() {

            var _oSideMetaBox    = $( "#side-sortables" );
            if ( ! _oSideMetaBox.length ) {
                return 0;
            }

            var _iScrollLeft            = $( window ).scrollLeft();
            var _aOffsetSideMetaBox     = _oSideMetaBox.offset();
            var _iSideMetaBoxPosX       = _aOffsetSideMetaBox.left - _iScrollLeft;
            var _aOffsetCanvasContainer = _oCanvasContainer.offset();
            var _iCanvasContainerPosX2  = _aOffsetCanvasContainer.left + _oCanvasContainer.width() - _iScrollLeft;

            // For one column layout,
            if ( _iSideMetaBoxPosX < 480 ) {
                return 0;
            }
            return _iCanvasContainerPosX2 > _iSideMetaBoxPosX
                ? Math.ceil( _iCanvasContainerPosX2 - _iSideMetaBoxPosX )
                : 0;

        }


    function getChartCreated( oChartElement, aX, aY, iTotal ) {

        // Store data for export
        var _oChartContainer = oChartElement.parent();
        _oChartContainer.after( "<input type='hidden' name='chart-x' value='" + aX.join( '|' ) + "'/>" );
        _oChartContainer.after( "<input type='hidden' name='chart-y' value='" + aY.join( '|' ) + "'/>" );

        return new Chart(
            oChartElement,
            {
                "type": "line",
                "data": {
                    "labels": aX,
                    "datasets": [{
                        "label": aalChartJSLoader.labels.counts + ' (' + aalChartJSLoader.labels.total + ': ' + iTotal + ')',
                        "data": aY,
                        "fill": false,
                        "borderColor": "rgb(75, 192, 192)",
                        "lineTension": 0
                    }]
                },
                "options": {

                    // Dynamic resizing
                    responsive: true,
                    maintainAspectRatio: true,

                    // Disabling the label
                    legend: {
                        display: true,
                    },
                    tooltips: {
                        callbacks: {
                            title: function( tooltipItems, data ) {
                                return data.labels[ tooltipItems[ 0 ].index ];
                            },
                            label: function( tooltipItem, data ) {
                                return aalChartJSLoader.labels.count + ': ' + tooltipItem.yLabel;
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: 'day',
                                displayFormats: {
                                    day: 'YYYY-MMM-D',  // the callback function _formatDateLabel() receives values with this format
                                },
                                tooltipFormat: 'll',
                                parser: function ( utcMoment ) {
                                    return moment( utcMoment ).utcOffset( aalChartJSLoader.GMTOffset ); // '+0900'
                                }
                            },
                            ticks: {
                                callback: _formatDateLabel,
                                source: 'labels'
                            },
                            scaleLabel: {
                                display:     true,
                                labelString: aalChartJSLoader.labels.dates
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display:     true,
                                labelString: aalChartJSLoader.labels.counts
                            },
                            ticks: {
                                suggestedMin: 0,
                                stepSize: 1
                            }
                        }]
                    }

                }
            }
        );
    }

    var _sYear  = null; // avoid empty string '' to match accidentally
    var _sMonth = null;
    // var _bInitialCall = true;

    /**
     * Formats the date labels of the X axis.
     * Omits repeated year and months labels.
     * @param sValue
     * @param iIndex
     * @param values
     * @returns {string|*}
     * @private
     * @remark The site date format stored in the WordPress site database is not supported as it is of PHP.
     * To implement that, see https://stackoverflow.com/questions/57279831/function-to-convert-php-date-format-to-javascript-date-format-not-the-date-itse
     */
    function _formatDateLabel( sValue, iIndex, values ) {

        // Chart.js passes unusable item for the very first or second call. In that case, cancel formatting.
        if ( 0 === iIndex && 1 >= values.length ) {
            // Initialize the property for the case that the chart is updated with Ajax.
            _sYear  = null;
            _sMonth = null;
            return sValue;
        }

        var _aDateParts = sValue.split( '-' ); // set in the format, scales.xAxes.time.displayFormats
        var _sThisYear  = _sYear  === _aDateParts[ 0 ] ? '' : _aDateParts[ 0 ];
        var _sThisMonth = _sMonth === _aDateParts[ 1 ] ? '' : _aDateParts[ 1 ];
        var _sThisDate  = _aDateParts[ 2 ];

        // If the x-axis is too long, omit some date labels.
        sValue = __getLabelOmittedByLength( sValue, iIndex, values, _sThisYear, _sThisMonth, _sThisDate );
        if ( ! sValue ) {
            return sValue;
        }

        // Store them as previous values to be checked in next calls.
        _sYear  = _aDateParts[ 0 ];
        _sMonth = _aDateParts[ 1 ];

        // Return the formatted value
        var _sLabel = _sThisYear + ' ' + _sThisMonth + ' ' + _sThisDate;
        return _sLabel.trim();
    }
        function __getLabelOmittedByLength( sValue, iIndex, values, sYear, sMonth, sDate ) {
            if ( 30 > values.length ) {
                return sValue;
            }
            // The very first item
            if ( 0 === iIndex ) {
                return sValue;
            }
            // The very last item
            if ( values.length === iIndex + 1 ) {
                return sValue;
            }

            // Give a span
            var _iSpan = Math.round( values.length / 10 );
            if ( ( iIndex % _iSpan ) ) {
                return '';
            }
            return sValue;
        }
}(jQuery));