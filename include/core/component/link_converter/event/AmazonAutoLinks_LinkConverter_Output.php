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
 * Loads the unit option converter component.
 *
 * @since        3.8.0
 * @since        4.5.0      Renamed from `AmazonAutoLinks_LinkConverter`.
 */
class AmazonAutoLinks_LinkConverter_Output extends AmazonAutoLinks_PluginUtility {

    /**
     * @var AmazonAutoLinks_Option|AmazonAutoLinks_ToolOption
     */
    public $oOption;

    private $___sUnitPostType = '';

    /**
     * @var integer
     * @since 4.7.10
     */
    public $iHookPriority = 11;

    /**
     * @var AmazonAutoLinks_ToolOption
     * @since 4.7.0
     */
    public $oToolOption;
    /**
     * @var AmazonAutoLinks_Option
     * @since 4.7.0
     */
    public $oMainOption;

    /**
     * Sets up hooks and properties.
     * @remark Assumes that the component-enable option check is already handled.
     */
    public function __construct() {

        $this->oToolOption   = AmazonAutoLinks_ToolOption::getInstance();
        $this->oMainOption   = AmazonAutoLinks_Option::getInstance();
        $_aRawMainOptions    = $this->oMainOption->getRawOptions();
        $this->oOption       = isset( $_aRawMainOptions[ 'convert_links' ] )
            ? $this->oMainOption
            : $this->oToolOption;
        $this->iHookPriority = $this->oOption->get( array( 'convert_links', 'hook_priority' ), $this->iHookPriority );
        $this->___setHooks(
            $this->oOption->get( 'convert_links', 'filter_hooks' ),
            $this->getAsArray( $this->oOption->get( 'convert_links', 'where' ) )
        );

        $_sPreviewPostType      = trim( ( string ) $this->oMainOption->get( 'unit_preview', 'preview_post_type_slug' ) );
        $this->___sUnitPostType = $_sPreviewPostType ? $_sPreviewPostType : AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ];

    }

        /**
         * Sets up hooks
         *
         * @param string $sFilterHooks
         * @param array $aWhere
         */
        private function ___setHooks( $sFilterHooks, array $aWhere ) {

            $_sFilterHooks = str_replace( array( "\r\n", "\r" ), "\n", $sFilterHooks );
            $_aFilterHooks = explode( "\n", $_sFilterHooks );

            foreach( $aWhere as $_sHookName => $_bEnabled ) {
                if ( ! $_bEnabled ) {
                    continue;
                }
                $_aFilterHooks[] = $_sHookName;
            }
            $_aFilterHooks = array_unique( $_aFilterHooks );
            foreach( $_aFilterHooks as $_sFilterHook ) {
                if ( 'the_content' === $_sFilterHook ) {
                    add_filter( $_sFilterHook, array( $this, 'replyToFilterContentsForPosts' ), $this->iHookPriority );
                    continue;
                }
                add_filter( $_sFilterHook, array( $this, 'replyToFilterContents' ), $this->iHookPriority );
            }

        }

    /**
     * @param   string  $sHTML
     * @return  string
     */
    public function replyToFilterContentsForPosts( $sHTML ) {
        // Do nothing if it is a preview page of this plugin
        if ( get_post_type() === $this->___sUnitPostType ) {
            return $sHTML;
        }
        return $this->replyToFilterContents( $sHTML );
    }

    /**
     * @param   string  $sHTML
     * @return  string
     */
    public function replyToFilterContents( $sHTML ) {
        return preg_replace_callback( $this->getRegexPattern_URL( 'amazon' ), array( $this, 'replyToConvertLink' ), $sHTML );
    }
        /**
         * @param    array $aMatches
         * @remark   $aMatches[ 2 ] contains the url
         * @return   string
         * @callback preg_replace_callback()
         */
        public function replyToConvertLink( $aMatches ) {

            $_sParsedURL   = $aMatches[ 2 ];
            $_sLocale      = AmazonAutoLinks_Locales::getLocaleFromURL( $_sParsedURL );
            $_sAssociateID = $this->___getAssociateIDByLocale( $_sLocale );

            // If the tag is already inserted,
            if ( strlen( $_sAssociateID ) && false !== strpos( $_sParsedURL, $_sAssociateID ) ) {
                return $aMatches[ 1 ] . $_sParsedURL . $aMatches[ 4 ];
            }

            $_sURL = add_query_arg(
                array(
                    'tag' => $_sAssociateID,
                ),
                $_sParsedURL
            );
            return $aMatches[ 1 ] . $_sURL . $aMatches[ 4 ];

        }
            /**
             * @param  string $sLocale
             * @return string
             * @since  4.5.0
             */
            private function ___getAssociateIDByLocale( $sLocale ) {
                $_sAssociateID = $this->getObjectCache( __METHOD__ . $sLocale );
                if ( ! empty( $_sAssociateID ) ) {
                    return $_sAssociateID;
                }
                $_sAssociateID = $this->oMainOption->getAssociateID( $sLocale );
                $this->setObjectCache( __METHOD__ . $sLocale, $_sAssociateID );
                return $_sAssociateID;
            }

}