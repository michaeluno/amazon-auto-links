<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Generates rating star icons.
 *
 * @since 4.6.0
 */
class AmazonAutoLinks_SVGGenerator_RatingStar extends AmazonAutoLinks_SVGGenerator_Base {
    
    public function get( /* $iRating */ ) {

        $_aParameters = func_get_args() + array( '', 0,  );
        $iRating      = $_aParameters[ 0 ];
        $_sTitle      = $this->sTitle ? "<title>" . esc_html( $this->sTitle ) . "</title>" : '';

        if ( ! $this->bUseCache ) {
            self::$aDefinitions   = array();    // reset cache
        }

        $_sSVGDefinition        = '';
        $_sDefsGradient         = '';

        if ( ! isset( self::$aDefinitions[ 'main' ] ) ) {
            $_sSVGDefinition = "<svg class='definition' data-class='definition' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width=0 height=0 viewBox='0 0 160 32' display='none'>"
                . "<g id='amazon-rating-stars'>"
                    . "<path stroke='#E17B21' stroke-width='2' d='M 16.025391 0.58203125 L 11.546875 10.900391 L 0 12.099609 L 8.8222656 19.849609 L 6.1269531 31.382812 L 16.021484 25.294922 L 25.914062 31.382812 L 23.265625 19.849609 L 32 12.099609 L 20.388672 10.900391 L 16.025391 0.58203125 z M 32 12.099609 L 40.822266 19.849609 L 38.126953 31.382812 L 48.021484 25.294922 L 57.914062 31.382812 L 55.265625 19.849609 L 64 12.099609 L 52.388672 10.900391 L 48.025391 0.58203125 L 43.546875 10.900391 L 32 12.099609 z M 64 12.099609 L 72.822266 19.849609 L 70.126953 31.382812 L 80.021484 25.294922 L 89.914062 31.382812 L 87.265625 19.849609 L 96 12.099609 L 84.388672 10.900391 L 80.025391 0.58203125 L 75.546875 10.900391 L 64 12.099609 z M 96 12.099609 L 104.82227 19.849609 L 102.12695 31.382812 L 112.02148 25.294922 L 121.91406 31.382812 L 119.26562 19.849609 L 128 12.099609 L 116.38867 10.900391 L 112.02539 0.58203125 L 107.54688 10.900391 L 96 12.099609 z M 128 12.099609 L 136.82227 19.849609 L 134.12695 31.382812 L 144.02148 25.294922 L 153.91406 31.382812 L 151.26562 19.849609 L 160 12.099609 L 148.38867 10.900391 L 144.02539 0.58203125 L 139.54688 10.900391 L 128 12.099609 z' />"    // the actual vector images of 5 stars
                . "</g>"
            . "</svg>";
            self::$aDefinitions[ 'main' ] = true;
        }
        $_sIDGradient = 'star-fill-gradient-' . $iRating;
        if ( ! isset( self::$aDefinitions[ $_sIDGradient ] ) ) {
            $_sDefsGradient = "<defs>"
                    . "<linearGradient id='{$_sIDGradient}'>"
                        . "<stop offset='" . ( $iRating * 2 ) . "%' stop-color='#FFA41C'/>"
                        . "<stop offset='" . ( $iRating * 2 ) . "%' stop-color='transparent' stop-opacity='1' />"
                    . "</linearGradient>"
                . "</defs>";
            self::$aDefinitions[ $_sIDGradient ] = true;
        }
        $_sSVG = "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 160 32' enable-background='new 0 0 160 32'>"
                . $_sTitle
                . $_sDefsGradient
                . "<use xlink:href='#amazon-rating-stars' fill='url(#{$_sIDGradient})' />"
                . "<image src='" . esc_url( $this->sSRCFallbackImage ) . "' />" // fallback for browsers not supporting SVG
            . "</svg>";
        return $_sSVGDefinition . $_sSVG;
        /*
        return "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 160 32' enable-background='new 0 0 160 32'>"
                . "<title>"
                    . esc_html( $sTitle )
                . "</title>"
                . "<defs>"
                    . "<linearGradient id='star-fill-gradient-{$iRating}'>"
                        . "<stop offset='" . ( $iRating * 2 ) . "%' stop-color='#FFA41C'/>"
                        . "<stop offset='" . ( $iRating * 2 ) . "%' stop-color='transparent' stop-opacity='1' />"
                    . "</linearGradient>"
                . "</defs>"
                . "<g fill='url(#star-fill-gradient-{$iRating})'>"
                    . "<path stroke='#E17B21' stroke-width='2' d='M 16.025391 0.58203125 L 11.546875 10.900391 L 0 12.099609 L 8.8222656 19.849609 L 6.1269531 31.382812 L 16.021484 25.294922 L 25.914062 31.382812 L 23.265625 19.849609 L 32 12.099609 L 20.388672 10.900391 L 16.025391 0.58203125 z M 32 12.099609 L 40.822266 19.849609 L 38.126953 31.382812 L 48.021484 25.294922 L 57.914062 31.382812 L 55.265625 19.849609 L 64 12.099609 L 52.388672 10.900391 L 48.025391 0.58203125 L 43.546875 10.900391 L 32 12.099609 z M 64 12.099609 L 72.822266 19.849609 L 70.126953 31.382812 L 80.021484 25.294922 L 89.914062 31.382812 L 87.265625 19.849609 L 96 12.099609 L 84.388672 10.900391 L 80.025391 0.58203125 L 75.546875 10.900391 L 64 12.099609 z M 96 12.099609 L 104.82227 19.849609 L 102.12695 31.382812 L 112.02148 25.294922 L 121.91406 31.382812 L 119.26562 19.849609 L 128 12.099609 L 116.38867 10.900391 L 112.02539 0.58203125 L 107.54688 10.900391 L 96 12.099609 z M 128 12.099609 L 136.82227 19.849609 L 134.12695 31.382812 L 144.02148 25.294922 L 153.91406 31.382812 L 151.26562 19.849609 L 160 12.099609 L 148.38867 10.900391 L 144.02539 0.58203125 L 139.54688 10.900391 L 128 12.099609 z' />"    // the actual vector images of 5 stars
                . "</g>"
                . "<image src='" . esc_url( $sFallbackIMGSRC ) . "' />" // fallback for browsers not supporting SVG
            . "</svg>";*/

    }

}