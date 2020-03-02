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
 * Provides utility methods that deal with XML.
 
 * @since       3       
 */
class AmazonAutoLinks_Utility_XML extends AmazonAutoLinks_Utility_String {
    
    /**
     * Returns an XML object from the given XML string content.
     * 
     * Returns a tag-stripped string on error.
     * 
     * @return      SimpleXMLElement|boolean        False or a SimpleXMLElement object.
     */
    static public function getXMLObject( $sXML ) {
        
        // Disable DOM related errors to be displayed.
        $bDOMError  = libxml_use_internal_errors( true );
        $_boXML     = simplexml_load_string( 
            $sXML,
            null,   // use the default value
            LIBXML_NOCDATA // preserve CDATA
        );
        
        // Restore the error setting.
        libxml_use_internal_errors( $bDOMError );    
        
        // Result - false or a SimpleXMLElement instance.
        return $_boXML;
            
    }
    
    /**
     * Converts an XML document to json.
     * 
     */
    static public function convertXMLtoJSON( $osXML ) {
                
        if ( is_object( $osXML ) ) {
            return json_encode( $osXML );
        }
                
        // Otherwise, it's a string.
        // Disable DOM related errors to be displayed.
        $bDOMError  = libxml_use_internal_errors( true );    
        $oXML       = simplexml_load_string( $osXML );
        
        // Restore the error setting.
        libxml_use_internal_errors( $bDOMError );    
        if ( false !== $oXML ) {
            // Process XML structure here
            return json_encode( $oXML );    
        }
        
        // libxml_get_errors() returns an array
        return  json_encode( libxml_get_errors() );    
                    
    }
    
    /**
     * Converts an XML document to associative array.
     * 
     * @return      array
     */
    static public function convertXMLtoArray( $osXML ) {
        return json_decode( 
            self::convertXMLtoJSON( $osXML ),   // string
            true 
        );
    }
        
}