<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * The Brazilian locale class.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale_BR extends AmazonAutoLinks_Locale_Base {

    /**
     * The locale code.
     * @var string e.g. US, IT, UK, JP
     */
    public $sSlug = 'BR';

    /**
     * Two digits locale number.
     * @var string
     * @remark Unknown
     */
    public $sLocaleNumber = '32';

    /**
     * @var string e.g. www.amazon.com
     */
    public $sDomain = 'www.amazon.com.br';

    /**
     * @var string
     */
    public $sAssociatesURL = 'https://associados.amazon.com.br';

    /**
     * @var string
     */
    public $sFlagImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAYCAYAAACbU/80AAAExElEQVRIS71Wa2wUZRQ9s7uz7+52u7RbC7S2sViIqEBtJDWiVn8ZfvhIU40KGpsAgahRo8T4IsFUCMGg8REBIdQfBgli/EeVqKlFQyugaAiiWbpdut1Hu+/ZnZ0Z7ze7Lftq2aLhSybfZHdm7rnn3Hvux+GRhrXguE8BzoXruhQfFKWXw6MLx689uJKDzF0jdMVHABZlv/JApQTQ47IMSBna6QIF12oBjY4u2itdx33qk/MDoLDgFDSdAIQYXJoMEpKMqMYIGK0Ab8qC4CpgZN4AZAnIpIFUDIgF8dACIz5Z04poWsJTJ85jkDDBUgMYCIiOJxCaubmoGMB01qIAJKZgE0LYs7IZ65bVzgSQZAU7hr14808vROsCwGQjNogVVZJZ2KgIgEJaq1nH1azvsXA4sKYFTXZD2exGfHE8+cNF/CFTcIuD2LBQfVBtlGNjTgBq1kQ5yzoZhjERxPalLjx3W11WXla2tLN7rihDISPjlaExvD8agaKyUUWSEGANkySPjVkBsKwlMZt1PIQVujQO3dWINoceKVFCQsjgL78LewbX47RvFUwGHTqbf8OLdx9Ek8M7w8xxdwxP/zyKMR3JYWZssAJltZEDUQqgLpd1irKOQBsLYGuLDW+014LXcGrwyUgKp9yL8PZAH4ZPK1AsepjsRtir9FjoEHD4iZdwY83lmTxDgoRNg5fxRZA6p4rYUAtUn62NEgBdVFRikrKeRKscwsGOeqyuJ9S5lRBEeANxhBf/guWtN8DtTmBf/xj2fxOAn+PBO83oXjmMfd3vgNdpoclrxc8vRLD5bBBTJophrqYCJUkG/EU+cJ8TnBDBhpowdi63wcIXVm8ylYEvlMRbPw1gXZcLd7bTh2il0zK+PDqO9w778Y+UxujrPbARM1pt4fuXYhmsH4nihOgkz6C6+DZQCoBPhbGtIYKXl5hR9D5EKq5oQkTLu0cR9ihosyvY2F2Lnu4G6HNg+/o92NLWiTqHiVgo9AFWtx/9LWCL2wbZQAC+CxYB6CKNmASxEFZrp0gCO1qrqYVyS6HOyEgU9OvtOHauE6GIADmYgAsiXtvYgFtvsWJH/1fY//BWOKkudHkALkxl0Hsqgu9FO2AlBphHlDBw/3QREghqPXM8gL4lRmxeZixoNXd4MR478hn8MStixMhkNIUU7Y6qGI493ovbGydgMfLUdRwyZFC7zpFsF1MQzJSgmQCoBkWJDUzMMgvUQUPmQ17P2vBeYxIHOsxorLqi6aVwE3adfAEnPXcgTdKscg3h+Y7duMnpUanXUvBfgxKeHU5iRCIzUi2amRLrgJw0VzciNnSybNiSfuy+WY9nWqmPS9b0SM7+QZ2HbWfT2OkhE7UU2XL+kKrIiosG0IOWJPa286g3l/f3H8dl9J4RcZ4jqpkV6ynr2QZTRQBYSjMjmNigYeQU/PhgqRY9zVeqPEKKvXpGwscTPNkvFRkbRqr9zjGaKwagEps7hLDBJETVwdRdncSHKzgMBRRs+l3BqI50ZiZjMJPW/+c4ztecscHmBGtXmhU2RUBEoixZUD1dzOHmGsH535ofAwUoskcydjJiOyssFlSl+yqHkPIA/suhtExTzO8nL5c9lmv20nvkRNd1eaHIG/4FX4sKA6uY5z8AAAAASUVORK5CYII=';

    /**
     * @return string The country name.
     * @since  4.5.0
     */
    public function getName() {
        return __( 'Brazil', 'amazon-auto-links' );
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->sSlug . ' - ' . __( 'Brazil', 'amazon-auto-links' );
    }

}