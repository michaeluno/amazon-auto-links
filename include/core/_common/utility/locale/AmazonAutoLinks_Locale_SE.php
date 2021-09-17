<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Sweden locale class.
 *
 * @since       4.4.0
 */
class AmazonAutoLinks_Locale_SE extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string
     */
    public $sSlug = 'SE';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '46';

    /**
     * @var string
     */
    public $sDomain = 'www.amazon.se';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.se/';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAmUlEQVRIS2OUclvky8jINJuBgUEciHGCJ84xKHIye5fgU06M3Mv///+lMkq7L3lByHKQaTRwAMjYlyAH/AexNDU18Lp6d4ELirzrhD3E+BKnmuvXb4DlRh0wGgKjITDwIfC/iwFcDjCECVCUr0nWvOoDpBwYdcCAh8BoXTAaAqMhMBoCA9wofcYIbZbPAdYLYnRulj8DNsszAARqsQATyOMBAAAAAElFTkSuQmCC';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Sweden', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Sweden', 'amazon-auto-links' );
    }

    /**
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     * @see https://www.assoc-amazon.com/s/impression-counter-common.js
     * @return string
     */
    protected function _getImpressionCounterScript() {
        return '';
    }

}