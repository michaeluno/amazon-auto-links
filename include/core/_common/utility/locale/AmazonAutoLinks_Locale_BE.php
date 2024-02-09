<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2024 Michael Uno
 *
 */

/**
 * The Brazilian locale class.
 *
 * @since 5.4.0
 */
class AmazonAutoLinks_Locale_BE extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'BE';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown
     */
    public $sLocaleNumber = '51';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.com.be';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.com.be';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAJVJREFUWEdjZBhgwDjA9jOMOoCRl5dX/evXr/v//fsniS86TjWQFlkfGvGrZ2Fies7Lxe3IyMTE9IyQ5SCjqO0AkJkgR4DSwH8QR1ZWFq+T7234RVIQvAhgxav++pMnYPlRB4yGwGgIjIbAaAiMhsBoCIyGwGgIDHwIDHCj9AmsWX7g379/EnRulj/h5eJ2Ge0ZjYYAAClGwBAadt1dAAAAAElFTkSuQmCC';

    /**
     * @return string The country name.
     * @since  5.4.0
     */
    public function getName() {
        return __( 'Belgium', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Belgium', 'amazon-auto-links' );
    }

}