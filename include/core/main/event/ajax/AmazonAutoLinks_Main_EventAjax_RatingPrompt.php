<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles the rating prompt display.
 * @since   4.5.6
 */
class AmazonAutoLinks_Main_EventAjax_RatingPrompt extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookSuffix = 'aal_action_rating_prompt';

    protected $_bGuest = false;

    /**
     * @param array $aPost
     *
     * @return string
     * @throws Exception
     */
    protected function _getResponse( array $aPost ) {

        $_bUpdated = ( boolean ) update_user_meta( get_current_user_id(), 'aal_rated', time() );
        if ( ! $_bUpdated ) {
            throw new Exception( 'Failed to update a user meta. User ID: ' . get_current_user_id() );
        }
        return 'OK';

    }

}