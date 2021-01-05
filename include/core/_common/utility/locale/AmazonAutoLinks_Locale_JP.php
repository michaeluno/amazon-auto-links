<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Japanese locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_JP extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'JP';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '09';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.co.jp';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate.amazon.co.jp';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAABvUlEQVRIS2NsnDTHl/E/42wGBgZxIKYnePmf8X8qY9PEuS8GwHKYR1+CHPAfxCtOj6On7xl6Zy4C2zfqAIpC4P/v35BgZGUlOfrIjoJ/374xfJg1jeHzulUMv+7cBlvMpqLKwBsUxiCQlsXAxMVFlGPIcsCfJ48ZnkYEMvy6ewerJWzKKgzSK9YzsMjIEnQEyQ74/+snw2MPJ4af16/iNZxdU5tBdsc+BkY2drzqSHbAxwVzGF5VlhD0GUiBWHsPA39CCnUd8DTUn+HbkYNEOYDLxp5BevVG6jrgvoEGw5+XoEKTMGARl2BQvHCDyg4w0WH48/QJYduBKlikZRgUz1yhrgOexYYxfN2ziygHcLu4MUgtXkVdB3xes5LhRW46UQ4QnzSDgS80groOYPj3j+EJMCF+P3YYr8GcVrYMMqAEyMREZQcAjfv7/h3D86QYhu8njmE1nNPCikFy7mIGZiFhgiFFcjkANxEYEp/Xr2H4tHYlw68b1yFFsYYmA19wOANvYAhBn8PMId8BBP1GnIJRBwyeEOieueT/9x8/iYs4Kqvi4uRgYDxx9sr/w6cvMHz/SV9HgCy3MdZnAACXuuyLbyizUQAAAABJRU5ErkJggg==';

    /**
     * @var string
     */
    public $sBlackCurtainURL = 'https://www.amazon.co.jp/gp/product/black-curtain-redirect.html';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Japan', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Japan', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @remark The supported locales: US, CA, FR, DE, UK, JP.
     */
    protected function _getImpressionCounterScript() {
        return <<<SCRIPT
var amazon_impression_url   = "www.assoc-amazon.jp";
var amazon_impression_campaign = '767';
var amazon_impression_ccmids =  {
    'as2'  : '4011',
    '-as2' : '4015',
    'am2'  : '4007',
    '-am2' : '4015',
    'ur2'  : '4015'
    };
document.write("<scr"+"ipt src='https://" 
    + amazon_impression_url 
    + "/s/impression-counter-common.js' type='text/javascr"+"ipt'></scr"+"ipt>");
SCRIPT;
    }

}