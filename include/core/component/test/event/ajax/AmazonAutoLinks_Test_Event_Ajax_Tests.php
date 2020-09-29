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
        'line'    => 0,         // the line that triggered an error.
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

            $this->___include( $_sClassName, $_sFilePath );

            $_aTags    = $this->getElementAsArray( $aPost, array( 'tags' ) );
            $_aResults = $this->_getResults( $_sClassName, $_sFilePath, $_aTags );

        } catch ( Exception $_oException ) {
            // throw new Exception( $_oException->getMessage() . '<hr />' . 'Line: ' . $_oException->getLine() )
            // Format
            $_sClassName = isset( $_sClassName ) ? $_sClassName : '';
            throw new Exception(
                // results array
                array(
                    // each result array
                    $this->___getExceptionResult( $_oException, $_sClassName, '', '' )
                )
            );
        }

        return $_aResults;

    }

        /**
         * @param string $sClassName
         * @param string $sFilePath
         * @throws Exception
         */
        private function ___include( $sClassName, $sFilePath ) {
            if ( class_exists( $sClassName ) ) {
                return;
            }
            // In case of a PHP error
            ob_start(); // Capture start
            include_once( $sFilePath );
            $_sPHPError = ob_get_contents();
            ob_end_clean(); // Capture end
            if ( ! $_sPHPError ) {
                return;
            }
            throw new Exception( 'PHP Error occurred during a test.<hr />' . $_sPHPError );
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

            $_aResults   = array();
            $_oClass     = new ReflectionClass( $sClassName );
            foreach( $_oClass->getMethods( ReflectionMethod::IS_PUBLIC ) as $_oMethod ) {
                if ( ! $this->___canMethodRun( $_oMethod, $sClassName, $aTags, $sMethodPrefix ) ) {
                    continue;
                }
                $_aResults[] = $this->___getMethodTested( $_oMethod, $sFilePath );
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

                $_sPurpose    = $this->___getDocBlockAnnotation( $oMethod, 'purpose' );
                $_sClassName  = $oMethod->class;
                $_sMethodName = $oMethod->getName();

                try {

                    // Perform testing
                    ob_start(); // Capture start
                    $_aResult  = $this->___getEachMethodTested( $_sClassName, $_sMethodName, $_sPurpose, $sFilePath );
                    $_sContent = ob_get_contents();
                    ob_end_clean(); // Capture end
                    if ( $_sContent ) {
                        throw new Exception( $_sContent, 1 );
                    }

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
            private function ___canMethodRun( ReflectionMethod $oMethod, $sClassName, array $aTags, $sMethodPrefix ) {

                // The method might be of its parent class. In that case, skip.
                if ( strtolower( $oMethod->class ) !== strtolower( $sClassName ) ) {
                    return false;
                }

                if ( ! $this->hasPrefix( $sMethodPrefix, $oMethod->getName() ) ) {
                    return false;
                }

                if ( $this->___hasAllowedTags( $oMethod->getDeclaringClass(), $aTags ) ) {
                    return true;
                }

                if ( ! $this->___hasAllowedTags( $oMethod, $aTags ) ) {
                    return false;
                }

                return true;

            }
            /**
             * @param ReflectionClass|ReflectionMethod $oSubject
             * @param array $aSpecifiedTags
             * @return bool `true` when tags are not specified or found. `false` when tags are specified and not found.
             * @since 4.3.0
             */
            private function ___hasAllowedTags( $oSubject, array $aSpecifiedTags ) {

                // Not specified, meaning all tags are allowed.
                if ( empty( $aSpecifiedTags ) ) {
                    return true;
                }
                // At this point there are specified tags that can only go through.
                $_aDocBlockTags = $this->getStringIntoArray( $this->___getDocBlockAnnotation( $oSubject, 'tags' ), ',' );
                if ( $this->isEmpty( array_intersect( $aSpecifiedTags, $_aDocBlockTags ) ) ) {
                    return false;
                }
                return true;

            }
            /**
             * @param Exception|AmazonAutoLinks_Test_Exception $oException
             * @param string $sClassName
             * @param string $sMethodName
             * @param string $sPurpose
             * @return array
             */
            private function ___getExceptionResult( Exception $oException, $sClassName, $sMethodName, $sPurpose ) {
                $_sClassName = get_class( $oException );
                $_sMessage   = 'AmazonAutoLinks_Test_Exception' === $_sClassName
                    ? implode( '<hr />', array_map( array( $this, '___replyToEncloseInP' ), $oException->getMessages() ) )
                    : $oException->getMessage();
                $_iLine      = 'AmazonAutoLinks_Test_Exception' === $_sClassName
                    ? $oException->get( 'line' )
                    : $oException->getLine();
                $_aDefault   = array(
                        'name'    => $sClassName . ( $sMethodName ? '::' . $sMethodName . '()' : '' ),
                        'purpose' => $sPurpose,
                        'line'    => $_iLine,
                    ) + $this->___aResultStructure;
                if ( 1 === $oException->getCode() ) {
                    return array(
                        'success' => false,
                        'message' => $_sMessage,
                        'raw'     => true,
                    ) + $_aDefault;
                }
                return array(
                    'success' => false,
                    'message' => $_sMessage,
                    'raw'     => false,
                ) + $_aDefault;

            }
                /**
                 * @param $sMessage
                 * @return string
                 * @callback array_map()
                 */
                private function ___replyToEncloseInP( $sMessage ) {
                    return "<p>" . $sMessage . "</p>";
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
                    'message' => null,
                    'name'    => $_sClassMethod,
                    'purpose' => $_sPurpose,
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
         * @param ReflectionMethod|ReflectionClass $oSubject
         * @param $sAnnotation
         *
         * @return string
         */
        private function ___getDocBlockAnnotation( $oSubject, $sAnnotation ) {
            $_sDockBlock = $oSubject->getDocComment();
            preg_match_all('/@\Q' . $sAnnotation . '\E\s+(.*?)\n/s', $_sDockBlock, $_aMatches );
            $_aAnnotations = $_aMatches[ 1 ];
            return implode( ' ', $_aAnnotations );
        }

        /**
         * @param string $sClassName
         * @param string $sMethodName
         * @param string $sPurpose
         * @param string $sFilePath
         * @return array
         * @throws ReflectionException
         * @throws AmazonAutoLinks_Test_Exception
         * @throws Exception
         */
        private function ___getEachMethodTested( $sClassName, $sMethodName, $sPurpose, $sFilePath ) {

            $_oTestClass = new $sClassName;
            try {

                $_mResult    = $_oTestClass->$sMethodName();
                $_aResult    = $this->___getResultFormatted( $_mResult, $sClassName, $sMethodName, $sPurpose, $sFilePath );
                $_aOutputs   = $_oTestClass->aOutputs;
                if ( ! empty( $_aOutputs ) ) {
                    $_aResult[ 'message' ] .= implode( '<hr />', $_aOutputs );
                }
                return $_aResult;

            } catch ( Exception $_oException ) {

                if ( 'AmazonAutoLinks_Test_Exception' !== get_class( $_oException ) ) {
                    throw $_oException;
                }
                // Otherwise it is the AmazonAutoLinks_Test_Exception
                $_aOutputs   = $_oTestClass->aOutputs;
                if ( ! empty( $_aOutputs ) ) {
                    $_oException->addMessage( implode( '<hr />', $_aOutputs ) );
                }
                throw $_oException;

            }

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
            $_sUtilityScriptPath = $this->isDebugMode()
                ? AmazonAutoLinks_Registry::$sDirPath . '/asset/js/utility.js'
                : AmazonAutoLinks_Registry::$sDirPath . '/asset/js/utility.min.js';
            wp_enqueue_script( 'aalUtility', $this->getSRCFromPath( $_sUtilityScriptPath ), array( 'jquery' ), true );
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
                // no labels set as tests are only available when the site debug mode is on.
                // Also text regarding tests such as error messages should not be translated.
            );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            wp_enqueue_script( $sScriptHandle, $sScriptSRC, array( 'jquery', 'jquery-ui-accordion', 'aalUtility' ), true );
            wp_localize_script( $sScriptHandle, $sScriptHandle, $_aScriptData );

        }

}