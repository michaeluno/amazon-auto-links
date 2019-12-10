<?php
/*
 * Available variables:
 * 
 * $aOptions   - the plugin options
 * $aProducts  - the fetched product links
 * $aArguments - the user defined unit arguments such as image size and count etc.
 */

$_oUtil   = new AmazonAutoLinks_PluginUtility;
$_oOption = AmazonAutoLinks_Option::getInstance();
echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>'; 

?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php
    do_action( 'rss2_ns' );
    do_action( 'aal_action_rss2_namespace' );    // 3.5.4+
    ?>
>
<channel>
    <?php
        $sFeedTitle = __( 'Amazon Products', 'amazon-auto-links' ) 
            . (
                $_oUtil->getElement( $aArguments, array( 'label' ) )
                    ? ( ' ' . implode( ', ', $aArguments[ 'label' ] ) )
                    : ( $aArguments[ 'id' ] 
                        ? ' ' . get_the_title( $aArguments[ 'id' ]  )
                        : ''
                    )
            );
    ?>
    <title><?php echo strip_tags( $sFeedTitle ); ?></title>
    <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
    <link><?php bloginfo_rss( 'url' ) ?></link>
    <description><?php bloginfo_rss( 'description' ); ?></description>
    <lastBuildDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ); ?></lastBuildDate>
    <language><?php bloginfo_rss( 'language' ); ?></language>
    <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
    <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
    <?php 
        do_action( 'rss2_head' ); 
        if ( isset( $aProducts[ 'Error' ][ 'Message' ], $aProducts[ 'Error' ][ 'Code' ] ) ) {
            $aProducts = array();
        }
        foreach ( $aProducts as $_aProduct ) : 
            $sLabels = empty( $aArguments[ '_labels' ] )
                ? $_oUtil->getReadableLabelsByUnitID( $aArguments[ 'id' ] )
                : $_oUtil->getReadableLabelsByLabelID( $aArguments[ '_labels' ] );
            $_sGUID  = $aArguments[ 'unit_type' ] . '_' . $_aProduct[ 'ASIN' ] . '_' . $aArguments[ 'country' ];

    ?>    
    <item>
        <title><![CDATA[<?php echo $_aProduct[ 'title' ]; ?>]]></title>
        <link><?php echo esc_url( $_aProduct[ 'product_url' ] ); ?></link>
        <!--<comments></comments>-->
        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $_aProduct[ 'updated_date' ], false ); ?></pubDate>
        <!--<dc:creator><![CDATA[]]></dc:creator>-->
        <?php if ( $sLabels ): ?>
        <category><![CDATA[<?php echo $sLabels; ?>]]></category>
        <?php endif; ?>
        <guid isPermaLink="false"><?php echo $_sGUID; ?></guid>        
        <?php if ( ! $_oOption->get( 'feed', 'use_description_tag_for_rss_product_content' ) ) : ?>
        <description><![CDATA[<?php echo $_aProduct[ 'description' ]; ?>]]></description>
        <content:encoded><![CDATA[<?php echo "<div class='amazon-products-container'><div class='amazon-product-container'>" . $_aProduct[ 'formatted_item' ] . "</div></div>"; ?>]]></content:encoded>
        <?php else : ?>
        <description><![CDATA[<?php echo "<div class='amazon-products-container'><div class='amazon-product-container'>" . $_aProduct[ 'formatted_item' ] . "</div></div>"; ?>]]></description>        
        <?php endif; ?>
        <!--<wfw:commentRss></wfw:commentRss>-->
        <!--<slash:comments></slash:comments>-->

    <?php
        do_action( 'rss2_item' );
        do_action( 'aal_action_rss2_item', $_aProduct );    // 3.5.4+
    ?>
    </item>
    
    <?php endforeach; ?>
</channel>
</rss>