<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * The Saudi locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_SA extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'SA';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown.
     */
    public $sLocaleNumber = '01';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.sa';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://affiliate-program.amazon.sa';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAFHUlEQVRIS8VWa0wcVRT+Znf2CZVSZKki1dKERFqgNFKrbRARrbG0NVZDoaaE9BEp8RFfbbCxCSo1NsZam/6w/rK20IeWKJpILdK0VVF8JkWBtKbU4qKlWTcLuzs7s+M5d3aHIrLgH7zZ2Zm5M5nznXO+77tXwgashIT9ADLomM4xCB2bJGyE938IHk90kAHofLe0cqmZvc5T/IvSlR6l49rCxJ6JKeOaKkg/8WfcGJ8UQ5LoicUizuZ7NH+26azxfDwADgpENQ2aqiHKRzQa/5z4uM4vmDHiFxyIg8cAxIBZKLhFtsIqy7BYY0ASAeDso1oUmhKBElKg0pnvzVQFAL4dUxYzW0p1zDMObnPYYXPaIdtskKxcK2niCnB2WkSFEgwjPBISAPLTCvDH8CAGApdF4uXzVuJbbxfUqIrSm+/BkV+aUTqnDD/8+T2GgkPImpGFjKTZ8A4PwBschOy0wZnkEiAYUAIAlD31XQ0rCA2HCEAQD2c/gleKX0VYC2P+/hxU5q7DS8WNWP9RFQoyFuL+7Afw4LEV6Kr5CacvncITJ7ZgZ8kuVNxaiZHIMKpaK9AT6IUz2QWHywmrTRZ8+FcOiPKrUUQYQGAEClXgk4fa6EMjyPcU4J0f38aSG+/AgvQ8LG8uxebCWlGFbR3P4fnbt9F8Pta3VuHQqsM4efEzHO95Hz7FJzLnCjjokBkA8SIBAE30ngFk2W9CkWcx7p27HMVZd8V4r+PEr5/ikr8feekFaOn7ABnuDHR5v0FV7qO4EryCtblVsEpWoaD3ug+gsevlUQB2G5iYiSsQClMLgqICt6UXYXfZXqQ4UhBSQ2g4swOeJA+2LqmHL+zDTMdMaFSFy4EBHDz3LlKdqXissM6U4eGfm/Bi53YDgJsq4EgAgJnOjI+EIwRgBGHiwZ6SvVA0BXu6duPgqmaspn63rGnF7OQbsOHjarxe9iYGA15s7XhWzPddpX7LLpz39aFkTim2tG1G+0C74IDd7YScqAIMmzXPzGcFhKkKFfPWYqGnEE+ffBLnNvbitc6dyE2bLzix4uh92HX3G0h3e1Ddug61i+pQt+hxyBYb9n33Fp4qegZrWlaj299NANwGgEQkFN52jQy5DW7dhbxZ+TjV3447M5dB1VV8PdCJZHsyApEAHFYH7LIdw8R4HtySZZnF+LDvuCBrr7+PZCjDwQAml6EBgN1PIR5wC5RgSDgi27Jpf2w2MZsVZ3JAZrZIgCqoUxvZp3iee+6gzLn/NrqW2A0nNSKVjUghEhIRSRFsTGMAxHzfsH7JsNiYDXNwbiMnwmw3ALAEjf4LsBMBYB/QNTIi5gBlzipgQvK6wCmNun58aYhVgj8qqiIJ6WUmZWJuSja++v1LQJaMCjAAsmQBNhEAw4hIhoEgIlSF+sXbUb2gxpTVPy+GSPf9/ovo6P8cx3qO4BYKfKD8ECySBWd+O41NbTWwOGTDil12sShN4oSGEYXJiHKSctBUflQQbSpD0zUElIDwjPho+GIHms43/Qcrph6qVHaWYYSIqI7r/8RQ3LIbaa7rkWJPIZXMEAvjBf8F+PS/zMVo0gow2VgF3HtVMQjIC5Qxxi/BY+DEtwnxpZoQWKnn3HuWoJVNaCoqEFIk4onNiJDUJIHj8GIbFfN9qgArgbO22qwiOJcloQyNPDkV+hdbsilkHnvFgDm6ZTNlGveJ+NaN3jIXI7mWfCYCw0Wmecg2aNKsejT4r+IF8p5pBcHBr0tF49/sacxm4rbOWwAAAABJRU5ErkJggg==';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Saudi Arabia', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Saudi Arabia', 'amazon-auto-links' );
    }

}