<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * The UK locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_UK extends AmazonAutoLinks_Locale_EuropeanUnion {

    /**
     * The locale code.
     * @var string
     */
    public $sSlug = 'UK';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '02';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.co.uk';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.co.uk';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAFMElEQVRIS+1WaVCVVRh+voW73y6LsoNLajAy1hh6oZn8Y2aN4eTkUmbiVogiZkrQAhoGpAWyFCMqOLgVNTQp+sOwaXQmldQxGRodQlyIAJeBu3Lx3u/7Ot/5uFcISqkf9aPz5zvnfO/ynOe8531fZj4Sc6eg8T0DHCzIsJdUyh/U7AZ+brpD5wNHbskoujSsXzlo36uXs/6+jk7HI6vAf5CcV88OvdiIKXlMLvSC1/lAACsXLkTC9P1oa7P9LQAcx6D263lovH5qWADUFwHBFAGSvFieEAcmOBT67Dzw8dOpUnePB4sWtcJqEXxG6usnwWjk0BUbC9FiofusyYSQy5dhswmYNauZ7pWWRWFavB4Mw0C8fQvOD7Ph+fG0z87es0107gOQPH2y8pMoaBYnQ7s6HYxKBatVQEZGGy5d6qW/HwQgKakFO3dGIyZGS+XvfV8PR/4WSFYFLDge/PzFqNyYqbjLLamkDKyXenCvZh/gvkd/8BMfgyF3G/gJkyAIEmpru1FU1PWXAHp7RXR3exAeroLksMP+ST76jn7jOzVnfhrq9Eyw0WMhigIOH265DyDjjWUQ29vgKtsOz6nvFHRqNfRrN0D3ajIEETh50gaz2QC9nh10BVxEBILPnYNEjkIIhPunC7BmZ0Jo/5XaYceMg4Y45hNn0PXFi04UFBxHXd1VEPFt0uzZ45CT8zymTtVRAfmunMUFEFt/oWuVOREmwoYUFAI5uGQnA2OAi4pCcEMDiSYJtrIiOKr3gNAGxmCEZsUaqBcsAfz80NnpxtGjFhw4cBcNDXXKIWUA8sRsTiIBF4ikJH9ERvpRA67aQ+itKIVos4B9xAT/9z+A9rkXqOJwAAQSbJ0znyIAWajnvgRt6ltgA4Oo/LFjFpSX34LDQagkwwfAGwPZ6St8d/Uwk2EZeBjFfpmtpVUKA/86AG8eeDksbAT4/7noFx0dCgP/A/AysDgmZkS8ilYrfXZ0sCxYo3FE+oeuXPmPBKH3Fby5PJki4nkGajVLkw0kEZLTST4kqajUYDUa3ymHe4aSxw3RqdQMVktqAU/yCRkejwSXS3n/3lG8t1phYGAiysgIxZw5JvrDdvwYOjatg2w0esenCHhxHt2XSZex/VkecF1twbVlS+BqbkbgqlQEv7MZDM/jzBkHCgs7ceeOh9oZlAnDwvQ4eHApEhP1kNxudOXl4G5lBdRjxmJ89QHoJsfR677Z5sKoINXQWuBNxf3HE+x2XF+Xip66I9A+OQ1R5VXwi4hEV5cHxcVdBIx9aCaUi5GbFI8bry+FkxQT0zOz8OiuKvD+/qQQSfi23oKP8jtJLp84pB/gwsMRfP48LKR0azTkClUsASyhY0ch2gu2gjOaEFW2C8aZz1KIcj1YsIC0XDKb3hhIGRuCm+kp8PT0IGLjJkS+mw2G49DnFvHxduK4TqnnD+oHUlJu4LPyaAT481S+50Q9WlYtp3ZD0jYgNCuHXAmHI0eakZZ24n4iMhu14Ax6TKioRFDSXEXZ4sGa1Jtobe3zBc+DAMgdkU7HIi8/nJZulkSz6/o1XHllIZxNTTCQyjpu9z7sGR+lMODNAzOeiEPs519CFxNL6Tt3wYGst9vhdA6O3ocB4EW75LVArE4ZTV4WC8HhQMvaVNz+qgZ+o0fj9I32/iv4Q1O6zi1g85YfkJ93xnfqgZORdMWyXnx8KOpPLEJZ9UFqZmA3bYdRYOS2/HHSluv72/KahP1oOPvbsM7lzZECkHUCAjTYsMUwCIDsvBFx+b8DctqHmw6AaZ4AAAAASUVORK5CYII=';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'United Kingdom', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'United Kingdom', 'amazon-auto-links' );
    }

    /* Returns the ISO 3166 country code.
     * Mostly the same as the slug. But the UK locale will be GB.
     * @see https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
     * @since  4.6.9
     * @return string
     */
    public function getCountryCode() {
        return 'GB';
    }

    /**
     * @return string
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     * @see https://ir-na.amazon-adsystem.com/s/impression-counter?o=2
     */
    protected function _getImpressionCounterScript() {
        return <<<SCRIPT
var amazon_impression_url   = "www.assoc-amazon.co.uk";
var amazon_impression_campaign = '2506';
var amazon_impression_ccmids =  {
    'as2'  : '9310',
    '-as2' : '9490',
    'am2'  : '9306',
    '-am2' : '9490',
    'ur2'  : '9490'
    };
document.write("<scr"+"ipt src='https://" 
    + amazon_impression_url 
    + "/s/impression-counter-common.js' type='text/javascr"+"ipt'></scr"+"ipt>");
SCRIPT;
    }

}