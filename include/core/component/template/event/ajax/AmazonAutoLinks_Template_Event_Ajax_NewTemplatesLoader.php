<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Loads new available templates.
 *
 * @since        4.6.18
 */
class AmazonAutoLinks_Template_Event_Ajax_NewTemplatesLoader extends AmazonAutoLinks_AjaxEvent_Base {

    /**
     * The part after `wp_ajax_` or `wp_ajax_nopriv_`.
     * @var string
     */
    protected $_sActionHookSuffix = 'aal_action_get_new_templates';
    protected $_bLoggedIn         = true;
    protected $_bGuest            = false;

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'disableAutoload' => ( boolean ) $this->getElement( $aPost, array( 'disableAutoload' ) ),
        );
    }

    /**
     * @return string|boolean
     * @throws Exception        Throws a string value of an error message.
     * @param  array $aPost     Unused at the moment.
     */
    protected function _getResponse( array $aPost ) {

        if ( $aPost[ 'disableAutoload' ] ) {
            update_user_meta( get_current_user_id(), 'aal_load_new_templates', false );
            return __( 'OK', 'amazon-auto-links' );
        }

        update_user_meta( get_current_user_id(), 'aal_load_new_templates', true );

        $_oRSS = new AmazonAutoLinks_RSSClient(
            'https://feeds.feedburner.com/AmazonAutoLinksTemplates'
        );
        $_aItems = $_oRSS->get();
        if ( empty( $_aItems ) ) {
            return "<p>"
                    . __( 'No templates found.', 'amazon-auto-links' )
                . "</p>";
        }

        // Format the description element.
        foreach( $_aItems as &$_aItem ) {
            $_aItem = array(
                'description' => $this->___getFormattedDescription( $_aItem ),            
            ) + $_aItem;
        }
        
        // Get the column output.
        $_oColumn = new AmazonAutoLinks_Column(
            $_aItems, // data
            3,  // number of columns
            'amazon_auto_links_' // selector prefix
        );
        return $_oColumn->get();        

    }
        /**
         * @return      string
         */
        private function ___getFormattedDescription( $aItem ) {
            $_aAttributes = array(
                'href'      => $aItem[ 'link' ],
                'rel'       => 'nofollow',
                'class'     => 'button button-secondary',
                'target'    => '_blank',
                'title'     => esc_attr( __( 'Get it Now', 'amazon-auto-links' ) ),
            );
            return "<h4>" . $aItem[ 'title' ] . "</h4>"
                . $aItem[ 'description' ]
                . "<div class='get-now'>"
                    . "<a " . AmazonAutoLinks_WPUtility::generateAttributes( $_aAttributes ) . ">"
                        . __( 'Get it Now', 'amazon-auto-links' )
                    . "</a>"
               . "</div>";
        }    
}