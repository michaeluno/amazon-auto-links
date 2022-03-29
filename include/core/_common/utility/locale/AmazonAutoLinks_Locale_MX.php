<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * The Mexican locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_MX extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'MX';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown. Made it same as the ES locale.
     */
    public $sLocaleNumber = '33';

    /**
     * @var string e.g. www.amazon.com
     * @remark Override it.
     */
    public $sDomain = 'www.amazon.com.mx';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://afiliados.amazon.com.mx';

    /**
     * @var string
     * @remark Override it.
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAB/klEQVRIS2NkyGcQZ2Rh2MPAwKADxDhBo9xcfNIYcpr5WXjVMzIwXPnFwOLCyFjMcJmQ5SCTqO0AkJkgR4Ac8B/E0XTRwOvi044gdxIP/nv741W8fe9esPyoA4Z2CGyb6MugaurJoGqFmeLpkgYu7Sxj+PLhL4NVeC9GgqOZA64fmsHAzskIzDp8DO+f7Wf4+puFQUXLi+HD89MMjCxSDFr26WDH0MwBIMNvHJnFcHnvVAYlA3OGq4d2MmjbuQND4heDbcwsBiZmNto6YNXxgwx8Tzcy8P95xfD7508GbgFBsIUf/nIyfJQMYAiydKStA+6enMrw4cVFhlcP7zJwcPMw/PjyiYGZhZXh+d2bDJahxQxqlnm0dcDeWb4MAuKSDC/u3mDgExFn4OKHhAC4VGNiYTDynUZbB+zbVM7A//89w70LJ8FpABm8ZhBl8PBvpa0DsmZ1M2j/usYgyvmPQZz1BwMTjxDD488MDG8YBRkufBNlmJdRSFsH1K9dyXDz+VOwJRpcnxge/OBm+PGPGcw3VlBiKPUJoK0DXn/6xLDm1HGGp+/fMtx//YqBm52dQVlMgkFFXILBy9CYgZOVxtkQOc6P3b7JoCcrz8DDwUG/khDDJhwCNC0JiXHEqANICYEBa5QC68zzjNBm+T5gvGrhi1vqt4r/n//NwOoJAHNnIpGip7nBAAAAAElFTkSuQmCC';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Mexico', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Mexico', 'amazon-auto-links' );
    }

}