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
 * @sicne       4.6.9
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
     * @param array|string  $asKeywords
     * @param array         $aPayload       API request parameters.
     * @since 4.6.9
     * @return string       The endpoint URI
     */
    public function getEndpoint( $asKeywords, array $aPayload=array() ) {
        return $this->oLocale->getAdWidgetAPIEndpoint( $aPayload + array(
            'multipageCount' => 20, // 20 is the max number of items    // @remark Not sure but this key must come first. Otherwise, the response become empty
            'Operation'      => 'GetResults',
            'Keywords'       => is_array( $asKeywords ) ? implode( '|', $asKeywords ) : $asKeywords,
            'SearchIndex'    => 'All',
            'multipageStart' => 0,
            'InstanceId'     => 0,
            'TemplateId'     => 'MobileSearchResults',
            'ServiceVersion' => '20070822',
            'MarketPlace'    => $this->oLocale->getCountryCode(),
        ) );
    }

}