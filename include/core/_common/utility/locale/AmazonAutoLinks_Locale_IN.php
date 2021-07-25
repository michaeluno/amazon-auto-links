<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Indian locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_IN extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'IN';

    /**
     * Two digits locale number.
     * @var    string
     * @remark Unknown
     */
    public $sLocaleNumber = '01';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.in';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.in';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAB7UlEQVRIS2O8mCmi/vLRh/2/fv6RZKAjYGNneS4uJ+DIuMub5Rm9LYf5E+QIxi0uDP9BAt6hhnT0PwPD1tXnwfaNOmA0BCgKgb//mBkYGf8zMDH+IzkBwxNh08S5/9nZ2BhyEiOINuT18+8M8/uvM1w+/Qasx8hKlCGxSItBQJidaDN6Zy6C5AJSHQCyvDr1OMP3r38YmJgYGP4DMzEIi0pyMrTNtWLg4GQmyhFkO6Cr7BzY5yoGnAy2gXwMf//8Z9i38iPDk1s/GWw9pBjSynVo5wCQZQmuu8E+j68XZ2BhYwRb9untX4aVPa8ZxKW5GHqW2NDQAX+BDnDZzcAIdEBstRgDOxeQAQTvnv9hWDvpDYOcMi9D6xxL2jkAZHJ/9XmGc8deM8iosTNY+/Ex/Pn1n+Hw+o8Mrx7/ZnAJkGWIz9ekrQM+vP3J0JR7igGUGJGBgiofQ/MsC6IsBykiOxGCNP/4/pdh0aTrDLcuf2Bg52BmUNMVINrnMBfCHaDZygWuDc2tzIh2PTUUnjx2ClIOjDpgRIbAn99/GM6ePsfAzAyszOgdAiDLH9x/yPD2zVsGQSFBBkbdTr5nf/7Qt0UMSv0sLCwM2rpalxgd5yqqv3n79gDQZRLUyF6EzAAFOz8/H4OsvOwlHjZuNwDlefHXDH776gAAAABJRU5ErkJggg==';

    /**
     * @var string
     * @since 4.6.9
     */
    public $sAdSystemServer = 'ws-in.amazon-adsystem.com';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'India', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'India', 'amazon-auto-links' );
    }

}