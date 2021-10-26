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
 * Enables the Web Page Dumper option
 *
 * @package      Auto Amazon Links
 * @since        4.7.3
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Ajax_Enable extends AmazonAutoLinks_AjaxEvent_Base {

    /**
     * @var string 
     * @since 4.7.3
     */
    protected $_sActionHookSuffix = 'aal_action_web_page_dumper_enable';
    protected $_bGuest            = false;
    
    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.7.3
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'enable' => ( boolean ) $this->getElement( $aPost, array( 'enable' ) ),
        );
    }

    /**
     * @return string
     * @param  array  $aPost Contains the sanitized `enable` element.
     * @since  4.7.3
     * @throws Exception
     */
    protected function _getResponse( array $aPost ) {

        $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
        $_oToolOption->set( array( 'web_page_dumper', 'enable' ), $aPost[ 'enable' ] );
        $_bEnabled    = $_oToolOption->save();
        if ( ! $_bEnabled ) {
            throw new Exception( __( 'Failed to update the option.', 'amazon-auto-links' ) );
        }
        return __( 'Enabled', 'amazon-auto-links' );

    }

}