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
 * Usage:
 * ```
 * $_oClass = new AmazonAutoLinks_MockClass( 'MyClass', 'a', 'b', 'c' );
 * return $_oClass->call( 'myMethodName', 'foo', 'bar' );
 * ```
 */
class AmazonAutoLinks_MockClass {

    /**
     * @var ReflectionClass
     */
    public $oReflectionClass;

    /**
     * @var object The specified class instance.
     */
    public $oClass;

    /**
     * Sets up properties.
     * @param  string $sClassName
     * @param  array  $aParameters
     * @throws ReflectionException
     */
    public function __construct( $sClassName, $aParameters=array() ) {
        $this->oReflectionClass = new ReflectionClass( $sClassName );
        $this->oClass = $this->oReflectionClass->newInstanceArgs( $aParameters );
    }

    /**
     * @param string $sPropertyName
     * @return mixed
     * @throws ReflectionException
     */
    public function get( $sPropertyName ) {
        $_oReflectionProperty = $this->oReflectionClass->getProperty( $sPropertyName );
        $_oReflectionProperty->setAccessible(true );
        return $_oReflectionProperty->getValue( $this->oClass );
    }

    /**
     * @param  string $sMethodName
     * @param  array $aParameters    To pass a parameter as a reference, add a prefix of an ampersand. like array( &$first, $second, $third )
     * @return mixed
     * @throws ReflectionException
     */
    public function call( $sMethodName, $aParameters=array() ) {
        $_oReflectionMethod = $this->___getMethodMocked( $this->oReflectionClass, $sMethodName );
        return $_oReflectionMethod->invokeArgs( $this->oClass, $aParameters );
    }
        /**
         * @param ReflectionClass $oReflectionClass
         * @param $sMethodName
         * @return ReflectionMethod
         * @throws ReflectionException
         */
        private function ___getMethodMocked( ReflectionClass $oReflectionClass, $sMethodName ) {
            $_oMethod = $this->oReflectionClass->getMethod( $sMethodName );
            $_oMethod->setAccessible( true );
            return $_oMethod;
        }

}