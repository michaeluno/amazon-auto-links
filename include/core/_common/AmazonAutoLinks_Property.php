<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Properties of Amazon store info.
 * 
 * @package     Amazon Auto Links
 * @since       2.0.0
 * @since       3       Changed the name from `AmazonAutoLinks_Properties`.
*/
final class AmazonAutoLinks_Property {
    
    /**
     * 
     * @see                http://php.net/manual/en/function.mb-language.php
     */
    static public $aCategoryPageMBLanguages = array(
        'CA'    => 'uni',
        'CN'    => 'uni',
        'FR'    => 'uni',
        'DE'    => 'uni',
        'IT'    => 'uni',
        'JP'    => 'ja',
        'UK'    => 'en',
        'ES'    => 'uni',
        'US'    => 'en',    
        'IN'    => 'uni',    
        'BR'    => 'uni',
        'MX'    => 'uni',
        'AU'    => 'uni',   // 3.5.5+
    );
    /**
     * @var array
     * @since   2?
     * @since   3.8.12      Changed the scheme to https from http.
     * @deprecated   3.9.1
     */
    static public $aCategoryRootURLs = array(
        'CA'    => 'https://www.amazon.ca/gp/bestsellers/',
        'CN'    => 'https://www.amazon.cn/gp/bestsellers/',
        'FR'    => 'https://www.amazon.fr/gp/bestsellers/',
        'DE'    => 'https://www.amazon.de/gp/bestsellers/',
        'IT'    => 'https://www.amazon.it/gp/bestsellers/',
        'JP'    => 'https://www.amazon.co.jp/gp/bestsellers/',
        'UK'    => 'https://www.amazon.co.uk/gp/bestsellers/',
        'ES'    => 'https://www.amazon.es/gp/bestsellers/',
        'US'    => 'https://www.amazon.com/gp/bestsellers/',
        'IN'    => 'https://www.amazon.in/gp/bestsellers/',
        'BR'    => 'https://www.amazon.com.br/gp/bestsellers/',
        'MX'    => 'https://www.amazon.com.mx/gp/bestsellers/',
        'AU'    => 'https://www.amazon.com.au/gp/bestsellers/',
    );

    /**
     * @var array 
     * @since   3.8.12
     */
    static public $aStoreDomains = array(
        'CA'    => 'www.amazon.ca',
        'CN'    => 'www.amazon.cn',
        'FR'    => 'www.amazon.fr',
        'DE'    => 'www.amazon.de',
        'IT'    => 'www.amazon.it',
        'JP'    => 'www.amazon.co.jp',
        'UK'    => 'www.amazon.co.uk',
        'ES'    => 'www.amazon.es',
        'US'    => 'www.amazon.com',
        'IN'    => 'www.amazon.in',
        'BR'    => 'www.amazon.com.br',
        'MX'    => 'www.amazon.com.mx',
        'AU'    => 'www.amazon.com.au',
        'TR'    => 'www.amazon.com.tr',
        'AE'    => 'www.amazon.ae',
        'SG'    => 'www.amazon.sg', // 3.10.1
    );    
    /**
     * Returns the market place domain url by the given locale.
     * 
     * @since       3.8.12
     * @return      string the store domain including the URL scheme (https://).
     */
    static public function getStoreDomainByLocale( $sLocale, $bPrefixScheme=true ) {
        $_sLocale = strtoupper( $sLocale );
        $_sScheme = $bPrefixScheme ? 'https://' : '';
        return isset( self::$aStoreDomains[ $_sLocale ] )
            ? $_sScheme . self::$aStoreDomains[ $_sLocale ]
            : $_sScheme . self::$aStoreDomains[ 'US' ];    // default
    }

    /**
     * @return      string      The locale key.
     */
    static public function getLocaleByDomain( $sDomain ) {
        $sDomain = untrailingslashit( $sDomain );
        $sDomain = str_replace( 'www.', '', $sDomain );
        foreach( self::$aStoreDomains as $_sLocale => $_sStoreDomain ) {
            $_sStoreDomain = str_replace( 'www.', '', $_sStoreDomain );
            if ( $_sStoreDomain === $sDomain ) {
                return $_sLocale;
            }
        }
        return 'US';    // not found: default
    }

