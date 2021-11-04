<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */
 
/**
 * Performs Ad Widget API Search requests.
 *
 * @since       4.6.9
 */
class AmazonAutoLinks_AdWidgetAPI_Search extends AmazonAutoLinks_AdWidgetAPI_Base {

    /**
     * @param  array|string $asKeywords
     * @param  array $aPayload
     * @return array
     * @since  4.6.9
     */
    public function get( $asKeywords, array $aPayload=array() ) {
        if ( ! $this->oLocale->get()->sAdSystemServer ) {
            return array();
        }
        $_aResult     = array(
            'results' => array(),
        );
        $_aKeywords   = $this->getAsArray( $asKeywords );
        $_aChunksBy20 = array_chunk( $_aKeywords, 20 );      // the maximum number of items is 20
        foreach( $_aChunksBy20 as $_aChunkBy20 ) {
            $_sEndpoint  = $this->getEndpoint( $_aChunkBy20, $aPayload );
            $_aResponse  = $this->getJSONFromJSONP( $this->getResponse( $_sEndpoint ) );
            if ( ! isset( $_aResponse[ 'results' ] ) ) {
                continue;
            }
            // Merge items
            $_aResult[ 'results' ] = array_merge( $_aResult[ 'results' ], $_aResponse[ 'results' ] );
            unset( $_aResponse[ 'results' ] );
            // Merge other elements such as `InstanceId` and `MarketPlace`.
            $_aResult    = $_aResult + $_aResponse;
        }
        return $_aResult;
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
            'InstanceId'     => 0,
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