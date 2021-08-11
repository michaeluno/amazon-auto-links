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
 * Tests KSES related functions
 *  
 * @package Amazon Auto Links
 * @since   4.6.19
 * @tags    kses
*/
class AmazonAutoLinks_UnitTest_WordPress_KSESFunctions extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags
     * @see wp_kses()
     */
    public function test_wp_kses() {
        $_sOutput = <<<OUTPUT
<div class="product-container">
    <h4 class="product-title">
        <a href="https://www.amazon.it/BRITA-Cartucce-caraffe-filtranti-filtrata/dp/B06Y2DCPMS/ref=zg_bs_kitchen_home_1?_encoding=UTF8&amp;psc=1&amp;refRID=2W263R32C1GA6PMP0GBN&amp;tag=nicolatest0d-21&amp;language=it_IT&amp;currency=EUR" title="Il filtro per caraffa BRITA MAXTRA+ è composto da carboni attivi di origine naturale, resine a scambio ionico ed una fitta maglia filtrante Il filtro per caraffa BRITA MAXTRA+ riduce cloro (minimo 80%), calcare (minimo 80%), metalli come piombo (mini... read more" target="_blank" rel="nofollow">
            Brita Filtri per Acqua MAXTRA+ Pack 2, 2 Mesi di Filtrazione            
        </a>
    </h4>
    <div class="product-thumbnail" style="width:160px">
        <a href="https://www.amazon.it/BRITA-Cartucce-caraffe-filtranti-filtrata/dp/B06Y2DCPMS/ref=zg_bs_kitchen_home_1?_encoding=UTF8&amp;psc=1&amp;refRID=2W263R32C1GA6PMP0GBN&amp;tag=nicolatest0d-21&amp;language=it_IT&amp;currency=EUR" title="Il filtro per caraffa BRITA MAXTRA+ è composto da carboni attivi di origine naturale, resine a scambio ionico ed una fitta maglia filtrante Il filtro per caraffa BRITA MAXTRA+ riduce cloro (minimo 80%), calcare (minimo 80%), metalli come piombo (mini... read more" target="_blank" rel="nofollow">
            <img src="https://images-eu.ssl-images-amazon.com/images/I/61mbFw3uR2L._AC_UL160_SR160,160_.jpg" style="max-width:160px" alt="Il filtro per caraffa BRITA MAXTRA+ è composto da carboni attivi di origine naturale, resine a scambio ionico ed una fitta maglia filtrante Il filtro per caraffa BRITA MAXTRA+ riduce cloro (minimo 80%), calcare (minimo 80%), metalli come piombo (mini... read more">
        </a>
    </div>
    <div class="product-description">
        <div class="amazon-customer-rating-stars">
            <div class="crIFrameNumCustReviews" data-rating="47" data-review-count="21096" data-review-url="https://www.amazon.it/product-reviews/B06Y2DCPMS?tag=nicolatest0d-21"><span class="crAvgStars"><span class="review-stars"><a href="https://www.amazon.it/product-reviews/B06Y2DCPMS?tag=nicolatest0d-21" target="_blank" rel="nofollow noopener"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 160 32" enable-background="new 0 0 160 32"><title>4.7 out of 5 stars</title><use xlink:href="#amazon-rating-stars" fill="url(#star-fill-gradient-47)"></use><image src="https://images-na.ssl-images-amazon.com/images/G/01/x-locale/common/customer-reviews/stars-4-5.gif"></image></svg></a></span><span class="review-count">(<a href="https://www.amazon.it/product-reviews/B06Y2DCPMS?tag=nicolatest0d-21" target="_blank" rel="nofollow noopener">21096</a>)</span></span></div></div>            <div class="amazon-product-description">Il filtro per caraffa BRITA MAXTRA+ è composto da carboni attivi di origine naturale, resine a scambio ionico ed una fitta maglia filtrante Il filtro per caraffa BRITA MAXTRA+ riduce cloro (minimo 80%), calcare (minimo 80%), metalli come piombo (mini... <a href="https://www.amazon.it/BRITA-Cartucce-caraffe-filtranti-filtrata/dp/B06Y2DCPMS/ref=zg_bs_kitchen_home_1?_encoding=UTF8&amp;psc=1&amp;refRID=2W263R32C1GA6PMP0GBN&amp;tag=nicolatest0d-21&amp;language=it_IT&amp;currency=EUR" target="_blank" rel="nofollow noopener" style="display:inline">read more</a>
        </div>        
    </div>
</div>
<svg style="position:absolute;width:0;height:0;overflow:hidden" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="0" height="0" viewBox="0 0 160 32">
<g id="amazon-rating-stars">
    <path stroke="#E17B21" stroke-width="2" d="M 16.025391 0.58203125 L 11.546875 10.900391 L 0 12.099609 L 8.8222656 19.849609 L 6.1269531 31.382812 L 16.021484 25.294922 L 25.914062 31.382812 L 23.265625 19.849609 L 32 12.099609 L 20.388672 10.900391 L 16.025391 0.58203125 z M 32 12.099609 L 40.822266 19.849609 L 38.126953 31.382812 L 48.021484 25.294922 L 57.914062 31.382812 L 55.265625 19.849609 L 64 12.099609 L 52.388672 10.900391 L 48.025391 0.58203125 L 43.546875 10.900391 L 32 12.099609 z M 64 12.099609 L 72.822266 19.849609 L 70.126953 31.382812 L 80.021484 25.294922 L 89.914062 31.382812 L 87.265625 19.849609 L 96 12.099609 L 84.388672 10.900391 L 80.025391 0.58203125 L 75.546875 10.900391 L 64 12.099609 z M 96 12.099609 L 104.82227 19.849609 L 102.12695 31.382812 L 112.02148 25.294922 L 121.91406 31.382812 L 119.26562 19.849609 L 128 12.099609 L 116.38867 10.900391 L 112.02539 0.58203125 L 107.54688 10.900391 L 96 12.099609 z M 128 12.099609 L 136.82227 19.849609 L 134.12695 31.382812 L 144.02148 25.294922 L 153.91406 31.382812 L 151.26562 19.849609 L 160 12.099609 L 148.38867 10.900391 L 144.02539 0.58203125 L 139.54688 10.900391 L 128 12.099609 z">
    </path>
</g>
<defs>
    <linearGradient id="star-fill-gradient-48">
    <stop offset="96%" stop-color="#FFA41C"></stop><stop offset="96%" stop-color="transparent" stop-opacity="1"></stop></linearGradient></defs><defs><linearGradient id="star-fill-gradient-47"><stop offset="94%" stop-color="#FFA41C">
    
</stop><stop offset="94%" stop-color="transparent" stop-opacity="1"></stop>
    </linearGradient></defs><defs><linearGradient id="star-fill-gradient-42">
    <stop offset="84%" stop-color="#FFA41C">
</stop><stop offset="84%" stop-color="transparent" stop-opacity="1">

</stop></linearGradient></defs><defs><linearGradient id="star-fill-gradient-30">
<stop offset="60%" stop-color="#FFA41C">
</stop><stop offset="60%" stop-color="transparent" stop-opacity="1"></stop></linearGradient></defs><defs><linearGradient id="star-fill-gradient-45">
<stop offset="90%" stop-color="#FFA41C"></stop><stop offset="90%" stop-color="transparent" stop-opacity="1"></stop></linearGradient></defs><defs><linearGradient id="star-fill-gradient-49">
<stop offset="98%" stop-color="#FFA41C">
</stop><stop offset="98%" stop-color="transparent" stop-opacity="1">

        </stop>
    </linearGradient>
</defs>
<defs>
    <linearGradient id="star-fill-gradient-46">
        <stop offset="92%" stop-color="#FFA41C"></stop>
        <stop offset="92%" stop-color="transparent" stop-opacity="1"></stop>
    </linearGradient>
</defs>
<image src="https://images-na.ssl-images-amazon.com/images/G/01/x-locale/common/customer-reviews/stars-5-0.gif">
</image>
</svg>
OUTPUT;
        $_sOutput = trim( $_sOutput );
        add_filter( 'safe_style_css', function( $styles ) {
            $styles[] = 'display';
            $styles[] = 'position';
            return $styles;
        } );
        $_aCommonAttributes   = array(
            'id'    => true,
            'class' => true,
            'name'  => true,
            'style' => true,
            'title' => true,
        );
        $_aDefaultAllowedTags = wp_kses_allowed_html( 'post' );
        $_aAllowedPostTags    = $_aDefaultAllowedTags + array(
            'a'              => array(
                'style' => true,
            ) + $_aDefaultAllowedTags[ 'a' ] + $_aCommonAttributes,
            'use'            => array(
                'xlink:href'        => true,
                'fill'              => true,
            ) + $_aCommonAttributes,
            'image'          => array(
                'src'        => true,
            ) + $_aCommonAttributes,
            'stop'           => array(
                 'offset'       => true,
                 'stop-color'   => true,
                 'stop-opacity' => true,
            ) + $_aCommonAttributes,
            'lineargradient' => array() + $_aCommonAttributes,
            'defs' => array() + $_aCommonAttributes,
            'svg'   => array(
                'class'             => true,
                'fill'              => true,
                'aria-hidden'       => true,
                'aria-labelledby'   => true,
                'role'              => true,
                'xmlns'             => true,
                'xmlns:xlink'       => true,
                'width'             => true,
                'height'            => true,
                'viewbox'           => true, // <= Must be lower case!
                'enable-background' => true,
            ) + $_aCommonAttributes,
            'g'     => array( 'fill' => true ) + $_aCommonAttributes,
            'title' => array( 'title' => true ) + $_aCommonAttributes,
            'path'  => array(
                'd'            => true,
                'fill'         => true,
                'stroke'       => true,
                'stroke-width' => true,
            ) + $_aCommonAttributes,
        );
        $_sSanitized = trim( wp_kses( $_sOutput, $_aAllowedPostTags ) );

        $this->_output( '<h4>Before</h4>' );
        $this->_output( $_sOutput );

        $this->_output( '<h4>After</h4>' );
        $this->_output( $_sSanitized );
        $_bOK = $this->_assertEqual( $_sOutput, $_sSanitized, 'The output should be remained intact after applying the sanitization method.' );
        if ( ! $_bOK ) {
            $_aSanitized = explode( PHP_EOL, $_sSanitized );
            foreach( explode( PHP_EOL, $_sOutput ) as $_iIndex => $_sElement ) {
                $_sSanitizedElement = isset( $_aSanitized[ $_iIndex ] ) ? $_aSanitized[ $_iIndex ] : null;
                if ( $_sElement !== $_sSanitizedElement ) {
                    $this->_outputDetails( "Detected Difference on line {$_iIndex}", $_sElement, $_sSanitizedElement );
                }
            }
        }

    }

}