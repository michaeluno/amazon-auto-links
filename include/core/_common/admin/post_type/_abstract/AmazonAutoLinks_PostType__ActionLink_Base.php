<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * A base class of custom action link components for the plugin custom post types.
 *
 * @remark The action link needs to have the following GET request parameters
 *  - $_GET[ 'custom_action' ],
 *  - $_GET[ 'nonce' ],
 *  - $_GET[ 'post' ],
 *  - $_GET[ 'post_type' ]
 *
 * @since       3.7.8
 */
class AmazonAutoLinks_PostType__ActionLink_Base extends AmazonAutoLinks_PluginUtility {

    protected $_oFactory;

    protected $_sCustomNonce = '';
    protected $_sNonceKey    = '';

    protected $_sActionSlug  = '';

    /**
     * @var bool
     * @since   3.7.6
     */
    protected $_bAddBulkAction = true;

    protected $_aDisabledPostStatuses = array( 'trash' );

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sNonceKey, $sCustomNonce ) {

        $this->_oFactory     = $oFactory;
        $this->_sNonceKey    = $sNonceKey;
        $this->_sCustomNonce = $sCustomNonce;

        if ( ! in_array( $this->getElement( $_REQUEST, 'post_status' ), $this->_aDisabledPostStatuses ) ) {
            add_filter(
                'action_links_' . $this->_oFactory->oProp->sPostType,
                array( $this, 'replyToModifyActionLinks' ),
                10,
                2
            );

            add_action( 'current_screen', array( $this, 'replyToSetHooks' ) );
        }

        $this->_construct();

        $this->___doCustomActions();

    }

        /**
         * @since   3.7.6
         * @callback    action      current_screen
         */
        public function replyToSetHooks() {
            if ( ! $this->_bAddBulkAction ) {
                return;
            }
            $_sPostTypeSlug = $this->_oFactory->oProp->sPostType;
            $_sScreenID = get_current_screen()->id;
            if ( "edit-{$_sPostTypeSlug}" !== $_sScreenID ) {
                return;
            }
            add_filter(
                'bulk_actions-' . $_sScreenID,
                array( $this, 'replyToAddBulkAction' )
            );
            add_filter(
                'handle_bulk_actions-' . $_sScreenID,
                array( $this, 'replyToFilterSendbackURL' ),
                10,
                3
            );
        }

    /**
     * @param $sSendbackURL
     * @param $sDoAction
     * @param $aPostIDs
     *
     * @return mixed
     * @callback    filter      handle_bulk_actions-{screen id}
     */
    public function replyToFilterSendbackURL( $sSendbackURL, $sDoAction, $aPostIDs ) {
        if ( $sDoAction !== $this->_sActionSlug ) {
            return $sSendbackURL;
        }
        $aPostIDs = is_array( $aPostIDs )
            ? $aPostIDs
            : array( $aPostIDs );
        $this->_doAction( $aPostIDs );
        return $sSendbackURL;
    }

    /**
     * @param array $aActionLabels
     *
     * @return      array
     * @callback    filter      bulk_actions-{screen id}
     * @sicne       3.7.6
     */
    public function replyToAddBulkAction( $aActionLabels ) {
        $aActionLabels[ $this->_sActionSlug ] = $this->_getActionLabel();
        return $aActionLabels;
    }

    /**
     * @return string
     * @since   3.7.6
     */
    protected function _getActionLabel() {
        return 'THIS_ACTION_LABEL';
    }

    /**
     * @since       3.1.0
     * @since       3.5.0       Moved from `AmazonAutoLinks_PostType_Unit_Action`.
     */
    private function ___doCustomActions() {

        if ( ! $this->___shouldDoAction() ) {
            return;
        }
        $_sNonce = $this->getElement( $_GET, 'nonce' );
        if ( ! wp_verify_nonce( $_sNonce, $this->_sNonceKey ) ) {
            new AmazonAutoLinks_AdminPageFramework_AdminNotice(
                __( 'The action could not be processed.', 'amazon-auto-links' ),
                array(
                    'class' => 'error',
                )
            );
            return;
        }

        $this->_doAction( $_GET[ 'post' ] );

        $this->___reload();

    }
        /**
         * @return bool
         */
        private function ___shouldDoAction() {

            // If a WordPress action is performed, do nothing.
            if ( isset( $_GET[ 'action' ] ) ) {
                return false;
            }
            $_bIsRequiredKeysSent = isset(
                $_GET[ 'custom_action' ],
                $_GET[ 'nonce' ],
                $_GET[ 'post' ],
                $_GET[ 'post_type' ]
            );
            if ( ! $_bIsRequiredKeysSent ) {
                return false;
            }

            return $_GET[ 'custom_action' ] === $this->_sActionSlug;

        }

        /**
         * Reloads the page without query arguments so that the admin notice will not be shown in the next page load with other actions.
         */
        private function ___reload() {

            $_sURLSendback = add_query_arg(
                array(
                    'post_type' => $this->_oFactory->oProp->sPostType,
                ),
                admin_url( 'edit.php' )
            );
            wp_safe_redirect( $_sURLSendback );
            exit();

        }

    /**
     * Called when the action should be performed.
     * @param   array|integer       Post ID(s) that the action should be performed.
     */
    protected function _doAction( $aiPostID ) {}

    /**
     * @param           $aActionLinks
     * @param           $oPost
     * @callback        add_filter      action_links_{post type slug}
     * @return          array
     */
    public function replyToModifyActionLinks( $aActionLinks, $oPost ) {
        $_sLink = $this->_getActionLink( $oPost );
        if ( $_sLink ) {
            $aActionLinks[ $this->_sActionSlug ] = $_sLink;
        }
        return $aActionLinks;
    }

    /**
     * @return string
     * @since   3.7.6
     */
    protected function _getActionLink( $oPost ) {
        return '';
    }

    /**
     * User constructor
     * @since 3.7.6
     */
    protected function _construct() {}

}