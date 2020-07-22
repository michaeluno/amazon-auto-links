<?php

class AmazonAutoLinks_Proxy_Fetch_socks5 extends AmazonAutoLinks_Proxy_Fetch_Base {

    protected $_sURL = 'https://www.proxy-list.download/api/v1/get?type=socks5';
//    protected $_sURL = 'https://www.amazon.com/Best-Sellers/zgbs';
//    protected $_sURL = 'https://www.amazon.it/gp/bestsellers';
//    protected $_sURL = 'https://www.amazon.com/gp/bestsellers';
    protected $_sScheme = 'socks5://';

}