    /**
     * @var array Base 64 images of country flags.
     * @since   4.0.0
     * @see https://www.iconfinder.com/iconsets/142-mini-country-flags-16x16px
     */
    static public $aCountryFlags = array(
        'CA'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAB+UlEQVRIS2NcxsDg+5+BYTYDA4M4EGOAuxPnYhMmWkw5PxmX2peMDAypjEsZGF7gshykk4YOABn/EuQAYAAwMEThcud/sDT5gBHoTywAGPJgMOqAoRsCvx4/Znjb28HApW/AwGluxcAiKcnAJCiEGdu0SAMPstIYvmxaz8DKygLErGBapLWLgTMghD4O+H75EsNdLxcGVhZmsAM4ZGUYJHccZGAEsjEA1UMAmC3///7FcF1blYH5z2+wA7iNjBnEV6zHnlWp5YAv588xvN+xjeE3MO6ZgT7/vG41Igo4ORiESioZ2FTVGBi+fmFg+PSRgdU3iIGBhQWY0alUDtwpyGN4NXc2Aws43pExJA2AMEiOBeg4npZuBhZnD2hJQyUHXIsMZ/iwZRNRDmDT1mPgmjqPgYGdnXoh8OPhQ4bbSfEM38+fxRsCbApKDNz1bQzMOnrUDQGQaf9//2Z40trE8Gb6VKAjQDkAkQ1BbJ7waAbuglIGRk4uRIKkVhpATuK3vN0Zfl08h+IADiVlBtHNu+mQDYFWfNy5nYHx3z+Gd1MmMPx/eJ9BKDmNgZmbm4EnLZs+DoDZ8qKsiOH3pfMMsjv2466uaREFMNs+b93M8OfeHQbB3MKBcQBRrRRahsCwccBANkqfMUKb5XOAwSmGLUhp2Cp+BqwlMgD289zmzxe0NwAAAABJRU5ErkJggg==',
        'CN'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAACXUlEQVRIS+1Wy4oTQRQ9Vd2dju1E4yAjDLgS3PsJgpvRvQsfCAqCC3HvRhAE0Q9w5eBC3Yi4MTDoB/gNLkVUFESdmUxPP6rKezqdTMwDklDMbLxw012P1D333Ee1ehcEVwtn13PnQuyjNJQqI6Wvq45WxX4b7/tJEOqNguPEhfaxgf/WOVgZ8amUgpb3QJ4Ubnb1Osdc457e6uzS+f2r2jwGgIcXosIKgkshilcl4lIhIhBRguJ6KUoJZa6/Nrt5YAzAeWGgbzy1FrsrDqsbh/HzQQbz2iDRGkJZBYDgkudxZS+9kiGWNQKZR8YArB1tw/BAMb5pDFr3YyxfbCD7ZPF5bRstBBUIUk7vKwBiM73sEUCeOIQPI8RnxwvCZWLsSQ73TGCWPRAMQlCHgQzMkwsTGWBsN62Bvhli5U6z8pCy9aHAn3sZlr5oNGsWTJ0HZI3bmAfz5MLEHCiE/i3qqsWp960BgK+PU5RPS7SDoALAiiADmew9tGAuTKwCMtCV+KtbIZZvx8i/WUTHNey2w49zXbSyAHFdDf8AWCAXpgLYEa+OdJrY/Wjw/W6KxkmNE48S5G9LuHVTASDlBED1GoIqrqfl9DMa6ctir7k0FJJrEdyLEjrb6wVek5CdkDVuxD1rpQOyy4m3NEJg7IyakzIxKEOffYAA2Igo/KUtlhWNMzeo1RwBiS757gPDd8FwRxvN9q50PtZ+D6rHVjwTAHF/RzofWzLDQ/F+GQ17z3dfl8/ouVNvw9GNvq7fhQGM/tHXeGYGfBn8z8BUBg7yo1TulUId1Gc5jYdK3/gL5fiNdGKmgFMAAAAASUVORK5CYII=',
        'FR'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAjUlEQVRIS2N01pktzszMvIeBgUEHiHECm6T/+KQx5JQrc/GqZ2RguMLAyOjC6KY/7zIhy0EmUdsBIDNBjgA5AOw1BeEQvC7u38ZJUgh8UFXCq/7A27dg+VEHjIbAaAiMhsBoCIyGwGgIjIbAaAgMihAYsEYpsEl4nhHULGdiZt4HbKFq0bNZDrackdETAIE6sTNLga3zAAAAAElFTkSuQmCC',
        'DE'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAkUlEQVRIS2NkYGAQB+I9QKwDxPQEV4CWuTACicsDYDnMo1dADvgP4qmrq9PT9ww3b94E2zfqgNEQGPgQ2MXI8J+ZiYlBTUqKrrng+pMnkFww6oDREBgNgQEPgVMNkNrQNECCruXA6Q0vIOXAqANGQ2AwhMDANUoZGc4znmhlEGf6zbAPmCO06JoPgZb/Y2HwBABVcmoAZk3aWwAAAABJRU5ErkJggg==',
        'IT'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAjUlEQVRIS2NkqPITZ2D4s4eBgUEHiHGCJvFgfNIYcpr5WXjVMzIwXPnFwOLCyFDldZmQ5SCTqO0AkJkgR4Ac8B/E0TfTxeviYx4tJIXAf29/vOq3790Llh91wGgIjIbAaAiMhsBoCIyGwGgIjIbAoAiBAWuUMjD8Pw8MAXCzfB+wfahFz2Y5yPLfDKyeAJLRsoJQI/N3AAAAAElFTkSuQmCC',
        'JP'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAABvUlEQVRIS2NsnDTHl/E/42wGBgZxIKYnePmf8X8qY9PEuS8GwHKYR1+CHPAfxCtOj6On7xl6Zy4C2zfqAIpC4P/v35BgZGUlOfrIjoJ/374xfJg1jeHzulUMv+7cBlvMpqLKwBsUxiCQlsXAxMVFlGPIcsCfJ48ZnkYEMvy6ewerJWzKKgzSK9YzsMjIEnQEyQ74/+snw2MPJ4af16/iNZxdU5tBdsc+BkY2drzqSHbAxwVzGF5VlhD0GUiBWHsPA39CCnUd8DTUn+HbkYNEOYDLxp5BevVG6jrgvoEGw5+XoEKTMGARl2BQvHCDyg4w0WH48/QJYduBKlikZRgUz1yhrgOexYYxfN2ziygHcLu4MUgtXkVdB3xes5LhRW46UQ4QnzSDgS80groOYPj3j+EJMCF+P3YYr8GcVrYMMqAEyMREZQcAjfv7/h3D86QYhu8njmE1nNPCikFy7mIGZiFhgiFFcjkANxEYEp/Xr2H4tHYlw68b1yFFsYYmA19wOANvYAhBn8PMId8BBP1GnIJRBwyeEOieueT/9x8/iYs4Kqvi4uRgYDxx9sr/w6cvMHz/SV9HgCy3MdZnAACXuuyLbyizUQAAAABJRU5ErkJggg==',
        'UK'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAFMElEQVRIS+1WaVCVVRh+voW73y6LsoNLajAy1hh6oZn8Y2aN4eTkUmbiVogiZkrQAhoGpAWyFCMqOLgVNTQp+sOwaXQmldQxGRodQlyIAJeBu3Lx3u/7Ot/5uFcISqkf9aPz5zvnfO/ynOe8531fZj4Sc6eg8T0DHCzIsJdUyh/U7AZ+brpD5wNHbskoujSsXzlo36uXs/6+jk7HI6vAf5CcV88OvdiIKXlMLvSC1/lAACsXLkTC9P1oa7P9LQAcx6D263lovH5qWADUFwHBFAGSvFieEAcmOBT67Dzw8dOpUnePB4sWtcJqEXxG6usnwWjk0BUbC9FiofusyYSQy5dhswmYNauZ7pWWRWFavB4Mw0C8fQvOD7Ph+fG0z87es0107gOQPH2y8pMoaBYnQ7s6HYxKBatVQEZGGy5d6qW/HwQgKakFO3dGIyZGS+XvfV8PR/4WSFYFLDge/PzFqNyYqbjLLamkDKyXenCvZh/gvkd/8BMfgyF3G/gJkyAIEmpru1FU1PWXAHp7RXR3exAeroLksMP+ST76jn7jOzVnfhrq9Eyw0WMhigIOH265DyDjjWUQ29vgKtsOz6nvFHRqNfRrN0D3ajIEETh50gaz2QC9nh10BVxEBILPnYNEjkIIhPunC7BmZ0Jo/5XaYceMg4Y45hNn0PXFi04UFBxHXd1VEPFt0uzZ45CT8zymTtVRAfmunMUFEFt/oWuVOREmwoYUFAI5uGQnA2OAi4pCcEMDiSYJtrIiOKr3gNAGxmCEZsUaqBcsAfz80NnpxtGjFhw4cBcNDXXKIWUA8sRsTiIBF4ikJH9ERvpRA67aQ+itKIVos4B9xAT/9z+A9rkXqOJwAAQSbJ0znyIAWajnvgRt6ltgA4Oo/LFjFpSX34LDQagkwwfAGwPZ6St8d/Uwk2EZeBjFfpmtpVUKA/86AG8eeDksbAT4/7noFx0dCgP/A/AysDgmZkS8ilYrfXZ0sCxYo3FE+oeuXPmPBKH3Fby5PJki4nkGajVLkw0kEZLTST4kqajUYDUa3ymHe4aSxw3RqdQMVktqAU/yCRkejwSXS3n/3lG8t1phYGAiysgIxZw5JvrDdvwYOjatg2w0esenCHhxHt2XSZex/VkecF1twbVlS+BqbkbgqlQEv7MZDM/jzBkHCgs7ceeOh9oZlAnDwvQ4eHApEhP1kNxudOXl4G5lBdRjxmJ89QHoJsfR677Z5sKoINXQWuBNxf3HE+x2XF+Xip66I9A+OQ1R5VXwi4hEV5cHxcVdBIx9aCaUi5GbFI8bry+FkxQT0zOz8OiuKvD+/qQQSfi23oKP8jtJLp84pB/gwsMRfP48LKR0azTkClUsASyhY0ch2gu2gjOaEFW2C8aZz1KIcj1YsIC0XDKb3hhIGRuCm+kp8PT0IGLjJkS+mw2G49DnFvHxduK4TqnnD+oHUlJu4LPyaAT481S+50Q9WlYtp3ZD0jYgNCuHXAmHI0eakZZ24n4iMhu14Ax6TKioRFDSXEXZ4sGa1Jtobe3zBc+DAMgdkU7HIi8/nJZulkSz6/o1XHllIZxNTTCQyjpu9z7sGR+lMODNAzOeiEPs519CFxNL6Tt3wYGst9vhdA6O3ocB4EW75LVArE4ZTV4WC8HhQMvaVNz+qgZ+o0fj9I32/iv4Q1O6zi1g85YfkJ93xnfqgZORdMWyXnx8KOpPLEJZ9UFqZmA3bYdRYOS2/HHSluv72/KahP1oOPvbsM7lzZECkHUCAjTYsMUwCIDsvBFx+b8DctqHmw6AaZ4AAAAASUVORK5CYII=',
        'ES'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAACgklEQVRIS+2WXUhTYRzGf2dnburWUGbOTJYtS4UuQguJLrowyoLoIij6vLOCyAstIi+iiMDwoyAKog+EIgQLQUu7yIGWhtnE0qY1JPJ76azmbDXd1tlSITC2E6I3/uFcHHjP8/z+7/u873mFYpHdCrgNGKRnIcvug1yhVGR4EcxnGrUHAPyBt/z16QvZPWWdXUG/JYDFn4HXF/CLEUoy9mz6KwN+KaJDY1r0OjdqlZcPNj8Pq7Us0wqcPu4Kju2161AxRoJBKTs/rY9e/cnAvwCcLiWt3SsZdmhYoXdR/6QdzfIUEpMTWZXgZHQMJjw6YnU/yc78jDZ6UhZESIAZtR7HZmx1I2Tcf8rIaiOmZw10tLyku60GhVdg3/a3qFTBjSSrwgLwScswlVKHtSCP1RWPmYyOIq5/hF/uCcxVN9l58Aw+2xEUnh5Z5oHBIQF8PoFqcyw5uRVYm56TbuvD6XGjvFqGTadl4NRR9h47T3NVIVlrGhFFiVZGhQSw9cbQ1JHE/pOl2Ps/kpyaifvWDTznzuKJUmMpKSTnQAHV5ZdIM7SwziiFQkaFBOjsiaPo8nvuNXZgtZhxOoYZ/zLA6LW7xGRvRJ21JQhwJe8QO7Y62JDmlGEfxhIM2QWKr3spqjBjfVNPe1NN0MA75UVUisQnrWWXlIHaBxfJMtai14vzCxBQe9cVgWlbJY21lXzqasYvhV2r09D2og1jSir5JeV8tRxGr+mTZR5WCGcVhQg8Hi/fXZE0WAykm8YxJX4jUu1FUEgHkF/e/p/RDZmBuVpy/VBJB45HdrdzffBfAPPiPC0yC7B0H1iagUW+lA4K09fyO1I44+cz5WFoDUq/rxO/AdFXJ6/Zeh0GAAAAAElFTkSuQmCC',
        'US'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAWCAYAAAChWZ5EAAABSklEQVRIS2PMFlaJ3SwbPO8dqwgLAwgwM4Ephr//IDQRfCYONobqI7kQ9cSDl///M6QyyhmU/37LKAixnEzAyM7KUHummBzdLxl5TLv////zl2HnvBQGRkYGhpiGC2CDFtcbEM1nZmNhODXXgiQHTJm3FKwe7oBd84EOAApEQx2wpAHoACL5zKxAB8wj1wEWvf////zNICZvQrLPYV5m5WZn2HYqkqQQWHv/CTQE0BxAis9hNrIBHbCVbAeMpoHRNDCaBtDSAEmZGaqYonKAz2Hy/38/fjFIyBkzMPxnYGBiYwaXgH9//yWaz87LwbB+byBJbocXRE0T5/5nZ2djSI0OJckAShXD64IBd0AnCyjgGRiCFWUo9RRJ+uFRMOAOGPAoGHAHAKPgBTDyxEmKQOopfsbYwczgC2yKzQGaKUY9c4ky6RmwUZoBAO2BIfPVOVScAAAAAElFTkSuQmCC',
        'IN'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAB7UlEQVRIS2O8mCmi/vLRh/2/fv6RZKAjYGNneS4uJ+DIuMub5Rm9LYf5E+QIxi0uDP9BAt6hhnT0PwPD1tXnwfaNOmA0BCgKgb//mBkYGf8zMDH+IzkBwxNh08S5/9nZ2BhyEiOINuT18+8M8/uvM1w+/Qasx8hKlCGxSItBQJidaDN6Zy6C5AJSHQCyvDr1OMP3r38YmJgYGP4DMzEIi0pyMrTNtWLg4GQmyhFkO6Cr7BzY5yoGnAy2gXwMf//8Z9i38iPDk1s/GWw9pBjSynVo5wCQZQmuu8E+j68XZ2BhYwRb9untX4aVPa8ZxKW5GHqW2NDQAX+BDnDZzcAIdEBstRgDOxeQAQTvnv9hWDvpDYOcMi9D6xxL2jkAZHJ/9XmGc8deM8iosTNY+/Ex/Pn1n+Hw+o8Mrx7/ZnAJkGWIz9ekrQM+vP3J0JR7igGUGJGBgiofQ/MsC6IsBykiOxGCNP/4/pdh0aTrDLcuf2Bg52BmUNMVINrnMBfCHaDZygWuDc2tzIh2PTUUnjx2ClIOjDpgRIbAn99/GM6ePsfAzAyszOgdAiDLH9x/yPD2zVsGQSFBBkbdTr5nf/7Qt0UMSv0sLCwM2rpalxgd5yqqv3n79gDQZRLUyF6EzAAFOz8/H4OsvOwlHjZuNwDlefHXDH776gAAAABJRU5ErkJggg==',
        'BR'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAExElEQVRIS71Wa2wUZRQ9s7uz7+52u7RbC7S2sViIqEBtJDWiVn8ZfvhIU40KGpsAgahRo8T4IsFUCMGg8REBIdQfBgli/EeVqKlFQyugaAiiWbpdut1Hu+/ZnZ0Z7ze7Lftq2aLhSybfZHdm7rnn3Hvux+GRhrXguE8BzoXruhQfFKWXw6MLx689uJKDzF0jdMVHABZlv/JApQTQ47IMSBna6QIF12oBjY4u2itdx33qk/MDoLDgFDSdAIQYXJoMEpKMqMYIGK0Ab8qC4CpgZN4AZAnIpIFUDIgF8dACIz5Z04poWsJTJ85jkDDBUgMYCIiOJxCaubmoGMB01qIAJKZgE0LYs7IZ65bVzgSQZAU7hr14808vROsCwGQjNogVVZJZ2KgIgEJaq1nH1azvsXA4sKYFTXZD2exGfHE8+cNF/CFTcIuD2LBQfVBtlGNjTgBq1kQ5yzoZhjERxPalLjx3W11WXla2tLN7rihDISPjlaExvD8agaKyUUWSEGANkySPjVkBsKwlMZt1PIQVujQO3dWINoceKVFCQsjgL78LewbX47RvFUwGHTqbf8OLdx9Ek8M7w8xxdwxP/zyKMR3JYWZssAJltZEDUQqgLpd1irKOQBsLYGuLDW+014LXcGrwyUgKp9yL8PZAH4ZPK1AsepjsRtir9FjoEHD4iZdwY83lmTxDgoRNg5fxRZA6p4rYUAtUn62NEgBdVFRikrKeRKscwsGOeqyuJ9S5lRBEeANxhBf/guWtN8DtTmBf/xj2fxOAn+PBO83oXjmMfd3vgNdpoclrxc8vRLD5bBBTJophrqYCJUkG/EU+cJ8TnBDBhpowdi63wcIXVm8ylYEvlMRbPw1gXZcLd7bTh2il0zK+PDqO9w778Y+UxujrPbARM1pt4fuXYhmsH4nihOgkz6C6+DZQCoBPhbGtIYKXl5hR9D5EKq5oQkTLu0cR9ihosyvY2F2Lnu4G6HNg+/o92NLWiTqHiVgo9AFWtx/9LWCL2wbZQAC+CxYB6CKNmASxEFZrp0gCO1qrqYVyS6HOyEgU9OvtOHauE6GIADmYgAsiXtvYgFtvsWJH/1fY//BWOKkudHkALkxl0Hsqgu9FO2AlBphHlDBw/3QREghqPXM8gL4lRmxeZixoNXd4MR478hn8MStixMhkNIUU7Y6qGI493ovbGydgMfLUdRwyZFC7zpFsF1MQzJSgmQCoBkWJDUzMMgvUQUPmQ17P2vBeYxIHOsxorLqi6aVwE3adfAEnPXcgTdKscg3h+Y7duMnpUanXUvBfgxKeHU5iRCIzUi2amRLrgJw0VzciNnSybNiSfuy+WY9nWqmPS9b0SM7+QZ2HbWfT2OkhE7UU2XL+kKrIiosG0IOWJPa286g3l/f3H8dl9J4RcZ4jqpkV6ynr2QZTRQBYSjMjmNigYeQU/PhgqRY9zVeqPEKKvXpGwscTPNkvFRkbRqr9zjGaKwagEps7hLDBJETVwdRdncSHKzgMBRRs+l3BqI50ZiZjMJPW/+c4ztecscHmBGtXmhU2RUBEoixZUD1dzOHmGsH535ofAwUoskcydjJiOyssFlSl+yqHkPIA/suhtExTzO8nL5c9lmv20nvkRNd1eaHIG/4FX4sKA6uY5z8AAAAASUVORK5CYII=',
        'MX'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAB/klEQVRIS2NkyGcQZ2Rh2MPAwKADxDhBo9xcfNIYcpr5WXjVMzIwXPnFwOLCyFjMcJmQ5SCTqO0AkJkgR4Ac8B/E0XTRwOvi044gdxIP/nv741W8fe9esPyoA4Z2CGyb6MugaurJoGqFmeLpkgYu7Sxj+PLhL4NVeC9GgqOZA64fmsHAzskIzDp8DO+f7Wf4+puFQUXLi+HD89MMjCxSDFr26WDH0MwBIMNvHJnFcHnvVAYlA3OGq4d2MmjbuQND4heDbcwsBiZmNto6YNXxgwx8Tzcy8P95xfD7508GbgFBsIUf/nIyfJQMYAiydKStA+6enMrw4cVFhlcP7zJwcPMw/PjyiYGZhZXh+d2bDJahxQxqlnm0dcDeWb4MAuKSDC/u3mDgExFn4OKHhAC4VGNiYTDynUZbB+zbVM7A//89w70LJ8FpABm8ZhBl8PBvpa0DsmZ1M2j/usYgyvmPQZz1BwMTjxDD488MDG8YBRkufBNlmJdRSFsH1K9dyXDz+VOwJRpcnxge/OBm+PGPGcw3VlBiKPUJoK0DXn/6xLDm1HGGp+/fMtx//YqBm52dQVlMgkFFXILBy9CYgZOVxtkQOc6P3b7JoCcrz8DDwUG/khDDJhwCNC0JiXHEqANICYEBa5QC68zzjNBm+T5gvGrhi1vqt4r/n//NwOoJAHNnIpGip7nBAAAAAElFTkSuQmCC',
        'AU'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAGAklEQVRIS61We0xTZxT/3Xtb2tKW8hAh2xAmqAhTkSgiOJ1uzjg1TmMcmiGLuummc2rmFoPMqXFGpqiZ2f4Y4h7iHtEthkyncTK2EXEwhzKVh7yl8rKlpaWF9t67r9+F0kJ9LO4Ecnu+x/l+3zm/c87HHNvz2e4Xy89lHp+5mAWRL05edX3w2spE+o3auoZ+B6Qh55iXPnR+W+wqr3kwIH8MRFH0HgfTBgivM83r1/K6ilL28PJNdMH/CUAml4NlOWpXFHg4HI5hIBihs120bt8KvuIanWRCR0K99wC4CQlUN02f6LVJd/k61fmKclgz34XY0U51bsIkqPflIGF1AdUdtk4IThs0/gpo1Aq0dpildXINZMogtNUWSecR14gEGqyHs9Fb8IM0KJMjIPcEuNExMCTFuwEwGi2CLpWAr6mCeV0GRKd0I8WipVBvfg8gN560OI+4m0efRY/QYA3SlyTDT87h1LmrqG3sgCsQCm3EIICvODqGdKcIvqEefeV/kdAIEtqISHSRgwaE0WqhO3AUfHOjNMSy8EtIBBc1mqr1d0x4ecNpst0Bh7UVMZGhyNu/Cjyxt+eTs7h0uYp6gFNo0V5XLF12AMCKe13EHgPBaERfaQlEm40uMGXv9fCABoEffgShvQ2MSgW/qclgg4JIfEX8XdmOO63d2JlXTm/U191M7eVkLoPCT4YPDhWgw2Bx27IamyknmNHTNlAPFJp/ck8+zo9FY7IIAIEAaIFa5QeH00U+HoE6fxhNPW5O1N2ulDwwAOCC4czjnOveu3T8LkLAe4SAPcPseXIi/9R5VNfpwVRGhQ9N0McCsmTMFp/sdxn15ERWdj4uFJWDKY+JpAA4jiP/pGq4hMSUEpGMCWYpfQaEUSmJ30hp4WSEhNJ6nidOd+0hsvypjPuy35MT23bnor3TNBiC2pKj1IC9phrW0ivEqgCOsL5h/VovABEfH5J0joV66jQox4ylakOLCWUVeuzI/t4n+11rPDmhYMwwdFkGAdwuPoLu34tgq5LIwSpVUI2PQ+VLc70AxJ67CNvNGxDsUpaoxsVCO2MmqR0y6NstmJN20Cf7OeItvt9Lrn09Jj3xGj+YhgkpKbBeK5cOJ26eWHwFyugYlOjUXgCSjd2w19fh+oxkCD0SCPWkBIz9Oh+KyCjEztk1jP0Way92bJyPX69Uo/ByNWSqEHQ2lZH6LA4CeFqpoMaUEaMQe/JbaCYnwknCUELSx1OmNN6FMiSIgr2Vthz25iY6LSf1YGzucaTmVEBwDM+Ane8sQHFZLS4WV0oAGkvpPqY4fIToMHZRJXDWc4jLPwn5iFAYTHb8eV0P1dxxXgBsP5YhKWUcgnVKODo7cCv9VRgLL/WDCMSbSftpHZB6IHG7wwKn3QgZ4YyrMzqdAvw0T5BK+IcE4LeQQNFJmB6xeQui9+0n7OZwobgeRrOd8IxFUFqSF4CuvF/ABeig9ffD89MjIfI8are/j+bDhyALCMDG1IPu9b7qAStXQ64MHuwFu48co/mTtWk13XjqfBX2fFqMtAXjEROhhcD3QqtWosfWS0nEcgrUNJnw3dkqZL2VimXzvD3kakYueeRuOABg4QsL8OWZf1BYIjUaJduFhbNjMerJYKQkRpN2akJdcyeaWgwouFgBa6+MttXZyZHIWPwMJseFwdLTh9QVJ/5bNwyLX0U9EBY9y+06Qegj3awNcTHhmDIxCutWPouW1i6cJi3VbLHj56Ib7rbqFZ9+5WHd0Os94AuAKJJmYmlBYIAK3xxZQ75SJtysuYvMg2fo42LgYeELwIO6oYuADMMNcsAXAM8YLp03GdvemOtKWbzy9udovmsEK1NCrgr1dTYde1A3fHQAdgPJZysyN8xHk94ApUKO8NAA+rBg5f6EySH3BUAvYCcdcUg9YGUqAnwE3ed+koXFZ7QSzGFDrfkplCQNGYSPDCLxN9DpsFAdzN09sNkd6Ouzu676YOnvbXQdaWDUjd6iZ8Li0xeRyVyyaKTnnIy8C1lSE6hLSc12bR144bpquHPYC/chYIZP64nl9f8CYyvoAns2VZUAAAAASUVORK5CYII=',
        'TR'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAADPklEQVRIS8VWTSxjURT+XrVT1F8HM0ZKMkgkiMTCRiLEUjIWNjYkmEwyQSIssJIICSsjsSAZs2DFEgssRCIRFjZCYiExxlSp/7+Wtk/fnHPrlZmJ1x8JJznNu+/de7/vnvOd0yuNAJ8g4TuA9+QvaXYo+CKNSDh4BXD1oHYmoPDos/h9Ofsh+bBCJuC552h4JteQCLgI7Iz86h/QWBqbyY1hkAmawCFtfqECSBIMGRmAokDe2YHi9Yov8eTvQiQRFAE/OAEnNDbC3N4OvcUioGSrFae9vbgYGhKEQiURkMA1geyzSPR6pExMIKay0n9Gz/Y25N1dSNHRcK+v47ChAYrbHRKJgAR+E9wteXJ/PxJaWgQ4Ax9UV+N2efkh4BQdyUCSpHQosiyaCWvjXuRPJkaTgHp6Y0EB0ldXAZ1ObL6bnw/35ia4AtRqYARjXh4SWlthr6/3AwZKiSaBI9rmnIU1OIj4piaxqWN6GraKCkTTs5NPSKQkkwne62tYFhYQVVwMa0kJ3Bsb8DqdIiVZGpHQJLB3D2KZn0dUWZkgcNLZidPubiTyM79gYTY3I6mvD5LxoRAdU1MiTd6rK2TSNN0TSdAkYKVFN+RpS0uILCoSWxxRiM+/fUMyPbvJ1dI0UVRSJyfFHOfsLPbKy0VVxNFY689Fk4CdFl+Sp4yNIbamRmx+OToKe22taDpMggV6TG7u6EBiV5dPG5mZ+JWdDdlmw0f6pn/i9PxakwCfjntAbFUVUsbHxTaKywVraSluV1bEmIlwh4yrq8MNpcpDZRlZWIiIpCQ4ZmaQSt9M4RKQaeFPcu4B6WtreJOT4yNB4jrt6cHF8DDuzrg5P5hKiN9wlaSTP5X/gBHgCWoX5BKzLC5CZ+au7zMuSW5Anq0t3J2cCHHK+/tIo2935FEBwIMiwJNUMRqyspA8MAATC4zUL4waj3NuDsdtbXBR6cXQqw9/xUR7ELAT8nJOBQuS655Nn5YGY24uiVyBm1IjH/Bdxndi3z9E8BYUAXU7zraDnEvzsTEwN6a3weP6Z4ZEQF3FEeEewMaiiwgDWF0SFoFn4P239DGB17yU2iS+lpOwR+hOGuql5rkBsdF1+OsfSE5HMaBO96YAAAAASUVORK5CYII=',
        'AE'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAAo0lEQVRIS2Ncxs0t/vcP0x4GRgYdBiwgNtYAmzDlYoz/rzD8++3CuJid9zIuy0G20MwBIMOBjmBczMH7H8T2EZPA6ivBTKwBQ3kI3HsDNmPUAaMhMPhDgO3WNcpTPBYTemcuIi4XjDpgNARGQ2DAQ8BRiJsm5cCFCxeIKwdGHTAaArQPAUKN0h+faZILoIaeZ4Q2y/cBW8ZaWJvltHPAeaB9ngBB+oBdtS5TjgAAAABJRU5ErkJggg==',
        'SG'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAABmElEQVRIS2PcyMur/uLb1/2//v6TZKAjYGNmei7Bxe3IOJOZ6RkuyznFxBg4RUQYfn78yPD16VOqOw/kCMbJDAz/QSZ7qUpDLGBkZOCLimPgT0pnYBYUYng/sYfh87qVDBKzF4HlnoUHUMUh225DPIThAMGcQgbBvGKGPy9fMDyPDWP4/eAeWKHUyg0g5UAH+NPOAYwcHAzyxy8wMHHzMDyPj2D4fvwIg9SK9Sg+R+eT6xqsIcCmqs4gs3Uvw5/nzxge2Zth9Tm1QgKrA1jlFRhkdx9h+PfpI8NDC32G/3/+wD1ILZ/DDMSeBpiYGOT2HWdgkZJm+DB9EsO7/i6EA+iRBkC28fgGMoh1TWBgYGZm+LRkAcPb7laG/9+/kxvVOPXhzAUgHZzmVgz8KRkM7Nq6DP9//WR401TL8G3fbqo6Aq8DqGoTDsMGjwOaJs4Fl4TF6XH08Djcjt6ZwJIVVLSNOmA0BEZDYDQEBjwEemYtffbt+w+6tohhxSEXJ8cTxo37jqjfuv3gwPcfPyXoWRaDLFdVkXcBADb54CECOnhyAAAAAElFTkSuQmCC',
    );

