<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Chinese locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_CN extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string
     */
    public $sSlug = 'CN';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '28';

    /**
     * @var string
     */
    public $sDomain = 'www.amazon.cn';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://associates.amazon.cn/';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAACXUlEQVRIS+1Wy4oTQRQ9Vd2dju1E4yAjDLgS3PsJgpvRvQsfCAqCC3HvRhAE0Q9w5eBC3Yi4MTDoB/gNLkVUFESdmUxPP6rKezqdTMwDklDMbLxw012P1D333Ee1ehcEVwtn13PnQuyjNJQqI6Wvq45WxX4b7/tJEOqNguPEhfaxgf/WOVgZ8amUgpb3QJ4Ubnb1Osdc457e6uzS+f2r2jwGgIcXosIKgkshilcl4lIhIhBRguJ6KUoJZa6/Nrt5YAzAeWGgbzy1FrsrDqsbh/HzQQbz2iDRGkJZBYDgkudxZS+9kiGWNQKZR8YArB1tw/BAMb5pDFr3YyxfbCD7ZPF5bRstBBUIUk7vKwBiM73sEUCeOIQPI8RnxwvCZWLsSQ73TGCWPRAMQlCHgQzMkwsTGWBsN62Bvhli5U6z8pCy9aHAn3sZlr5oNGsWTJ0HZI3bmAfz5MLEHCiE/i3qqsWp960BgK+PU5RPS7SDoALAiiADmew9tGAuTKwCMtCV+KtbIZZvx8i/WUTHNey2w49zXbSyAHFdDf8AWCAXpgLYEa+OdJrY/Wjw/W6KxkmNE48S5G9LuHVTASDlBED1GoIqrqfl9DMa6ctir7k0FJJrEdyLEjrb6wVek5CdkDVuxD1rpQOyy4m3NEJg7IyakzIxKEOffYAA2Igo/KUtlhWNMzeo1RwBiS757gPDd8FwRxvN9q50PtZ+D6rHVjwTAHF/RzofWzLDQ/F+GQ17z3dfl8/ouVNvw9GNvq7fhQGM/tHXeGYGfBn8z8BUBg7yo1TulUId1Gc5jYdK3/gL5fiNdGKmgFMAAAAASUVORK5CYII=';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'China', 'amazon-auto-links' );
    }


    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'China', 'amazon-auto-links' );
    }

}