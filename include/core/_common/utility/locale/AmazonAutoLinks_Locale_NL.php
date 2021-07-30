<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Netherlands locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_NL extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'NL';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown.
     */
    public $sLocaleNumber = '01';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.nl';

    /**
     * @var string
     * @remark Override it.
     */
    public $sAssociatesURL = 'https://partnernet.amazon.nl';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAoElEQVRIS2OcwMAg/p+Bdc9/BgYdBjoCRgaGK4wMv10Y+xlYL9Pbcpg/wY7oY2AF2s/A4CfES0f/MzBsevcZbN+oA0ZDYOBDoGni3P/sbGwMOYkRdM0FvTMXQXLBqANGQ2A0BAY8BEQs28C1oZKmJV3LgXvXj0PKgVEHjIbAgIeAqGXbQDZKzzOKmbeK/2Ni3AfMEVr0zIfAFvF5xn//PQFO/XiCv4AWHQAAAABJRU5ErkJggg==';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Netherlands', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @remark Override it.
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Netherlands', 'amazon-auto-links' );
    }

}