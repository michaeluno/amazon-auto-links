<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * The Spanish locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_ES extends AmazonAutoLinks_Locale_EuropeanUnion {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'ES';

    /**
     * Two digits locale number.
     * @var string
     */
    public $sLocaleNumber = '30';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.es';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://afiliados.amazon.es';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAACgklEQVRIS+2WXUhTYRzGf2dnburWUGbOTJYtS4UuQguJLrowyoLoIij6vLOCyAstIi+iiMDwoyAKog+EIgQLQUu7yIGWhtnE0qY1JPJ76azmbDXd1tlSITC2E6I3/uFcHHjP8/z+7/u873mFYpHdCrgNGKRnIcvug1yhVGR4EcxnGrUHAPyBt/z16QvZPWWdXUG/JYDFn4HXF/CLEUoy9mz6KwN+KaJDY1r0OjdqlZcPNj8Pq7Us0wqcPu4Kju2161AxRoJBKTs/rY9e/cnAvwCcLiWt3SsZdmhYoXdR/6QdzfIUEpMTWZXgZHQMJjw6YnU/yc78jDZ6UhZESIAZtR7HZmx1I2Tcf8rIaiOmZw10tLyku60GhVdg3/a3qFTBjSSrwgLwScswlVKHtSCP1RWPmYyOIq5/hF/uCcxVN9l58Aw+2xEUnh5Z5oHBIQF8PoFqcyw5uRVYm56TbuvD6XGjvFqGTadl4NRR9h47T3NVIVlrGhFFiVZGhQSw9cbQ1JHE/pOl2Ps/kpyaifvWDTznzuKJUmMpKSTnQAHV5ZdIM7SwziiFQkaFBOjsiaPo8nvuNXZgtZhxOoYZ/zLA6LW7xGRvRJ21JQhwJe8QO7Y62JDmlGEfxhIM2QWKr3spqjBjfVNPe1NN0MA75UVUisQnrWWXlIHaBxfJMtai14vzCxBQe9cVgWlbJY21lXzqasYvhV2r09D2og1jSir5JeV8tRxGr+mTZR5WCGcVhQg8Hi/fXZE0WAykm8YxJX4jUu1FUEgHkF/e/p/RDZmBuVpy/VBJB45HdrdzffBfAPPiPC0yC7B0H1iagUW+lA4K09fyO1I44+cz5WFoDUq/rxO/AdFXJ6/Zeh0GAAAAAElFTkSuQmCC';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Spain', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Spain', 'amazon-auto-links' );
    }

}