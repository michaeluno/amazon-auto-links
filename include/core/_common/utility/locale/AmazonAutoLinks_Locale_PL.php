<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Poland locale class.
 *
 * @since       4.5.10
 */
class AmazonAutoLinks_Locale_PL extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string
     */
    public $sSlug = 'PL';

    /**
     * Two digits locale number.
     * @var string
     * @remark unknown
     */
    public $sLocaleNumber = '01';

    /**
     * @var string
     */
    public $sDomain = 'www.amazon.pl';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.pl/';

    /**
     * @var string  32x32 png image
     * @see https://www.iconfinder.com/iconsets/142-mini-country-flags-16x16px
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAlUlEQVRIS2PcuO+I+u07D/d/+/5DkoGOgIuT47mqirwjY8+spc/obTnMnyBHMDZNnPsfJFCcHkdH/zMw9M5cBLZv1AGjITAaAqMhMPAhMJmBAVwSeqlK07Uk3Hb7KaQkHHXAaAiMhsBoCAx4CMxkZnr26+8/uraIYUUuOzPTE8aNvLzqL759PQB0hAQ9y2KQ5eJc3C4AGv2GItpRdZwAAAAASUVORK5CYII=';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Poland', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Poland', 'amazon-auto-links' );
    }

}