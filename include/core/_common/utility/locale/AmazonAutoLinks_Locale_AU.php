<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Australian locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_AU extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'AU';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown. Same as the U.S locale.
     */
    public $sLocaleNumber = '35';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.com.au';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.com.au';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,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';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Australia', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @remark Override it.
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Australia', 'amazon-auto-links' );
    }

}