<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * A base class of custom action link components for the plugin custom post types.
 *
 * @remark The action link needs to have the following GET request parameters
 *  - GET[ 'custom_action' ],
 *  - GET[ 'nonce' ],
 *  - GET[ 'post' ],
 *  - GET[ 'post_type' ]
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
     *
     * @param AmazonAutoLinks_AdminPageFramework_PostType $oFactory
     * @param string $sNonceKey
     * @param string $sCustomNonce
     */
    public function __construct( $oFactory, $sNonceKey='', $sCustomNonce='' ) {

        $this->_oFactory     = $oFactory;
        $this->_sNonceKey    = $sNonceKey ? $sNonceKey : $this->_sNonceKey;
        $this->_sCustomNonce = $sCustomNonce
            ? $sCustomNonce
            : (
                $this->_sCustomNonce
                    ? $this->_sCustomNonce
                    : wp_create_nonce( $this->_sNonceKey )
            );

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
        $this->_doAction( $this->getAsArray( $aPostIDs ) );
        return $sSendbackURL;
    }

    /**
     * @param array $aActionLabels
     *
     * @return      array
     * @callback    filter      bulk_actions-{screen id}
     * @since       3.7.6
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
        if ( ! wp_verify_nonce( $this->getElement( $_GET, 'nonce' ), $this->_sNonceKey ) ) {    // sanitization unnecessary
            new AmazonAutoLinks_AdminPageFramework_AdminNotice(
                __( 'The action could not be processed.', 'amazon-auto-links' ),
                array(
                    'class' => 'error',
                )
            );
            return;
        }

        $this->_doAction( absint( $_GET[ 'post' ] ) );  // sanitization done

        $this->___reload();

    }
        /**
         * @return bool
         */
        private function ___shouldDoAction() {

            $_aGET = $_GET; // sanitization unnecessary as just checking values. It's better not sanitized to let it fail checks when modified unexpectedly

            // If a WordPress action is performed, do nothing.
            if ( isset( $_aGET[ 'action' ] ) ) {
                return false;
            }
            $_bIsRequiredKeysSent = isset(
                $_aGET[ 'custom_action' ],
                $_aGET[ 'nonce' ],
                $_aGET[ 'post' ],
                $_aGET[ 'post_type' ]
            );
            if ( ! $_bIsRequiredKeysSent ) {
                return false;
            }

            return $_aGET[ 'custom_action' ] === $this->_sActionSlug;

        }

        /**
         * Reloads the page without query arguments so that the admin notice will not be shown in the next page load with other actions.
         */
        private function ___reload() {

            $_aArguments   = array(
                'post_type' => $this->_oFactory->oProp->sPostType,
            );
            $_iPaged = ( integer ) $this->getElement( $_GET, 'paged' );   // sanitization done
            if ( $_iPaged ) {
                $_aArguments[ 'paged' ] = $_iPaged;
            }
            $_sURLSendback = add_query_arg( $_aArguments, admin_url( 'edit.php' ) );
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