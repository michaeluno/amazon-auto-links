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
     * @param $sClassName
     * @param mixed ...$aParameters
     * @throws ReflectionException
     */
    public function __construct( $sClassName, ...$aParameters ) {
        if (version_compare(phpversion(), '<', '5.6.0')) {
            trigger_error('Amazon Auto Links: The class requires PHP 5.6 or above.', E_USER_WARNING);
            return;
        }
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
     * @param $sMethodName
     * @param mixed ...$aParameters
     * @return mixed
     * @throws ReflectionException
     */
    public function call( $sMethodName, ...$aParameters ) {
        $_oMethod = $this->___getMethodMocked( $this->oReflectionClass, $sMethodName );
        return $_oMethod->invokeArgs( $this->oClass, $aParameters );
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