<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Italian locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_IT extends AmazonAutoLinks_Locale_EuropeanUnion {

    /**
     * The locale code.
     * @var string
     */
    public $sSlug = 'IT';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '29';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.it';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://programma-affiliazione.amazon.it';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAjUlEQVRIS2NkqPITZ2D4s4eBgUEHiHGCJvFgfNIYcpr5WXjVMzIwXPnFwOLCyFDldZmQ5SCTqO0AkJkgR4Ac8B/E0TfTxeviYx4tJIXAf29/vOq3790Llh91wGgIjIbAaAiMhsBoCIyGwGgIjIbAoAiBAWuUMjD8Pw8MAXCzfB+wfahFz2Y5yPLfDKyeAJLRsoJQI/N3AAAAAElFTkSuQmCC';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Italy', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Italy', 'amazon-auto-links' );
    }

}