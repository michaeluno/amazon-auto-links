<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The UAE locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_AE extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'AE';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown. Same as U.S.
     */
    public $sLocaleNumber = '01';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.ae';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.ae';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAo0lEQVRIS2Ncxs0t/vcP0x4GRgYdBiwgNtYAmzDlYoz/rzD8++3CuJid9zIuy0G20MwBIMOBjmBczMH7H8T2EZPA6ivBTKwBQ3kI3HsDNmPUAaMhMPhDgO3WNcpTPBYTemcuIi4XjDpgNARGQ2DAQ8BRiJsm5cCFCxeIKwdGHTAaArQPAUKN0h+faZILoIaeZ4Q2y/cBW8ZaWJvltHPAeaB9ngBB+oBdtS5TjgAAAABJRU5ErkJggg==';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'United Arab Emirates', 'amazon-auto-links' );
    }


    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'United Arab Emirates', 'amazon-auto-links' );
    }

}