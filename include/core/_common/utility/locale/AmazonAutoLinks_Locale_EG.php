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
class AmazonAutoLinks_Locale_EG extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'EG';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown
     */
    public $sLocaleNumber = '42';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.eg';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.eg/';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAaFJREFUWEftl01LAkEYx/8zO+v70hYSClKQB3s5SeE1ovwGHYIuee8LBB0iunbo2CGColufwKAX6GSBp8igoEJUEMvYFHRnd0Ilwdsqvlx2jsPM/H/zn3meeYZgxI2MWB82AHlQlIhWrdxw0wwO8zgYpXnF410h1xLNDVv8f6MNCJIkEI2OuVBomAbgOZtt6tkAtgOjd2D/6EQ4HQ5sJza6igJavgStpppzTE8Mprre1fzD47NWFPQCwD42QX/vOgRN3zL49IVliJ4BWH4H9OscpKhDQAd9NwHGYEZdMCa2YAQPLEH0DCC/roLUXiBKGqSMgFABY4aCun0Qrnno4eRgARyZBcD4Adc0OO6B+qKA8BLIXgWQxlCffRosAPtMgGpX4MU6eKmG+iSB2+uE7JRhKnHwqdPBAhC9APktDhjfnULSOPTwFYQcGCxAM3T0AqTCLmglBUEYhCcGI7BnWbyxRvsSAq3XcCkatUTer0GP6XQrD9gAtgMjd4AxluOcD7UibheljGWJ3++PlMvlW865tQzSpzhkjGVVVV2zf0a2A3+A9s72/xx+dgAAAABJRU5ErkJggg==';

    /**
     * @return string The country name.
     * @since 5.4.0
     */
    public function getName() {
        return __( 'Egypt', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Egypt', 'amazon-auto-links' );
    }

}