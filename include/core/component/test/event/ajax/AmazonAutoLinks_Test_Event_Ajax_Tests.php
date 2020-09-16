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
 * Performs tests.
 * @since   4.3.0
 *
 */
class AmazonAutoLinks_Test_Event_Ajax_Tests extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookSuffix = 'aal_action_admin_do_tests';
    protected $_bLoggedIn = true;
    protected $_bGuest    = false;

    protected function _construct() {
        // load_{page slug}_{tab slug}
        add_action( 'load_aal_tests_tests', array( $this, 'replyToEnqueueResources' ) );
    }

    private $___aResultStructure = array(
        'success' => false,
        'raw'     => false,
        'name'    => '',
        'message' => '',
        'purpose' => '',
    );

    /**
     * @param  array $aPost
     *
     * @return array
     * @throws Exception        Throws a string value of an error message.
     */
    protected function _getResponse( array $aPost ) {

        try {

            $_sFilePath = $this->getElement( $aPost, array( 'file_path' ), '' );
            if ( ! file_exists( $_sFilePath ) ) {
                throw new Exception( 'The file does not exist: ' . $_sFilePath  );
            }
            $_sPHPCode   = AmazonAutoLinks_Test_Utility::getPHPCode( $_sFilePath );
            $_sClassName = AmazonAutoLinks_Test_Utility::getDefinedClass( $_sPHPCode );

            if ( ! class_exists( $_sClassName ) ) {
                include_once( $_sFilePath );
            }

            $_aTags    = $this->getElementAsArray( $aPost, array( 'tags' ) );
            $_aResults = $this->_getResults( $_sClassName, $_sFilePath, $_aTags );

        } catch ( Exception $_oException ) {
            throw new Exception( $this->___getExceptionErrorMessage( $_oException ) );
        }

        return $_aResults;

    }
        /**
         * @param string $sClassName The class name to test.
         * @param string $sFilePath The file path of the class.
         * @param array $aTags Tags set in the `@tags` annotation in test method doc-blocks.
         * @param string $sMethodPrefix The prefix of methods to test.
         * @return array
         * @throws ReflectionException
         * @since   4.3.0
         */
        protected function _getResults( $sClassName, $sFilePath, array $aTags=array(), $sMethodPrefix='test' ) {

            $_oReflect   = new ReflectionClass( $sClassName );
            $_aResults   = array();
            foreach( $_oReflect->getMethods( ReflectionMethod::IS_PUBLIC ) as $_oMethod ) {

                if ( ! $this->___canRun( $_oMethod, $sClassName, $aTags, $sMethodPrefix ) ) {
                    continue;
                }
                $_aResults[]  = $this->___getMethodTested( $_oMethod, $sFilePath );

            }
            return $_aResults;
        }
            /**
             * @return array
             * @param ReflectionMethod $oMethod
             * @param string $sFilePath
             * @since 4.3.0
             */
            private function ___getMethodTested( ReflectionMethod $oMethod, $sFilePath ) {

                $_sPurpose    = $this->___getMethodAnnotation( $oMethod, 'purpose' );
                $_sClassName  = $oMethod->class;
                $_sMethodName = $oMethod->getName();

                try {

                    // Perform testing
                    ob_start(); // Capture start
                    $_bsResult = $this->___getEachMethodTested( $_sClassName, $_sMethodName );
                    $_sContent = ob_get_contents();
                    ob_end_clean(); // Capture end
                    if ( $_sContent ) {
                        throw new Exception( $_sContent, 1 );
                    }

                    $_aResult = $this->___getResultFormatted( $_bsResult, $_sClassName, $_sMethodName, $_sPurpose, $sFilePath );

                }
                // Tests can throw exceptions (Exception) so here caching them.
                catch ( Exception $_oInnerException ) {
                    $_aResult = $this->___getExceptionResult( $_oInnerException, $_sClassName, $_sMethodName, $_sPurpose );
                }
                return $_aResult;

            }
            /**
             * Checks if the test method can be run.
             * @param ReflectionMethod $oMethod
             * @param string $sClassName
             * @param array $aTags
             * @param string $sMethodPrefix
             * @return boolean
             */
            private function ___canRun( ReflectionMethod $oMethod, $sClassName, array $aTags, $sMethodPrefix ) {

                // The method might be of its parent class. In that case, skip.
                if ( strtolower( $oMethod->class ) !== strtolower( $sClassName ) ) {
                    return false;
                }

                if ( ! $this->hasPrefix( $sMethodPrefix, $oMethod->getName() ) ) {
                    return false;
                }

                //  Check if tags are specified
                $_aMethodTags = $this->getStringIntoArray( $this->___getMethodAnnotation( $oMethod, 'tags' ), ',' );
                if ( ! empty( $aTags ) && $this->isEmpty( array_intersect( $aTags, $_aMethodTags ) ) ) {
                    return false;
                }

                return true;

            }
            /**
             * @param Exception $oException
             *
             * @return array
             */
            private function ___getExceptionResult( Exception $oException, $sClassName, $sMethodName, $sPurpose ) {
                $_aDefault = array(
                        'name'    => $sClassName . '::' . $sMethodName . '()',
                        'purpose' => $sPurpose,
                    ) + $this->___aResultStructure;
                if ( 1 === $oException->getCode() ) {
                    return array(
                        'success' => false,
                        'message' => $oException->getMessage(),
                        'raw'     => true,
                    ) + $_aDefault;
                }
                return array(
                    'success' => false,
                    'message' => $this->___getExceptionErrorMessage( $oException ),
                    'raw'     => false,
                ) + $_aDefault;

            }
                /**
                 * @param Exception $oException
                 *
                 * @return string
                 */
                private function ___getExceptionErrorMessage( Exception $oException ) {
                    $oException->getPrevious();
                    return $oException->getMessage()
                        . ' on the file, ' . $oException->getFile()
                        . ', Line: ' . $oException->getLine();
                }
            /**
             * @param string $mResult
             * @param string $sClassName
             * @param string $sMethodName
             * @param string $sPurpose
             * @param string $sFilePath
             * @return array
             */
            private function ___getResultFormatted( $mResult, $sClassName, $sMethodName, $sPurpose, $sFilePath ) {
                $_sPurpose     = $sPurpose ? $sPurpose . ' ' : '';
                $_sClassMethod = $sClassName . '::' . $sMethodName . '()';
                $_aDefault     = array(
                    'name'    => $_sClassMethod,
                    'purpose' => $sPurpose,
                ) + $this->___aResultStructure;
                if ( is_null( $mResult ) ) {
                    $_aError = ( ( array ) error_get_last() ) + array( 'type' => null, 'message' => null, 'file' => null, 'line' => null );
                    return array(
                        'message' => $_aError[ 'message' ] . ' in' . $_aError[ 'file' ] . ' on line ' . $_aError[ 'line' ],
                    ) + $_aDefault;
                }
                if ( is_bool( $mResult ) ) {
                    return $mResult
                        ? array(
                            'success' => true,
                        ) + $_aDefault
                        : $_aDefault;
                }
                if ( is_scalar( $mResult ) ) {
                    return array(
                        'success' => true,
                        'message' => $mResult,
                        'raw'     => true,
                    ) + $_aDefault;
                }
                if ( is_wp_error( $mResult ) ) {
                    /**
                     * @var WP_Error $mResult
                     */
                    return array(
                        'message' => $mResult->get_error_code() . ' ' . $mResult->get_error_message(),
                    ) + $_aDefault;
                }
                if ( is_array( $mResult ) ) {
                    return array(
                        'success' => true,
                        'message' => AmazonAutoLinks_Debug::getDetails( $mResult ),
                        'raw'     => true,
                    ) + $_aDefault;
                }
                return array(
                    'message' => 'Unsupported type result was returned: ' . gettype( $mResult ),
                ) + $_aDefault;
            }
        /**
         * @param ReflectionMethod $oMethod
         * @param $sAnnotation
         *
         * @return string
         */
        private function ___getMethodAnnotation( ReflectionMethod $oMethod, $sAnnotation ) {
            $_sDockBlock = $oMethod->getDocComment();
            preg_match_all('/@\Q' . $sAnnotation . '\E\s+(.*?)\n/s', $_sDockBlock, $_aMatches );
            $_aAnnotations = $_aMatches[ 1 ];
            return implode( ' ', $_aAnnotations );
        }
        /**
         * @param $sClassName
         * @param $sMethodName
         *
         * @return mixed|void
        */
        private function ___getEachMethodTested( $sClassName, $sMethodName ) {
            $_oClass = AmazonAutoLinks_AdminPageFramework_ClassTester::getInstance( $sClassName );
            return AmazonAutoLinks_AdminPageFramework_ClassTester::call(
                $_oClass,           // subject class object
                $sMethodName,       // method name (private/protected supported)
                array()      // method parameters
            );
        }

    /**
     * @since       4.3.0
     * @return      void
     */
    public function replyToEnqueueResources() {
        $this->_enqueueResources( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/tests', array( 'AmazonAutoLinks_UnitTest_Base' ), 'test' );
    }

        /**
         * @param $sScanDirPath
         * @param array $aBaseClasses
         * @param $sContext
         *
         * @return  void
         */
        protected function _enqueueResources( $sScanDirPath, array $aBaseClasses, $sContext ) {

            $_oFinder = new AmazonAutoLinks_Test_ClassFinder( $sScanDirPath, $aBaseClasses );
            $this->___enqueueAjaxScript(
                'aalTests',
                array(
                    'files'     => $_oFinder->getFiles(),
                    'context'   => $sContext,
                ),
                $this->getSRCFromPath(
                    $this->isDebugMode()
                        ? AmazonAutoLinks_Test_Loader::$sDirPath . '/asset/js/plugin-tests.js'
                        : AmazonAutoLinks_Test_Loader::$sDirPath . '/asset/js/plugin-tests.min.js'
                )
            );

        }

        /**
         * @param string $sScriptHandle The script handle to enqueue. This also serves as the data variable name on the JavaScript side.
         * @param array  $aTranslations The translation data passed to the JavaScript script.
         * @param string $sScriptSRC    The url of the Ajax JavaScript script.
         * @since 4.3.0
         */
        private function ___enqueueAjaxScript( $sScriptHandle, array $aTranslations, $sScriptSRC ) {

            $_aScriptData   = $aTranslations + array(
                'ajaxURL'          => admin_url( 'admin-ajax.php' ),
                'actionHookSuffix' => $this->_sActionHookSuffix,
                'nonce'            => wp_create_nonce( $this->_sNonceKey ),
                'spinnerURL'       => admin_url( 'images/loading.gif' ),
            );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            wp_enqueue_script( $sScriptHandle, $sScriptSRC, array( 'jquery' ), true );
            wp_localize_script( $sScriptHandle, $sScriptHandle, $_aScriptData );

        }

}