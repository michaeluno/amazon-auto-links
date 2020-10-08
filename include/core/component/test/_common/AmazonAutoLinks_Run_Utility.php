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
     * @param  string $sHTML
     * @param  array  $aRemoveTags  HTML tags to remove.
     * @return string
     * @since  4.3.4
     */
    static public function getHTMLBody( $sHTML, $aRemoveTags=array( 'script', 'style' ) ) {

        $_oDOM    = new AmazonAutoLinks_DOM;
        $_oDoc    = $_oDOM->loadDOMFromHTML( $sHTML );
        $_oXPath  = new DOMXPath( $_oDoc );
        $_oDOM->removeTags( $_oDoc, $aRemoveTags );
        $_noNode  = $_oXPath->query( './/body' )->item( 0 );
        return $_oDOM->getInnerHTML( $_noNode );

    }

}