    /**
     * Returns an array of locale labels.
     * This is for `select` input elements.
     * For the category unit type, use this method and avoid using the one provided by PAAPI class
     * as API supported locales and the actual stores are different.
     * @sinec       3.10.1
     */
    static public function getLocaleLabels() {
        return array(
            'CA' => 'CA - ' . __( 'Canada', 'amazon-auto-links' ),
            'CN' => 'CN - ' . __( 'China', 'amazon-auto-links' ),
            'FR' => 'FR - ' . __( 'France', 'amazon-auto-links' ),
            'DE' => 'DE - ' . __( 'Germany', 'amazon-auto-links' ),
            'IT' => 'IT - ' . __( 'Italy', 'amazon-auto-links' ),
            'JP' => 'JP - ' . __( 'Japan', 'amazon-auto-links' ),
            'UK' => 'UK - ' . __( 'United Kingdom', 'amazon-auto-links' ),
            'ES' => 'ES - ' . __( 'Spain', 'amazon-auto-links' ),
            'US' => 'US - ' . __( 'United States', 'amazon-auto-links' ),
            'IN' => 'IN - ' . __( 'India', 'amazon-auto-links' ),
            'BR' => 'BR - ' . __( 'Brazil', 'amazon-auto-links' ),
            'MX' => 'MX - ' . __( 'Mexico', 'amazon-auto-links' ),
            'AU' => 'AU - ' . __( 'Australia', 'amazon-auto-links' ), // 3.5.5+
            'TR' => 'TR - ' . __( 'Turkey', 'amazon-auto-links' ), // 3.9.1
            'AE' => 'AE - ' . __( 'United Arab Emirates', 'amazon-auto-links' ), // 3.9.1
            'SG' => 'SG - ' . __( 'Singapore', 'amazon-auto-links' ), // 3.10.1
        );
    }

