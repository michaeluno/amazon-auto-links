<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Sets the no outer container argument within the output function call.
 *
 * This removes the <div class="amazon-auto-links">...</div> wrapper from the output.
 *
 * The `_no_outer_container` output argument is checked at the final part of the output function call
 * but outside the method that calls the template. So within the template, there is no means to change the argument value.
 * This class allows templates to change that value using the filter hook.
 *
 *
 * ### Usage
 * Just instantiate this class within the template.
 *
 * @since       4.1.0
 */
class AmazonAutoLinks_TemplateUtility_NoOuterContainer {
    public function __construct() {
        add_filter( 'aal_filter_output_is_without_outer_container', array( $this, 'replyToSetNoOuterContainer' ), 10, 2 );
    }
    /**
     * @callback    filter      aal_filter_output_is_without_outer_container
     */
    public function replyToSetNoOuterContainer() {
        remove_filter( 'aal_filter_output_is_without_outer_container', array( $this, 'replyToSetNoOuterContainer' ), 10 );
        return true;
    }
}