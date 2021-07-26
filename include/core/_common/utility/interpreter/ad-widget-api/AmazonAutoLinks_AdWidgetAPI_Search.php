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
        $_sEndpoint  = $this->getEndpoint( $asKeywords, $aPayload );
        return $this->getJSONFromJSONP( $this->getResponse( $_sEndpoint ) );
    }

    /**
     * @param  string $sEndpoint
     * @return string
     * @since  4.6.9
     */
    public function getResponse( $sEndpoint ) {
        $_aArguments = array(
            'user-agent' => 'WordPress/' . $GLOBALS[ 'wp_version' ],
        );
        $_oHTTP      = new AmazonAutoLinks_HTTPClient( $sEndpoint, $this->iCacheDuration, $_aArguments, 'ad_widget_api' );
        return $_oHTTP->getBody();
    }

    /**
     * @param array|string  $asKeywords
     * @param array         $aPayload       API request parameters.
     * @since 4.6.9
     * @return string       The endpoint URI
     */
    public function getEndpoint( $asKeywords, array $aPayload=array() ) {
        return $this->oLocale->getAdWidgetAPIEndpoint( $aPayload + array(
            'Operation'      => 'GetResults',
            'Keywords'       => is_array( $asKeywords ) ? implode( '|', $asKeywords ) : $asKeywords,
            'SearchIndex'    => 'All',
            'multipageStart' => 0,
            'InstanceId'     => 0,
            'multipageCount' => 20, // 20 is the max number of items
            'TemplateId'     => 'MobileSearchResults',
            'ServiceVersion' => '20070822',
            'MarketPlace'    => $this->oLocale->getCountryCode(),
        ) );
    }

}