<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The German locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_DE extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'DE';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '03';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.de';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://partnernet.amazon.de';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAkUlEQVRIS2NkYGAQB+I9QKwDxPQEV4CWuTACicsDYDnMo1dADvgP4qmrq9PT9ww3b94E2zfqgNEQGPgQ2MXI8J+ZiYlBTUqKrrng+pMnkFww6oDREBgNgQEPgVMNkNrQNECCruXA6Q0vIOXAqANGQ2AwhMDANUoZGc4znmhlEGf6zbAPmCO06JoPgZb/Y2HwBABVcmoAZk3aWwAAAABJRU5ErkJggg==';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Germany', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Germany', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     */
    protected function _getImpressionCounterScript() {
        return <<<SCRIPT
var amazon_impression_url   = "www.assoc-amazon.de";
var amazon_impression_campaign = '2514';
var amazon_impression_ccmids =  {
    'as2'  : '9398',
    '-as2' : '9494',
    'am2'  : '9394',
    '-am2' : '9494',
    'ur2'  : '9494'
    };
document.write("<scr"+"ipt src='https://" 
    + amazon_impression_url 
    + "/s/impression-counter-common.js' type='text/javascr"+"ipt'></scr"+"ipt>");
SCRIPT;
    }

}