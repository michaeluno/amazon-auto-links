<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Canadian locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_CA extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'CA';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '01';

    /**
     * @var string
     */
    public $sDomain = 'www.amazon.ca';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://associates.amazon.ca/';

    /**
     * @var string
     * @remark Override it.
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAB+UlEQVRIS2NcxsDg+5+BYTYDA4M4EGOAuxPnYhMmWkw5PxmX2peMDAypjEsZGF7gshykk4YOABn/EuQAYAAwMEThcud/sDT5gBHoTywAGPJgMOqAoRsCvx4/Znjb28HApW/AwGluxcAiKcnAJCiEGdu0SAMPstIYvmxaz8DKygLErGBapLWLgTMghD4O+H75EsNdLxcGVhZmsAM4ZGUYJHccZGAEsjEA1UMAmC3///7FcF1blYH5z2+wA7iNjBnEV6zHnlWp5YAv588xvN+xjeE3MO6ZgT7/vG41Igo4ORiESioZ2FTVGBi+fmFg+PSRgdU3iIGBhQWY0alUDtwpyGN4NXc2Aws43pExJA2AMEiOBeg4npZuBhZnD2hJQyUHXIsMZ/iwZRNRDmDT1mPgmjqPgYGdnXoh8OPhQ4bbSfEM38+fxRsCbApKDNz1bQzMOnrUDQGQaf9//2Z40trE8Gb6VKAjQDkAkQ1BbJ7waAbuglIGRk4uRIKkVhpATuK3vN0Zfl08h+IADiVlBtHNu+mQDYFWfNy5nYHx3z+Gd1MmMPx/eJ9BKDmNgZmbm4EnLZs+DoDZ8qKsiOH3pfMMsjv2466uaREFMNs+b93M8OfeHQbB3MKBcQBRrRRahsCwccBANkqfMUKb5XOAwSmGLUhp2Cp+BqwlMgD289zmzxe0NwAAAABJRU5ErkJggg==';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Canada', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Canada', 'amazon-auto-links' );
    }

    /**
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     * @see https://www.assoc-amazon.com/s/impression-counter-common.js
     * @return string
     */
    protected function _getImpressionCounterScript() {
        return <<<SCRIPT
var amazon_impression_url   = "www.assoc-amazon.ca";
var amazon_impression_campaign = '212553';
var amazon_impression_ccmids =  {
    'as2'  : '381317',
    '-as2' : '381337',
    'am2'  : '381313',
    '-am2' : '381337',
    'ur2'  : '381337'
    };
document.write("<scr"+"ipt src='https://" 
    + amazon_impression_url 
    + "/s/impression-counter-common.js' type='text/javascript'></scr"+"ipt>");             
SCRIPT;
    }

}