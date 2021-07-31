/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 * @name Pointer Tooltip jQuery Widget
 * @description Extends the WordPress pointer tooltip widget to have additional features.
 * @version 1.0.0
 */
(function($){

	var zindex = 9999;

    // Extend wp.pointer jQuery widget
    $.widget( 'aal.aalPointer', $.wp.pointer, {
        /**
         * Overrides the reposition() method.
         * Just adds the `within` option to the ui position() arguments
         * so that the tooltip does not exceed the container
         */
		reposition: function() {
			var position;

			if ( this.options.disabled )
				return;

			position = this._processPosition( this.options.position );

			// Reposition pointer.
			this.pointer.css({
				top: 0,
				left: 0,
				zIndex: zindex++ // Increment the z-index so that it shows above other opened pointers.
			});
			this.pointer.fadeIn( this.options.fadeIn );
            var _optionsPosition = $.extend(
                {
                    of: this.element,
                    collision: 'fit none'
			    },
                // Add support for the jQuery.ui.position() arguments to be passed through the `position` argument.
                'string' === typeof this.options.position ? {} : this.options.position,
                position
            );
			this.pointer.position( _optionsPosition ); // The object comes before this.options.position so the user can override position.of.
			this.repoint();
		}
    } );

}(jQuery));