    /**
     * @var array
     */
    static public $aCategoryBlackCurtainURLs = array(
        'CA'    => 'https://www.amazon.ca/gp/product/black-curtain-redirect.html',
        'CN'    => 'https://www.amazon.cn/gp/product/black-curtain-redirect.html',
        'FR'    => 'https://www.amazon.fr/gp/product/black-curtain-redirect.html',
        'DE'    => 'https://www.amazon.de/gp/product/black-curtain-redirect.html',
        'IT'    => 'https://www.amazon.it/gp/product/black-curtain-redirect.html',
        'JP'    => 'https://www.amazon.co.jp/gp/product/black-curtain-redirect.html',
        'UK'    => 'https://www.amazon.co.uk/gp/product/black-curtain-redirect.html',
        'ES'    => 'https://www.amazon.es/gp/product/black-curtain-redirect.html',
        'US'    => 'https://www.amazon.com/gp/product/black-curtain-redirect.html',
        'IN'    => 'https://www.amazon.in/gp/product/black-curtain-redirect.html',
        'BR'    => 'https://www.amazon.com.br/gp/product/black-curtain-redirect.html',
        'MX'    => 'https://www.amazon.com.mx/gp/product/black-curtain-redirect.html',
        'AU'    => 'https://www.amazon.com.au/gp/product/black-curtain-redirect.html',   // 3.5.5+
    );

