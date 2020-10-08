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
 * A unit test utility class.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.4
*/
class AmazonAutoLinks_Run_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * @param  string  $sHTML
     * @param  array   $aRemoveTags     HTML tags to remove.
     * @param  boolean $bRemoveComment  Whether to remove HTML comments.
     * @return string
     * @since  4.3.4
     */
    static public function getHTMLBody( $sHTML, $aRemoveTags=array( 'script', 'style' ), $bRemoveComment=true ) {

        $_oDOMHelper = new AmazonAutoLinks_DOM;
        $_oDoc       = $_oDOMHelper->loadDOMFromHTML( $sHTML );
        $_oXPath     = new DOMXPath( $_oDoc );
        $_oDOMHelper->removeTags( $_oDoc, $aRemoveTags );
        if ( $bRemoveComment ) {
            $_oDOMHelper->removeComments( $_oDoc );
        }

        $_noNode  = $_oXPath->query( './/body' )->item( 0 );
        $_sHTML   = $_oDOMHelper->getInnerHTML( $_noNode );
        return preg_replace('/([\r\n]+\s*){2,}/', '$1', $_sHTML );

    }

}