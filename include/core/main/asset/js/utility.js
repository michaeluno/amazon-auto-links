/**
 * @name Utility
 * @version 1.0.0
 */
(function($){

    aalCopyToClipboard = function( elem ) {
          // create hidden text element, if it doesn't already exist
        var targetId = "_hiddenCopyText_";
        var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
        var origSelectionStart, origSelectionEnd;
        if (isInput) {
            // can just use the original source element for the selection and copy
            target = elem;
            origSelectionStart = elem.selectionStart;
            origSelectionEnd = elem.selectionEnd;
        } else {
            // must use a temporary form element for the selection and copy
            target = document.getElementById(targetId);
            if (!target) {
                var target = document.createElement("textarea");
                target.style.position = "absolute";
                target.style.left = "-9999px";
                target.style.top = "0";
                target.id = targetId;
                document.body.appendChild(target);
            }
            target.textContent = elem.textContent;
        }
        // select the content
        var currentFocus = document.activeElement;
        target.focus();
        target.setSelectionRange(0, target.value.length);

        // copy the selection
        var succeed;
        try {
              succeed = document.execCommand("copy");
        } catch(e) {
            succeed = false;
        }
        // restore original focus
        if (currentFocus && typeof currentFocus.focus === "function") {
            currentFocus.focus();
        }

        if (isInput) {
            // restore prior selection
            elem.setSelectionRange(origSelectionStart, origSelectionEnd);
        } else {
            // clear temporary content
            target.textContent = "";
        }
        return succeed;
    }

    /**
     * Cancels callback within the set time span, which is 200 milliseconds by default.
     * @param func
     * @param timeout
     * @returns {function(): void}
     */
    debounce = function ( func , timeout ) {
       var timeoutID , timeout = timeout || 200;
       return function () {
          var scope = this , args = arguments;
          clearTimeout( timeoutID );
          timeoutID = setTimeout( function () {
              func.apply( scope , Array.prototype.slice.call( args ) );
          } , timeout );
       }
    }
    
}(jQuery));        