    static public $aNoImageAvailable = array(    // the domain can be g-ecx.images-amazon.com
        'CA'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',
        'CN'    => 'http://g-images.amazon.com/images/G/28/x-site/icons/no-img-sm.gif',
        'FR'    => 'http://g-images.amazon.com/images/G/08/x-site/icons/no-img-sm.gif',
        'DE'    => 'http://g-images.amazon.com/images/G/03/x-site/icons/no-img-sm.gif',
        'IT'    => 'http://g-images.amazon.com/images/G/29/x-site/icons/no-img-sm.gif',
        'JP'    => 'http://g-images.amazon.com/images/G/09/x-site/icons/no-img-sm.gif',
        'UK'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',
        'ES'    => 'http://g-images.amazon.com/images/G/30/x-site/icons/no-img-sm.gif',
        'US'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',
        'IN'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',
        'BR'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',    // should be Portuguese but could not find
        'MX'    => 'http://g-images.amazon.com/images/G/30/x-site/icons/no-img-sm.gif',    // Spanish
        'AU'    => 'http://g-images.amazon.com/images/G/01/x-site/icons/no-img-sm.gif',    // 3.5.5+
    );

    /**
     * @since       3.1.0
     * @see         http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AddToCartForm.html
     */
    static public $aAddToCartURLs = array(
        'CA' => 'www.amazon.ca/gp/aws/cart/add.html',
            'CN' => 'www.amazon.cn/gp/aws/cart/add.html',
        'FR' => 'www.amazon.fr/gp/aws/cart/add.html',
        'DE' => 'www.amazon.de/gp/aws/cart/add.html',
            'IT' => 'www.amazon.it/gp/aws/cart/add.html',
        'JP' => 'www.amazon.co.jp/gp/aws/cart/add.html',
        'UK' => 'www.amazon.co.uk/gp/aws/cart/add.html',
            'ES' => 'www.amazon.es/gp/aws/cart/add.html',
        'US' => 'www.amazon.com/gp/aws/cart/add.html',
        'US' => 'www.amazon.com/gp/aws/cart/add.html',
            'IN' => 'www.amazon.in/gp/aws/cart/add.html',
            'BR'    => 'www.amazon.com.br/gp/aws/cart/add.html',
            'MX'    => 'www.amazon.com.mx/gp/aws/cart/add.html',
        'AU' => 'www.amazon.com.au/gp/aws/cart/add.html',   // 3.5.5+
    );

    static public $aTokens = array(
        'CA' => 'bWl1bm9zb2Z0Y2EtMjA=',
        'CN' => 'bWl1bm9zb2Z0LTIz',
        'FR' => 'bWl1bm9zb2Z0ZnItMjE=',
        'DE' => 'bWl1bm9zb2Z0ZGUtMjE=',
        'IT' => 'bWl1bm9zb2Z0LTIx',
        'JP' => 'bWl1bm9zb2Z0LTIy',
        'UK' => 'bWl1bm9zb2Z0dWstMjE=',
        'ES' => 'bWl1bm9zb2Z0ZXMtMjE=',
        'US' => 'bWl1bm9zb2Z0LTIw',
        'MX' => 'bWl1bm9zb2Z0LTIw', // 3.5.5+
        'AU' => 'bWl1bm9zb2Z0LTIw', // 3.5.5+
    );

