<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Provides the ability to generate a table of an array representation.
 *
 * @since       4.6.21
 */
class AmazonAutoLinks_ArrayTable extends AmazonAutoLinks_Utility {

    public $aArray = array();
    public $aAllAttributes = array(
        'table' => array(),
        'tr'    => array(),
        'td'    => array()
    );

    /**
     * Sets up properties and hooks.
     */
    public function __construct( array $aArray, array $aAllAttributes=array() ) {
        $this->aArray         = $aArray;
        $this->aAllAttributes = $aAllAttributes + $this->aAllAttributes;
    }

    public function get() {
        return $this->getTableOfArray( $this->aArray, $this->aAllAttributes );
    }

    /**
     * Generates a table output of a given array.
     * Designed to display key-value pairs in a table.
     * @since  4.6.21
     * @return string
     */
    static public function getTableOfArray( array $aArray, array $aAllAttributes ) {

        $_aAllAttributes = $aAllAttributes + array(
            'table' => array(),
            'tbody' => array(),
            'td' => array(),
            'tr' => array(),
            't' => array(),
        );
        return "<table " . self::getAttributes( self::getElementAsArray( $_aAllAttributes, 'table' ) ) . ">"
                . "<tbody " . self::getAttributes( self::getElementAsArray( $_aAllAttributes, 'tbody' ) ) . ">"
                    . self::___getTableRows( $aArray, $_aAllAttributes )
                . "</tbody>"
            . "</table>";
    }
        static private function ___getTableRows( array $aItem, array $aAllAttributes ) {
            $_aTRAttr = self::getElementAsArray( $aAllAttributes, 'tr' );
            $_aTDAttr = self::getElementAsArray( $aAllAttributes, 'td' );
            if ( empty( $aItem ) ) {
                $_aTDAttr = array( 'colspan' => 2 ) + $_aTDAttr;
                return "<tr " . self::getAttributes( $_aTRAttr ) . ">"
                        . "<td " . self::getAttributes( $_aTDAttr ) . ">"
                            . __( 'No data found.', 'amazon-auto-links' )
                        . "</td>"
                    . "</tr>";
            }
            $_aTDAttrFirst            = $_aTDAttr;
            $_aTDAttrFirst[ 'class' ] = self::___addClass( 'column-key', self::getElement( $_aTDAttrFirst, array( 'class' ), '' ) );
            $_sOutput = '';
            foreach( $aItem as $_sColumnName => $_asValue ) {
                $_sOutput .= "<tr " . self::getAttributes( $_aTRAttr ) . ">";
                $_sOutput .= "<td " . self::getAttributes( $_aTDAttrFirst ) . ">"
                        . "<p>{$_sColumnName}</p>"
                     . "</td>";
                $_sOutput .= self::___getColumnValue( $_asValue, $aAllAttributes );
                $_sOutput .= "</tr>";
            }
            return $_sOutput;
        }

            /**
             * @param  string $sClassToAdd
             * @param  string $sClasses
             * @return string
             * @since  4.6.21
             */
            static private function ___addClass( $sClassToAdd, $sClasses ) {
                $_aClasses    = explode( ' ', $sClasses );
                $_aClasses[]  = $sClassToAdd;
                return implode( ' ', array_unique( $_aClasses ) );
            }
            static private function ___getColumnValue( $mValue, array $aAllAttributes ) {
                $_aTDAttr = self::getElementAsArray( $aAllAttributes, 'td' );
                $_aTDAttr[ 'class' ] = self::___addClass( 'column-value', self::getElement( $_aTDAttr, array( 'class' ), '' ) );
                if ( is_null( $mValue ) ) {
                    $mValue = '(null)';
                }
                if ( is_scalar( $mValue ) ) {
                    return "<td " . self::getAttributes( $_aTDAttr ) . ">"
                        . "<p>{$mValue}</p>"
                       . "</td>";
                }
                if ( is_array( $mValue ) ) {
                    return "<td " . self::getAttributes( $_aTDAttr ) . ">"
                            . self::getTableOfArray( $mValue, $aAllAttributes )
                        . "</td>";
                }
                return "<td " . self::getAttributes( $_aTDAttr ) . ">"
                        . AmazonAutoLinks_Debug::getDetails( $mValue )
                    . "</td>";
            }

}