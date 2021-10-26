<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles the survey permission question.
 * @since   4.7.3
 */
class AmazonAutoLinks_Opt_Event_Ajax_PermissionSurvey extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookSuffix = 'aal_action_opt_survey_permission';

    protected $_bGuest = false;

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.7.3
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'allowed' => ( boolean ) $this->getElement( $aPost, 'allowed' ),
        );
    }

    /**
     * @return string
     * @throws Exception
     * @param  array     $aPost Unused at the moment.
     */
    protected function _getResponse( array $aPost ) {

        $_bAllowed = $this->getElement( $aPost, 'allowed' );
        $_iUserID  = get_current_user_id();
        $_bUpdated = $_bAllowed
            ? ( boolean ) update_user_meta( $_iUserID, 'aal_surveys', time() )
            : ( boolean ) update_user_meta( $_iUserID, 'aal_never_ask_surveys', true );
        if ( ! $_bUpdated ) {
            throw new Exception( 'Failed to update a user meta. User ID: ' . $_iUserID );
        }
        return $_bAllowed
            ? __( 'Thank you!', 'amazon-auto-links' )
            : __( 'All right.', 'amazon-auto-links' );

    }

}