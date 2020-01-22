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
 * Loads the unit option converter component.
 *
 * @package      Amazon Auto Links
 * @since        3.8.0
 */
class AmazonAutoLinks_LinkConverter extends AmazonAutoLinks_PluginUtility {

    private $___sUnitPostType = '';
    private $___sAssociateID  = '';

    public function __construct() {

        $_oOption  = AmazonAutoLinks_Option::getInstance();

        $_bEnabled = $_oOption->get( 'convert_links', 'enabled' );
        if ( ! $_bEnabled ) {
            return;
        }
        $this->___sAssociateID = trim( ( string ) $_oOption->get( 'unit_default', 'associate_id' ) );
        if ( ! $this->___sAssociateID ) {
            return;
        }

        $this->___setHooks( $_oOption );

        // Properties
        $_sPreviewPostType      = trim( ( string ) $_oOption->get( 'unit_preview', 'preview_post_type_slug' ) );
        $this->___sUnitPostType = $_sPreviewPostType
            ? $_sPreviewPostType
            : AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ];


    }
        /**
         * Sets up hooks
         */
        private function ___setHooks( $_oOption ) {
            // Set hooks
            $_sFilterHooks = $_oOption->get( 'convert_links', 'filter_hooks' );
            $_sFilterHooks = str_replace( array( "\r\n", "\r" ), "\n", $_sFilterHooks );
            $_aFilterHooks = explode( "\n", $_sFilterHooks );

            $_aWhere = $this->getAsArray( $_oOption->get( 'convert_links', 'where' ) );
            foreach( $_aWhere as $_sHookName => $_bEnabled ) {
                if ( ! $_bEnabled ) {
                    continue;
                }
                $_aFilterHooks[] = $_sHookName;
            }
            $_aFilterHooks = array_unique( $_aFilterHooks );
            foreach( $_aFilterHooks as $_sFilterHook ) {
                if ( 'the_content' === $_sFilterHook ) {
                    add_filter( $_sFilterHook, array( $this, 'replyToFilterContentsForPosts' ), 11 );
                    continue;
                }
                add_filter( $_sFilterHook, array( $this, 'replyToFilterContents' ), 11 );
            }
        }

    /**
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
     * @return  string
     */
    public function replyToFilterContents( $sHTML ) {
        return preg_replace_callback(
            $this->___getPattern(),
            array( $this, 'replyToConvertLink' ),
            $sHTML
        );

    }

        /**
         * @param array $aMatches
         * @remark $aMatches[ 2 ] contains the url
         * @return string
         * @callback    preg_replace_callback()
         */
        public function replyToConvertLink( $aMatches ) {

            // If the tag is already inserted,
            if ( false !== strpos( $aMatches[ 2 ], $this->___sAssociateID ) ) {
                return $aMatches[ 1 ]
                        . $aMatches[ 2 ]
                    . $aMatches[ 4 ];
            }

            $_sURL = add_query_arg(
                array(
                    'tag' => $this->___sAssociateID,
                ),
                $aMatches[ 2 ]
            );
            return $aMatches[ 1 ]
                    . $_sURL
                . $aMatches[ 4 ];
        }
        /**
         * @return  boolean
         * @deprecated      kept for debugging
         */
        private function ___hasAmazonLinks( $sHTML ) {
            $_sPattern  = '\shref\=["\']'; // src attribute
            $_sPattern .= "(https?\:\/\/)"; // SCHEME
            $_sPattern .= ".+?amazon\.";
            if ( preg_match( "/" . $_sPattern . "/i", $sHTML ) ) {
                return true;
            }
            return false;
        }

        private function ___getPattern() {

            $_sPatternHref  = "("; // first element open
                $_sPatternHref .= "<"; // 1 start of the tag
                $_sPatternHref .= "\s*"; // 2 zero or more whitespace
                $_sPatternHref .= "a"; // 3 the a of the tag itself
                $_sPatternHref .= "\s+"; // 4 one or more whitespace
                $_sPatternHref .= "[^>]*"; // 5 zero or more of any character that is _not_ the end of the tag
                $_sPatternHref .= "href"; // 6 the href bit of the tag
                $_sPatternHref .= "\s*"; // 7 zero or more whitespace
                $_sPatternHref .= "="; // 8 the = of the tag
                $_sPatternHref .= "\s*"; // 9 zero or more whitespace
                $_sPatternHref .= '["\']?'; // 10 none or one of " or ' opening quote
            $_sPatternHref .= ')'; // first element close
            $_sPatternHref .= "("; // second element open
                $_sPatternHref .= 'https?:\/\/(www\.)?amazon\.[^"\' >]+';   // URL
            $_sPatternHref .= ")"; // second elementclose
            $_sPatternHref .= "("; // fourth element
                $_sPatternHref .= '["\' >]'; // 14 closing chartacters of the bit we want to capture
            $_sPatternHref .= ')'; // fourth element close

            $_sNeedle  = "/"; // regex start delimiter
            $_sNeedle .= $_sPatternHref;
            $_sNeedle .= "/"; // regex end delimiter
            $_sNeedle .= "i"; // Pattern Modifier - makes regex case insensative
            $_sNeedle .= "s"; // Pattern Modifier - makes a dot metacharater in the pattern
            // match all characters, including newlines
            $_sNeedle .= "U"; // Pattern Modifier - makes the regex ungready
            return $_sNeedle;
        }

}