    /**
     * Returns an array of search index of the specified locale.
     *
     * @see                http://docs.aws.amazon.com/AWSECommerceService/latest/DG/APPNDX_SearchIndexValues.html
     * @remark             The above link is no longer available.
     * @see                https://docs.aws.amazon.com/AWSECommerceService/latest/DG/localevalues.html
     * @remark             The `AU` locale is missing in the AWS documentation.
     * @since   unknown
     * @since   3.9.0   Made it compatible with PA-API 5
     * @see     https://webservices.amazon.com/paapi5/documentation/locale-reference.html
     */
    static public function getSearchIndexByLocale( $sLocale ) {

        switch ( strtoupper( $sLocale ) ) {
            case 'AU':
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ),
                    'Automotive' => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby' => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ),
                    'Books' => __( 'Books', 'amazon-auto-links' ),
                    'Computers' => __( 'Computers', 'amazon-auto-links' ),
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ),
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ),
                    'Fashion' => __( 'Clothing & Shoes', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'HealthPersonalCare' =>    __( 'Health,  Household & Personal Care', 'amazon-auto-links' ),
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Lighting', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Luggage & Travel Gear', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV', 'amazon-auto-links' ), 
                    'Music' => __( 'CDs & Vinyl', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Stationery & Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports,  Fitness & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Home Improvement', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ), 
                );
            case 'BR':
                return array(
                    'All' => __( 'Todos os departamentos', 'amazon-auto-links' ), 
                    'Books' => __( 'Livros', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computadores e Informática', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Eletrônicos', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Casa e Cozinha', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Loja Kindle', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps e Jogos', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Material para Escritório e Papelaria', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Ferramentas e Materiais de Construção', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Games', 'amazon-auto-links' ), 
                );
            case 'CA':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/canada.html
                return array(
                    'All' => __( 'All Department', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing & Accessories', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Automotive', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Classical' => __( 'Classical Music', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'English Books', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Patio, Lawn & Garden', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Grocery & Gourmet Food', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health & Personal Care', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewelry', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Luggage & Bags', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV', 'amazon-auto-links' ), 
                    'Music' => __( 'Music', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments, Stage & Studio', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes & Handbags', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Tools & Home Improvement', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Watches', 'amazon-auto-links' ), 
                );
            case 'FR':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/france.html
                return array(
                    'All' => __( 'Toutes nos catégories', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Vêtements et accessoires', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Gros électroménager', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Auto et Moto', 'amazon-auto-links' ), 
                    'Baby' => __( 'Bébés & Puériculture', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauté et Parfum', 'amazon-auto-links' ), 
                    'Books' => __( 'Livres en français', 'amazon-auto-links' ), 
                    'Computers' => __( 'Informatique', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Téléchargement de musique', 'amazon-auto-links' ), 
                    'Electronics' => __( 'High-Tech', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Autres', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Mode', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'Livres anglais et étrangers', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Jardin', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Boutique chèques-cadeaux', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Epicerie', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Hygiène et Santé', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Cuisine & Maison', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Secteur industriel & scientifique', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Bijoux', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Boutique Kindle', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Luminaires et Eclairage', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Bagages', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Beauté Prestige', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Applis & Jeux', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'DVD & Blu-ray', 'amazon-auto-links' ), 
                    'Music' => __( 'Musique : CD & Vinyles', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Instruments de musique & Sono', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Fournitures de bureau', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Animalerie', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Chaussures et Sacs', 'amazon-auto-links' ), 
                    'Software' => __( 'Logiciels', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports et Loisirs', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Bricolage', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Jeux et Jouets', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Jeux vidéo', 'amazon-auto-links' ), 
                    'Watches' => __( 'Montres', 'amazon-auto-links' ), 
                );
            case 'DE':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/germany.html
                return array(
                    'All' => __( 'Alle Kategorien', 'amazon-auto-links' ), 
                    'AmazonVideo' => __( 'Prime Video', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Bekleidung', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Elektro-Großgeräte', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Auto & Motorrad', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Bücher', 'amazon-auto-links' ), 
                    'Classical' => __( 'Klassik', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computer & Zubehör', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Musik-Downloads', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Elektronik & Foto', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Sonstiges', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Fashion', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'Bücher (Fremdsprachig)', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Garten', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Geschenkgutscheine', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Lebensmittel & Getränke', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Drogerie & Körperpflege', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Küche, Haushalt & Wohnen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Gewerbe, Industrie & Wissenschaft', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Schmuck', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle-Shop', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Beleuchtung', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Koffer, Rucksäcke & Taschen', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'Magazines' => __( 'Zeitschriften', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Spiele', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'DVD & Blu-ray', 'amazon-auto-links' ), 
                    'Music' => __( 'Musik-CDs & Vinyl', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musikinstrumente & DJ-Equipment', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Bürobedarf & Schreibwaren', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Haustier', 'amazon-auto-links' ), 
                    'Photo' => __( 'Kamera & Foto', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Schuhe & Handtaschen', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sport & Freizeit', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Baumarkt', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Spielzeug', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Uhren', 'amazon-auto-links' ), 
                );
            case 'IN':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/india.html
                return array(
                    'All' => __( 'All Categories', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing & Accessories', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Appliances', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Car & Motorbike', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Collectibles' => __( 'Collectibles', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers & Accessories', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Amazon Fashion', 'amazon-auto-links' ), 
                    'Furniture' => __( 'Furniture', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Garden & Outdoors', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Grocery & Gourmet Foods', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health & Personal Care', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewellery', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Luggage & Bags', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV Shows', 'amazon-auto-links' ), 
                    'Music' => __( 'Music', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes & Handbags', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports, Fitness & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Tools & Home Improvement', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches' => __( 'Watches', 'amazon-auto-links' ),
                );
            case 'IT':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/italy.html
                return array(
                    'All' => __( 'Tutte le categorie', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Abbigliamento', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Grandi elettrodomestici', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Auto e Moto', 'amazon-auto-links' ), 
                    'Baby' => __( 'Prima infanzia', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Bellezza', 'amazon-auto-links' ), 
                    'Books' => __( 'Libri', 'amazon-auto-links' ), 
                    'Computers' => __( 'Informatica', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Musica Digitale', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Elettronica', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Altro', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Moda', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'Libri in altre lingue', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Giardino e giardinaggio', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Buoni Regalo', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Alimentari e cura della casa', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Salute e cura della persona', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Casa e cucina', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industria e Scienza', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Gioielli', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Illuminazione', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Valigeria', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'App e Giochi', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Film e TV', 'amazon-auto-links' ), 
                    'Music' => __( 'CD e Vinili', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Strumenti musicali e DJ', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Cancelleria e prodotti per ufficio', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Prodotti per animali domestici', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Scarpe e borse', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sport e tempo libero', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Fai da te', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Giochi e giocattoli', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Videogiochi', 'amazon-auto-links' ), 
                    'Watches' => __( 'Orologi', 'amazon-auto-links' ),
                );
            case 'JP':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/japan.html
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ), 
                    'AmazonVideo' => __( 'Prime Video', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing & Accessories', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Large Appliances', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Car & Bike Products', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby & Maternity', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Japanese Books', 'amazon-auto-links' ), 
                    'Classical' => __( 'Classical', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers & Accessories', 'amazon-auto-links' ), 
                    'CreditCards' => __( 'Credit Cards', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Digital Music', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics & Cameras', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Fashion', 'amazon-auto-links' ), 
                    'FashionBaby' => __( 'Kids & Baby', 'amazon-auto-links' ), 
                    'FashionMen' => __( 'Men', 'amazon-auto-links' ), 
                    'FashionWomen' => __( 'Women', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'English Books', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Food & Beverage', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health & Personal Care', 'amazon-auto-links' ), 
                    'Hobbies' => __( 'Hobby', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Kitchen & Housewares', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewelry', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV', 'amazon-auto-links' ), 
                    'Music' => __( 'Music', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Stationery and Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes & Bags', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'DIY, Tools & Garden', 'amazon-auto-links' ), 
                    'Toys' => __( 'Toys', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Computer & Video Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Watches', 'amazon-auto-links' ),
                );
            case 'MX':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/mexico.html
                return array(
                    'All' => __( 'Todos los departamentos', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Auto', 'amazon-auto-links' ), 
                    'Baby' => __( 'Bebé', 'amazon-auto-links' ), 
                    'Books' => __( 'Libros', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electrónicos', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Ropa, Zapatos y Accesorios', 'amazon-auto-links' ), 
                    'FashionBaby' => __( 'Ropa, Zapatos y Accesorios Bebé', 'amazon-auto-links' ), 
                    'FashionBoys' => __( 'Ropa, Zapatos y Accesorios Niños', 'amazon-auto-links' ), 
                    'FashionGirls' => __( 'Ropa, Zapatos y Accesorios Niñas', 'amazon-auto-links' ), 
                    'FashionMen' => __( 'Ropa, Zapatos y Accesorios Hombres', 'amazon-auto-links' ), 
                    'FashionWomen' => __( 'Ropa, Zapatos y Accesorios Mujeres', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Alimentos y Bebidas', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Productos Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Salud, Belleza y Cuidado Personal', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Hogar y Cocina', 'amazon-auto-links' ), 
                    'IndustrialAndScientific' => __( 'Industria y ciencia', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Tienda Kindle', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Películas y Series de TV', 'amazon-auto-links' ), 
                    'Music' => __( 'Música', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Instrumentos musicales', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Oficina y Papelería', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Mascotas', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Deportes y Aire Libre', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Herramientas y Mejoras del Hogar', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Juegos y juguetes', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Videojuegos', 'amazon-auto-links' ), 
                    'Watches' => __( 'Relojes', 'amazon-auto-links' ),
                );
            case 'ES':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/spain.html
                return array(
                    'All' => __( 'Todos los departamentos', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Ropa y accesorios', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Grandes electrodomésticos', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Coche y moto', 'amazon-auto-links' ), 
                    'Baby' => __( 'Bebé', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Belleza', 'amazon-auto-links' ), 
                    'Books' => __( 'Libros', 'amazon-auto-links' ), 
                    'Computers' => __( 'Informática', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Música Digital', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electrónica', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Otros Productos', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Moda', 'amazon-auto-links' ), 
                    'ForeignBooks' => __( 'Libros en idiomas extranjeros', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Jardín', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Cheques regalo', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Alimentación y bebidas', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Salud y cuidado personal', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Hogar y cocina', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industria y ciencia', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Joyería', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Tienda Kindle', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Iluminación', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Equipaje', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Appstore para Android', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Películas y TV', 'amazon-auto-links' ), 
                    'Music' => __( 'Música: CDs y vinilos', 'amazon-auto-links' ),
                    'MusicalInstruments' => __( 'Instrumentos musicales', 'amazon-auto-links' ),
                    'OfficeProducts' => __( 'Oficina y papelería', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Productos para mascotas', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Zapatos y complementos', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Deportes y aire libre', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Bricolaje y herramientas', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Juguetes y juegos', 'amazon-auto-links' ), 
                    'Vehicles' => __( 'Coche - renting', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Videojuegos', 'amazon-auto-links' ), 
                    'Watches' => __( 'Relojes', 'amazon-auto-links' ), 
                );
            case 'TR':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/turkey.html
                return array(
                    'All' => __( 'Tüm Kategoriler', 'amazon-auto-links' ), 
                    'Baby' => __( 'Bebek', 'amazon-auto-links' ), 
                    'Books' => __( 'Kitaplar', 'amazon-auto-links' ), 
                    'Computers' => __( 'Bilgisayarlar', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Elektronik', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Diğer Her Şey', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Moda', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Ev ve Mutfak', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Ofis Ürünleri', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Spor', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Yapı Market', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Oyuncaklar ve Oyunlar', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'PC ve Video Oyunları', 'amazon-auto-links' ), 
                );
            case 'AE':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/united-arab-emirates.html
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Automotive Parts & Accessories', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty & Personal Care', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Clothing, Shoes & Jewelry', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Lighting', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ), 
                );
            case 'UK':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/united-kingdom.html
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ), 
                    'AmazonVideo' => __( 'Amazon Video', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Large Appliances', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Car & Motorbike', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Classical' => __( 'Classical Music', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers & Accessories', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Digital Music', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics & Photo', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Fashion', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Garden & Outdoors', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Grocery', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health & Personal Care', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewellery', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'Lighting' => __( 'Lighting', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'DVD & Blu-ray', 'amazon-auto-links' ), 
                    'Music' => __( 'CDs & Vinyl', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments & DJ', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Stationery & Office Supplies', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes & Bags', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'DIY & Tools', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'PC & Video Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Watches', 'amazon-auto-links' ), 
                );
            case 'CN':
                // @see https://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleCN.html
                return array(
                    'All'                   => __( 'All', 'amazon-auto-links' ),
                    'Apparel'               => __( 'Apparel', 'amazon-auto-links' ),
                    'Appliances'            => __( 'Appliances', 'amazon-auto-links' ),
                    'Automotive'            => __( 'Automotive', 'amazon-auto-links' ),
                    'Baby'                  => __( 'Baby', 'amazon-auto-links' ),
                    'Beauty'                => __( 'Beauty', 'amazon-auto-links' ),
                    'Books'                 => __( 'Books', 'amazon-auto-links' ),
                    'Electronics'           => __( 'Electronics', 'amazon-auto-links' ),
                    'Grocery'               => __( 'Grocery', 'amazon-auto-links' ),
                    'HealthPersonalCare'    => __( 'Health Personal Care', 'amazon-auto-links' ),
                    'Home'                  => __( 'Home', 'amazon-auto-links' ),
                    'HomeImprovement'       => __( 'Home Improvement', 'amazon-auto-links' ),
                    'Jewelry'               => __( 'Jewelry', 'amazon-auto-links' ),
                    'KindleStore'           => __( 'Kindle Store', 'amazon-auto-links' ),
                    'Miscellaneous'         => __( 'Miscellaneous', 'amazon-auto-links' ),   // missing in recent documentation
                    'Kitchen'               => __( 'Kitchen', 'amazon-auto-links' ),    // 3.5.5+
                    'MobileApps'            => __( 'Mobile Apps', 'amazon-auto-links' ),
                    'Music'                 => __( 'Music', 'amazon-auto-links' ),    // 3.5.5+
                    'MusicalInstruments'    => __( 'MusicalInstruments', 'amazon-auto-links' ),    // 2.1.0+
                    'OfficeProducts'        => __( 'Office Products', 'amazon-auto-links' ),
                    'PCHardware'            => __( 'PCHardware', 'amazon-auto-links' ),    // 3.5.5+
                    'PetSupplies'           => __( 'Pet Supplies', 'amazon-auto-links' ),
                    'Photo'                 => __( 'Photo', 'amazon-auto-links' ),
                    'Shoes'                 => __( 'Shoes', 'amazon-auto-links' ),
                    'Software'              => __( 'Software', 'amazon-auto-links' ),
                    'SportingGoods'         => __( 'Sporting Goods', 'amazon-auto-links' ),
                    'Toys'                  => __( 'Toys', 'amazon-auto-links' ),   // missing in recent documentation
                    'Video'                 => __( 'Video', 'amazon-auto-links' ),
                    'VideoGames'            => __( 'Video Games', 'amazon-auto-links' ),
                    'Watches'               => __( 'Watches', 'amazon-auto-links' ),
                );
            default:
            case 'US':
                // @see https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html
                return array(
                    'All' => __( 'All Departments', 'amazon-auto-links' ), 
                    'AmazonVideo' => __( 'Prime Video', 'amazon-auto-links' ), 
                    'Apparel' => __( 'Clothing & Accessories', 'amazon-auto-links' ), 
                    'Appliances' => __( 'Appliances', 'amazon-auto-links' ), 
                    'ArtsAndCrafts' => __( 'Arts, Crafts & Sewing', 'amazon-auto-links' ), 
                    'Automotive' => __( 'Automotive Parts & Accessories', 'amazon-auto-links' ), 
                    'Baby' => __( 'Baby', 'amazon-auto-links' ), 
                    'Beauty' => __( 'Beauty & Personal Care', 'amazon-auto-links' ), 
                    'Books' => __( 'Books', 'amazon-auto-links' ), 
                    'Classical' => __( 'Classical', 'amazon-auto-links' ), 
                    'Collectibles' => __( 'Collectibles & Fine Art', 'amazon-auto-links' ), 
                    'Computers' => __( 'Computers', 'amazon-auto-links' ), 
                    'DigitalMusic' => __( 'Digital Music', 'amazon-auto-links' ), 
                    'Electronics' => __( 'Electronics', 'amazon-auto-links' ), 
                    'EverythingElse' => __( 'Everything Else', 'amazon-auto-links' ), 
                    'Fashion' => __( 'Clothing, Shoes & Jewelry', 'amazon-auto-links' ), 
                    'FashionBaby' => __( 'Clothing, Shoes & Jewelry Baby', 'amazon-auto-links' ), 
                    'FashionBoys' => __( 'Clothing, Shoes & Jewelry Boys', 'amazon-auto-links' ), 
                    'FashionGirls' => __( 'Clothing, Shoes & Jewelry Girls', 'amazon-auto-links' ), 
                    'FashionMen' => __( 'Clothing, Shoes & Jewelry Men', 'amazon-auto-links' ), 
                    'FashionWomen' => __( 'Clothing, Shoes & Jewelry Women', 'amazon-auto-links' ), 
                    'GardenAndOutdoor' => __( 'Garden & Outdoor', 'amazon-auto-links' ), 
                    'GiftCards' => __( 'Gift Cards', 'amazon-auto-links' ), 
                    'GroceryAndGourmetFood' => __( 'Grocery & Gourmet Food', 'amazon-auto-links' ), 
                    'Handmade' => __( 'Handmade', 'amazon-auto-links' ), 
                    'HealthPersonalCare' => __( 'Health, Household & Baby Care', 'amazon-auto-links' ), 
                    'HomeAndKitchen' => __( 'Home & Kitchen', 'amazon-auto-links' ), 
                    'Industrial' => __( 'Industrial & Scientific', 'amazon-auto-links' ), 
                    'Jewelry' => __( 'Jewelry', 'amazon-auto-links' ), 
                    'KindleStore' => __( 'Kindle Store', 'amazon-auto-links' ), 
                    'LocalServices' => __( 'Home & Business Services', 'amazon-auto-links' ), 
                    'Luggage' => __( 'Luggage & Travel Gear', 'amazon-auto-links' ), 
                    'LuxuryBeauty' => __( 'Luxury Beauty', 'amazon-auto-links' ), 
                    'Magazines' => __( 'Magazine Subscriptions', 'amazon-auto-links' ), 
                    'MobileAndAccessories' => __( 'Cell Phones & Accessories', 'amazon-auto-links' ), 
                    'MobileApps' => __( 'Apps & Games', 'amazon-auto-links' ), 
                    'MoviesAndTV' => __( 'Movies & TV', 'amazon-auto-links' ), 
                    'Music' => __( 'CDs & Vinyl', 'amazon-auto-links' ), 
                    'MusicalInstruments' => __( 'Musical Instruments', 'amazon-auto-links' ), 
                    'OfficeProducts' => __( 'Office Products', 'amazon-auto-links' ), 
                    'PetSupplies' => __( 'Pet Supplies', 'amazon-auto-links' ), 
                    'Photo' => __( 'Camera & Photo', 'amazon-auto-links' ), 
                    'Shoes' => __( 'Shoes', 'amazon-auto-links' ), 
                    'Software' => __( 'Software', 'amazon-auto-links' ), 
                    'SportsAndOutdoors' => __( 'Sports & Outdoors', 'amazon-auto-links' ), 
                    'ToolsAndHomeImprovement' => __( 'Tools & Home Improvement', 'amazon-auto-links' ), 
                    'ToysAndGames' => __( 'Toys & Games', 'amazon-auto-links' ), 
                    'VHS' => __( 'VHS', 'amazon-auto-links' ), 
                    'VideoGames' => __( 'Video Games', 'amazon-auto-links' ), 
                    'Watches' => __( 'Watches', 'amazon-auto-links' ), 
                );
        }
    }

    /**
     * @todo    Confirm if this property is being used.
     * @remark  Seems not used. And the documentation of the below url does not include the `AU` locale.
     * @see     https://docs.aws.amazon.com/AWSECommerceService/latest/DG/localevalues.html
     */
    static public $aSearchIndex = array(
        'CA' => array(
            "All","Baby","Beauty","Blended","Books","Classical","DVD","Electronics","ForeignBooks",
            "HealthPersonalCare","KindleStore","LawnAndGarden","Music","PetSupplies","Software",
            "SoftwareVideoGames","VHS","Video","VideoGames"
        ),
        'CN' => array(
            "All","Apparel","Appliances","Automotive","Baby","Beauty","Books","Electronics","Grocery",
            "HealthPersonalCare","Home","HomeImprovement","Jewelry","KindleStore","Miscellaneous","Music",
            "OfficeProducts","PetSupplies","Photo","Shoes","Software","SportingGoods","Toys","Video","VideoGames",
            "Watches"
        ),
        'DE' => array(
            "All","Apparel","Automotive","Baby","Beauty","Blended","Books","Classical","DVD","Electronics",
            "ForeignBooks","Grocery","HealthPersonalCare","HomeGarden","HomeImprovement","Jewelry","KindleStore",
            "Kitchen","Lighting","Magazines","Marketplace","MP3Downloads","Music","MusicalInstruments",
            "MusicTracks","OfficeProducts","OutdoorLiving","Outlet","PCHardware","Photo","Shoes","Software",
            "SoftwareVideoGames","SportingGoods","Tools","Toys","VHS","Video","VideoGames","Watches"
        ),
        'ES' => array(
            "All","Automotive","Baby","Books","DVD","Electronics","ForeignBooks","KindleStore","Kitchen",
            "MP3Downloads","Music","Shoes","Software","Toys","VideoGames","Watches"
        ),
        'FR' => array(
            "All","Apparel","Automotive","Baby","Beauty","Blended","Books","Classical","DVD","Electronics",
            "ForeignBooks","HealthPersonalCare","Jewelry","KindleStore","Kitchen","Lighting","MP3Downloads",
            "Music","MusicalInstruments","MusicTracks","OfficeProducts","PCHardware","PetSupplies","Shoes","Software",
            "SoftwareVideoGames","SportingGoods","Toys","VHS","Video","VideoGames","Watches"
        ),
        'IN' => array( "All", "Books","DVD"    ),
        'IT' => array(
            "All","Automotive","Baby","Books","DVD","Electronics","ForeignBooks","Garden","KindleStore","Kitchen",
            "Lighting","MP3Downloads","Music","Shoes","Software","Toys","VideoGames","Watches"
        ),
        'JP' => array(
            "All","Apparel","Appliances","Automotive","Baby","Beauty","Blended","Books","Classical","DVD",
            "Electronics","ForeignBooks","Grocery","HealthPersonalCare","Hobbies","HomeImprovement","Jewelry",
            "KindleStore","Kitchen","Marketplace","MobileApps","MP3Downloads","Music","MusicalInstruments",
            "MusicTracks","OfficeProducts","Shoes","Software","SportingGoods","Toys","VHS","Video","VideoGames"
        ),
        'UK' => array(
            "All","Apparel","Automotive","Baby","Beauty","Blended","Books","Classical","DVD","Electronics",
            "Grocery","HealthPersonalCare","HomeGarden","HomeImprovement","Jewelry","KindleStore","Kitchen",
            "Lighting","Marketplace","MP3Downloads","Music","MusicalInstruments","MusicTracks","OfficeProducts",
            "OutdoorLiving","Outlet","PCHardware","Shoes","Software","SoftwareVideoGames","SportingGoods","Tools",
            "Toys","VHS","Video","VideoGames","Watches"
        ),
        'US' => array(
            "All","Apparel","Appliances","ArtsAndCrafts","Automotive","Baby","Beauty","Blended","Books","Classical",
            "Collectibles","DigitalMusic","DVD","Electronics","GourmetFood","Grocery","HealthPersonalCare",
            "HomeGarden","Industrial","Jewelry","KindleStore","Kitchen","LawnAndGarden","Magazines","Marketplace",
            "Miscellaneous","MobileApps","MP3Downloads","Music","MusicalInstruments","MusicTracks","OfficeProducts",
            "OutdoorLiving","PCHardware","PetSupplies","Photo","Shoes","Software","SportingGoods","Tools","Toys",
            "UnboxVideo","VHS","Video","VideoGames","Watches","Wireless","WirelessAccessories"
        ),
        // 3.5.5+
        'MX' => array(),
        'AU' => array(),

    );

    /**
     * 
     * @remark          These IDs were valid as of the publication date of this guide. API Version 2011-08-01
     * @see             http://docs.aws.amazon.com/AWSECommerceService/latest/DG/BrowseNodeIDs.html
     * @todo            Confirm if this property is used or not. The caller method may not be used.
     */
    static public $aRootNodes = array(
        'CA' => array(
            3561346011,6205124011,927726,962454,14113311,677211011,927726,6205177011,2972705011,2206275011,
            6205499011,962454,6205514011,3234171,3323751,962072,962454,110218011,
        ),
        'CN' => array(
            2016156051,80207071,1947899051,746776051,658390051,2016116051,2127215051,852803051,2016126051,
            1952920051,816482051,116087071,899280051,754386051,2127221051,118863071 ,755653051,2029189051,
            863872051,836312051,647070051,2016136051,897415051,1953164051,
        ),
        'DE' => array(
            78689031,78191031,357577011,64257031,541686,542676,547664,569604,54071011,340846031,64257031,
            10925241,327473011,530484031,3169011,213083031,1161658,77195031,542676,340849031,192416031,10925051,
            569604,569604,542064,541708,16435121,12950661,547082,547664,541708,193708031,
        ),
        'ES' => array(
            1951051031,1703495031,599364031,599379031,667049031,599367031,530484031,599391031,1748200031,
            599373031,1571262031,599376031,599385031,599382031,599388031,        
        ),
        'FR' => array(
            340855031,1571265031,206617031,197858031,468256,537366,578608,1058082,69633011,197861031,590748031,
            193711031,818936031,57686031,213080031,206442031,537366,340862031,192420031,1571268031,215934031,
            548012,548014,548014,578610,578608,548014,60937031,
        ),
        // 'IN' => array( 976389031, 976416031 ), 
        // 2.1.0+ Updated the list to the API Version 2013-08-01) from API Version 2011-08-01
        'IN' => array( 976389031, 976416031, 976419031, 976442031, 1951048031, 976392031, 1350380031, 1350387031 ),
        'IT' => array(
            1571280031,1571286031,411663031,412606031,412609031,433842031,635016031,818937031,524015031,
            1571292031,1748203031,412600031,524006031,412612031,523997031,412603031,524009031,
        ),
        'JP' => array(
            361299011,2277724051,2017304051,13331821,52391051,465610,562032,562002,3210991,388316011,57239051,
            161669011,13331821,85896051,2250738051,3839151,2381130051,2128134051,562032,2123629051,2016926051,
            637630,14304371,13331821,2130989051,561972,637872,324025011
        ),
        'UK' => array(
            83451031,248877031,60032031,66280031,1025612,505510,283926,560800,340834031,66280031,11052591,
            2016929051,193717031,341677031,11052591,213077031,77198031,505510,340837031,560800,11052591,1025614,
            1025616,319530011,11052591,712832,283926,283926,1025616,595312
        ),
        'US' => array(
            1036592, 2619525011, 2617941011, 15690151, 165796011, 11055981, 1000,301668, 4991425011, 
            2625373011, 493964, 16310101,3760931, 228239, 3880591, 133141011, 1063498, 
            2972638011, 599872, 10304191, 2350149011, 301668, 11091801, 1084128, 1063498, 493964, 
            1063498, 493964, 409488, 3375251, 468240, 493964, 130, 493964, 377110011, 13900851        
        ),    // caused error: 195208011, 3580501, 285080, 195211011, 404272, 508494
        'MX' => array(),
        'AU' => array(),
    );
    
    /**
     * Returns an array of root node IDs of the specified locale.
     * 
     * The nodes are divided up to 10 elements for the API request.
     * @remark      Not used at the moment.
     */
    static public function getRootNodes( $sLocale ) {
        
        if ( ! isset( self::$aRootNodes[ strtoupper( $sLocale ) ] ) ) {
            return array();
        }
        return array_chunk( 
            self::$aRootNodes[ strtoupper( $sLocale ) ], 
            10 
        );
        
    }
    
    /**
     * The list of marketplace domains.
     * 
     * This is used when the search API request has the category of 'Marketplace', the domain needs to be specified.
     * @since       2.1.0
     * @see         http://docs.aws.amazon.com/AWSECommerceService/latest/DG/MarketplaceDomainParameter.html
     */
    static public $aMarketplaceDomain = array(
        'DE' => 'www.javari.de',
        'JP' => 'www.javari.jp',
        'UK' => 'www.javari.co.uk',
        'US' => 'www.amazonsupply.com',
    );
    
    /**
     * Returns the market place domain url by the given locale.
     * 
     * @since       2.1.0
     */
    static public function getMarketplaceDomainByLocale( $sLocale ) {
        
        return isset( self::$aMarketplaceDomain[ $sLocale ]  )
            ? self::$aMarketplaceDomain[ $sLocale ]
            : self::$aMarketplaceDomain[ 'US' ];    // default
        
    }
    
    /**
     * Returns the JavaScript script of the impression counter.
     *
     * @since       3.1.0
     * @since       3.5.6       Supported SSL.
     * @return      string
     * @rmark       Some locales are not available.
     */
    static public function getImpressionCounterScript( $sLocale ) {
        $_sScript = isset( self::$aImpressionCounterScripts[ $sLocale ] )
            ? self::$aImpressionCounterScripts[ $sLocale ]
            : self::$aImpressionCounterScripts[ 'US' ]; // default
        return is_ssl()
            ? str_replace( 'http://', 'https://', $_sScript )
            : $_sScript;
    }
        /**
         * 
         * @remark      %ASSOCIATE_TAG% is a dummy associate id.
         * @since       3.1.0
         */
        static public $aImpressionCounterScripts = array(

            // https://associates.amazon.ca/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_m=A3DWYIK6Y9EEQB&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_10060771_2&rw_useCurrentProtocol=1
            'CA'    => '<script class="amazon_auto_links_impression_counter_ca" type="text/javascript" src="http://ir-ca.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=15"></script><noscript><img class="amazon_auto_links_impression_counter_ca" src="http://ir-ca.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
            
            // https://associates.amazon.cn/gp/associates/tips/impressions.html?ie=UTF8&%20=&pf_rd_i=assoc_help_t20_a2&pf_rd_m=A1AJ19PSB66TGU&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_3141918_2&rw_useCurrentProtocol=1
            // @remark      seems not available now at the date of 05/14/2018ir-in.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=31
            'CN'    => '<script class="amazon_auto_links_impression_counter_cn" type="text/javascript" src="http://ir-cn.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=28"></script><noscript><img class="amazon_auto_links_impression_counter_cn" src="http://ir-cn.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
           
            // https://partnernet.amazon.de/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_s=assoc-center-1&pf_rd_t=501
            'DE'    => '<script class="amazon_auto_links_impression_counter_de" type="text/javascript" src="http://ir-de.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=3"></script><noscript><img class="amazon_auto_links_impression_counter_de" src="http://ir-de.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',            
            
            // https://affiliate.amazon.co.jp/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t16_a8&pf_rd_m=AN1VRQENFRJN5&pf_rd_p=&pf_rd_r=&pf_rd_s=center-1&pf_rd_t=501&ref_=amb_link_10038521_1&rw_useCurrentProtocol=1
            'JP'    => '<script class="amazon_auto_links_impression_counter_jp" type="text/javascript" src="http://ir-jp.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=9"></script><noscript><img class="amazon_auto_links_impression_counter_jp" src="http://ir-jp.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
            
            // https://affiliate-program.amazon.co.uk/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_s=assoc-center-1&pf_rd_t=501
            'UK'    => '<script class="amazon_auto_links_impression_counter_uk" type="text/javascript" src="http://ir-uk.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=2"></script><noscript><img class="amazon_auto_links_impression_counter_uk" src="http://ir-uk.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
            
            // https://affiliate-program.amazon.co.uk/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_s=assoc-center-1&pf_rd_t=501
            'US'    => '<script class="amazon_auto_links_impression_counter_us" type="text/javascript" src="http://ir-na.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=1"></script><noscript><img class="amazon_auto_links_impression_counter_us" src="http://ir-na.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',

            // https://associados.amazon.com.br/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_m=A1ZZFT5FULY4LN&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_395484562_2&rw_useCurrentProtocol=1
            // @remark      seems not available at the date of 05/14/2018ir-in.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=31
            'BR'    => '<script class="amazon_auto_links_impression_counter_br" type="text/javascript" src="http://ir-br.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=33"></script><noscript><img class="amazon_auto_links_impression_counter_br" src="http://ir-br.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',
            
            // https://affiliate-program.amazon.in/gp/associates/tips/impressions.html?ie=UTF8&pf_rd_i=assoc_help_t20_a2&pf_rd_m=A1VBAL9TL5WCBF&pf_rd_p=&pf_rd_r=&pf_rd_s=assoc-center-1&pf_rd_t=501&ref_=amb_link_162366867_2&rw_useCurrentProtocol=1
            // @remark      seems not available at the date of 05/14/2018ir-in.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=31
            'IN'    => '<script class="amazon_auto_links_impression_counter_in" type="text/javascript" src="http://ir-in.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=31"></script><noscript><img class="amazon_auto_links_impression_counter_in" src="http://ir-in.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',

            // @since   3.5.6   Checked manually by changing the `o` url query parameter.
            'FR'    => '<script class="amazon_auto_links_impression_counter_fr" type="text/javascript" src="http://ir-na.amazon-adsystem.com/s/impression-counter?tag=%ASSOCIATE_TAG%&o=8"></script><noscript><img class="amazon_auto_links_impression_counter_us" src="http://ir-fr.amazon-adsystem.com/s/noscript?tag=%ASSOCIATE_TAG%" alt="" /></noscript>',

            // Not available
            // 'IT'    => '',
            // 'ES'    => '',
            // 'MX'    => '',
            // 'AU'    => '',
            
        );
}