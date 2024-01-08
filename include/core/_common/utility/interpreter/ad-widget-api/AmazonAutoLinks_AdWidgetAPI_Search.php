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
 * Performs Ad Widget API Search requests.
 *
 * @since 4.6.9
 */
class AmazonAutoLinks_AdWidgetAPI_Search extends AmazonAutoLinks_AdWidgetAPI_Base {

    /**
     * @param  array|string $asKeywords
     * @param  array        $aPayload
     * @param  integer|null $inCount
     * @return array
     * @since  4.6.9
     * @since  5.1.2        Added the `$inCount` parameter.
     */
    public function get( $asKeywords, array $aPayload=array(), $inCount=null ) {

        if ( ! $this->oLocale->get()->sAdSystemServer ) {
            return array();
        }
        $_iOffset = ( integer ) $this->getElement( $aPayload, array( 'multipageStart' ), 0 );
        $_aResult = $this->___getResultWithOffset( $asKeywords, $aPayload, $_iOffset );

        // Check item count and if it is less than the expected number, perform requests by incrementing the page.
        $_iExpected  = ( integer ) $inCount;
        $_iActual    = count( $this->getElementAsArray( $_aResult, array( 'results' ) ) );
        while( $this->___shouldFetchNext( $_iActual, $_iExpected ) ) {
            $_iOffset      = $_iOffset + 20;
            $_aResultPaged = $this->___getResultWithOffset( $asKeywords, $aPayload, $_iOffset );
            $_iThis        = count( $_aResultPaged[ 'results' ] );
            if ( ! $_iThis ) {
                break;  // not found
            }
            $_aResult[ 'results' ] = $this->___getResultItemsMerged( $_aResult[ 'results' ], $_aResultPaged[ 'results' ] );
            $_iThisActual  = count( $_aResult[ 'results' ] );
            if ( $_iActual === $_iThisActual ) {
                break;  // no more new items, found but duplicates
            }
            unset( $_aResultPaged[ 'results' ] );
            $_iActual      = $_iThisActual;
            $_aResult      = $_aResult + $_aResultPaged;
        }
        return $_aResult;

    }
        /**
         * @since  5.2.1
         * @return boolean
         */
        private function ___shouldFetchNext( $iActual, $iExpected ) {
            if ( $iExpected <= 20 ) {
                return false;
            }
            return $iActual && $iActual < $iExpected;
        }
        private function ___getResultItemsMerged( $aPrecedence, $aAdditional ) {
            $_aMerged = array();
            foreach( array_merge( $aPrecedence, $aAdditional ) as $_aItem ) {
                $_aItem = $_aItem + array( 'ASIN' => '' );
                $_aMerged[ $_aItem[ 'ASIN' ] ] = $_aItem;
            }
            unset( $_aMerged[ '' ] );
            return $_aMerged;
        }
        /**
         * @param  array|string $asKeywords
         * @param  array        $aPayload
         * @param  integer      $iPage
         * @return array
         * @since  5.1.2
         */
        private function ___getResultWithOffset( $asKeywords, array $aPayload, $iPage ) {
            $_aResult     = array(
                'results' => array(),
            );
            $aPayload[ 'multipageStart' ] = $iPage;
            $_aKeywords   = $this->getAsArray( $asKeywords );
            $_aChunksBy20 = array_chunk( $_aKeywords, 20 );      // the maximum number of items is 20
            add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 1 );
            add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10, 2 );
            $_iLastModified = null;
            unset( $this->___iLastModifiedDate );   // clean a previous value
            foreach( $_aChunksBy20 as $_aChunkBy20 ) {

                $_sEndpoint  = $this->getEndpoint( $_aChunkBy20, $aPayload );
                $_aResponse  = $this->getJSONFromJSONP( $this->getResponse( $_sEndpoint ) );
                if ( ! isset( $_aResponse[ 'results' ] ) ) {
                    continue;
                }

                // Capture last modified date
                $_iLastModified = isset( $_iLastModified ) ? $_iLastModified : $this->___iLastModifiedDate;

                // Merge items
                $_aResult[ 'results' ] = $this->___getResultItemsMerged( $_aResult[ 'results' ], $_aResponse[ 'results' ] );
                unset( $_aResponse[ 'results' ] );

                // Merge other elements such as `InstanceId` and `MarketPlace`.
                $_aResult    = $_aResult + $_aResponse;

            }
            remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
            remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10 );
            unset( $this->___iLastModifiedDate );   // clean-up
            $_aResult[ '_ModifiedDate' ] = $_iLastModified;
            return $_aResult;
        }
            /**
             * @var   string
             * @since 5.1.0
             */
            private $___iLastModifiedDate = '';
            public function replyToCaptureUpdatedDate( $aCache ) {
                $this->___iLastModifiedDate = $this->getLastModified( $aCache[ 'data' ], $aCache[ '_modified_timestamp' ] );
                return $aCache;
            }
            public function replyToCaptureUpdatedDateForNewRequest( $aoResponse, $sURL ) {
                $this->___iLastModifiedDate = $this->getLastModified( $aoResponse, time() );
                return $aoResponse;
            }

    /**
     * @param  array|string $asKeywords
     * @param  array        $aPayload   API request parameters.
     * @since  4.6.9
     * @return string      The endpoint URI.
     */
    public function getEndpoint( $asKeywords, array $aPayload=array() ) {
        $aPayload = array_filter( $aPayload, array( $this, 'isNotNull' ) );    // drop null-value elements
        return $this->oLocale->getAdWidgetAPIEndpoint( $aPayload + array(
            'multipageCount' => 20, // 20 is the max number of items    // @remark Not sure but this key must come first. Otherwise, the response become empty
            'Operation'      => 'GetResults',
            'Keywords'       => $this->___getKeywordsFormatted( $asKeywords ),
            'SearchIndex'    => 'All',
            'multipageStart' => 0,
            // 'InstanceId'     => 0, // @deprecated 5.3.6 by not specifying it, it reduces the chance of getting empty responses
            'TemplateId'     => 'MobileSearchResults',
            'ServiceVersion' => '20070822',
            'MarketPlace'    => $this->oLocale->getCountryCode(),
        ) );
    }
        /**
         * @since  5.0.2
         * @return string
         */
        private function ___getKeywordsFormatted( $asKeywords ) {
            $_aKeywords = $this->getAsArray( $asKeywords );
            $_aKeywords = array_map( array( $this, '___replyToGetEachKeywordFormatted' ), $_aKeywords );
            return implode( '|', $_aKeywords );
        }
            /**
             * @param    string $sKeyword
             * @return   string
             * @since    5.0.2
             * @callback array_map()
             */
            private function ___replyToGetEachKeywordFormatted( $sKeyword ) {
                $_sKeyword = html_entity_decode( $sKeyword, ENT_QUOTES );   //  Convert apostrophes such as &#039; to normal characters.
                return urlencode( $_sKeyword ); // Convert white spaces to URL-encoded characters.
            }

}