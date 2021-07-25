<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 *
 * Provides PA-API BR locale information.
 *
 * @since 4.3.4
 */
class AmazonAutoLinks_PAAPI50_Locale_BR extends AmazonAutoLinks_PAAPI50_Locale_Base {

    /**
     * @var string e.g. US, UK, CA etc.
     */
    public $sSlug = 'BR';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sServerRegion = 'us-east-1';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/locale-reference.html#locale-reference-for-product-advertising-api
     */
    public $sMarketPlaceHost = 'www.amazon.com.br';

    /**
     * @var string
     * @see https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */
    public $sHost = 'webservices.amazon.com.br';

    /**
     * @var string
     * @remark Portuguese - BRAZIL
     */
    public $sDefaultLanguage = 'pt_BR';

    /**
     * @var string
     * @remark Brazilian Real
     */
    public $sDefaultCurrency = 'BRL';

    /**
     * API license agreement URL.
     * @var string
     */
    public $sLicenseURL = 'https://associados.amazon.com.br/help/operating/agreement';

    /**
     * @return array
     */
    public function getLanguages() {
        return array(
            'pt_BR' => __( 'Portuguese - BRAZIL', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     */
    public function getCurrencies() {
        return array(
            'BRL' => __( 'Brazilian Real', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @remark No need to translate items.
     */
    public function getSearchIndex() {
        return array(
            'All'                       => 'Todos os departamentos',
            'Books'                     => 'Livros',
            'Computers'                 => 'Computadores e Informática',
            'Electronics'               => 'Eletrônicos',
            'HomeAndKitchen'            => 'Casa e Cozinha',
            'KindleStore'               => 'Loja Kindle',
            'MobileApps'                => 'Apps e Jogos',
            'OfficeProducts'            => 'Material para Escritório e Papelaria',
            'ToolsAndHomeImprovement'   => 'Ferramentas e Materiais de Construção',
            'VideoGames'                => 'Games',
        );
    }

}