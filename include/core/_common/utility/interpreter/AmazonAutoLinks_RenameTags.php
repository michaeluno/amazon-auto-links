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
 * Provides method to rename tags in DOM.
 * 
 * @package     Amazon Auto Links
 * @since       3.3.0
 */
class AmazonAutoLinks_RenameTags extends AmazonAutoLInks_DOM {
        
    /**
     * Renames HTML tags.
     */
    public function rename( $asSearches, $asReplaces, $oDOM ) {

    
        $_aSearches = $this->getAsArray( $asSearches );
        $_aReplaces = $this->getAsArray( $asReplaces );

        foreach( $_aSearches as $_iIndex => $_sSearchTag ) {
            
            $_oXpath = new DOMXPath( $oDOM );
            $_oNodes = $_oXpath->query( 
                ".//{$_sSearchTag}" // "//*/{$_sSearchTag}" 
            );            
            
            foreach( $_oNodes as $_oNode ) {
                $_sReplace = isset( $_aReplaces[ $_iIndex ] )
                    ? $_aReplaces[ $_iIndex ]
                    : $_aReplaces[ 0 ];  
                $this->_renameTag( $_oNode, $_sReplace );
            }                    
                        
        }

    }
    
         /**
         * Renames a node in a DOM Document.
         *
         * @param       DOMElement $oNode
         * @param       string     sTagName
         *
         * @see         http://stackoverflow.com/questions/16307103/use-domdocument-to-replace-all-header-tags-with-the-h4-tags/16314814#16314814
         * @return      DOMNode
         */
        private function _renameTag( DOMElement $oNode, $sTagName ) {
            
            $_oRenamed = $oNode->ownerDocument->createElement( $sTagName );

            // foreach ( $oNode->attributes as $attribute ) {
                // $_oRenamed->setAttribute( $attribute->nodeName, $attribute->nodeValue );
            // }

            while ( $oNode->firstChild ) {
                $_oRenamed->appendChild( $oNode->firstChild );
            }

            return $oNode->parentNode->replaceChild( $_oRenamed, $oNode );
        }
 
        /**
         * @deprecated      not used at the moment
         * @see     http://stackoverflow.com/questions/8163298/how-do-i-change-xml-tag-names-with-php/8163404#8163404
         */
        function __renameTag(DOMElement $oldTag, $newTagName) {
            $document = $oldTag->ownerDocument;

            $newTag = $document->createElement($newTagName);
            foreach($oldTag->attributes as $attribute)
            {
                $newTag->setAttribute($attribute->name, $attribute->value);
            }
            foreach($oldTag->childNodes as $child)
            {
                $newTag->appendChild($oldTag->removeChild($child));
            }
            $oldTag->parentNode->replaceChild($newTag, $oldTag);
            return $newTag;
        } 
        /**
         * @deprecated      not used at the moment
         * @see     http://stackoverflow.com/questions/8163298/how-do-i-change-xml-tag-names-with-php/13882419#13882419
         */
        function ___renameTag( DOMElement $oldTag, $newTagName ) {
            $document = $oldTag->ownerDocument;

            $newTag = $document->createElement($newTagName);
            $oldTag->parentNode->replaceChild($newTag, $oldTag);

            foreach ($oldTag->attributes as $attribute) {
                $newTag->setAttribute($attribute->name, $attribute->value);
            }
            if ( $oldTag->hasChildNodes() ) {
                foreach ( $oldTag->childNodes as $child) {
                    $newTag->appendChild($oldTag->removeChild($child));
                }
            }
            return $newTag;
        } 
 
 
}