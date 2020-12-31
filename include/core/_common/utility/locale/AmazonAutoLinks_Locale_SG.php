<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * The Singaporean locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_SG extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'SG';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown. Same as U.S.
     */
    public $sLocaleNumber = '01';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.sg';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.sg';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAABmElEQVRIS2PcyMur/uLb1/2//v6TZKAjYGNmei7Bxe3IOJOZ6RkuyznFxBg4RUQYfn78yPD16VOqOw/kCMbJDAz/QSZ7qUpDLGBkZOCLimPgT0pnYBYUYng/sYfh87qVDBKzF4HlnoUHUMUh225DPIThAMGcQgbBvGKGPy9fMDyPDWP4/eAeWKHUyg0g5UAH+NPOAYwcHAzyxy8wMHHzMDyPj2D4fvwIg9SK9Sg+R+eT6xqsIcCmqs4gs3Uvw5/nzxge2Zth9Tm1QgKrA1jlFRhkdx9h+PfpI8NDC32G/3/+wD1ILZ/DDMSeBpiYGOT2HWdgkZJm+DB9EsO7/i6EA+iRBkC28fgGMoh1TWBgYGZm+LRkAcPb7laG/9+/kxvVOPXhzAUgHZzmVgz8KRkM7Nq6DP9//WR401TL8G3fbqo6Aq8DqGoTDsMGjwOaJs4Fl4TF6XH08Djcjt6ZwJIVVLSNOmA0BEZDYDQEBjwEemYtffbt+w+6tohhxSEXJ8cTxo37jqjfuv3gwPcfPyXoWRaDLFdVkXcBADb54CECOnhyAAAAAElFTkSuQmCC';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Singapore', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Singapore', 'amazon-auto-links' );
    }

}