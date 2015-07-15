<?php
/*
 * Available variables:
 * 
 * $aOptions   - the plugin options
 * $aProducts  - the fetched product links
 * $aArguments - the user defined unit arguments such as image size and count etc.
 */


echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>'; ?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php do_action( 'rss2_ns' ); ?>
>
<channel>
    <?php
        $sFeedTitle = __( 'Amazon Products', 'amazon-auto-links' ) 
            . (
                isset( $aArguments[ 'label' ] ) && ! empty( $aArguments[ 'label' ] )
                    ? ( ' ' . implode( ', ', $aArguments[ 'label' ] ) )
                    : ( $aArguments[ 'id' ] 
                        ? ' ' . get_the_title( $aArguments[ 'id' ]  )
                        : ''
                    )
            );
    ?>
    <title><![CDATA[ <?php echo $sFeedTitle; ?>]]></title>
    <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
    <link><?php bloginfo_rss('url') ?></link>e
    <description><?php bloginfo_rss( "description" ); ?></description>
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
                ? AmazonAutoLinks_PluginUtility::getReadableLabelsByUnitID( $aArguments[ 'id' ] )
                : AmazonAutoLinks_PluginUtility::getReadableLabelsByLabelID( $aArguments[ '_labels' ] );
            $_sGUID  = $aArguments[ 'unit_type' ] . '_' . $_aProduct[ 'ASIN' ] . '_' . $aArguments[ 'country' ];
            
    ?>    
    <item>
        <title><![CDATA[<?php echo $_aProduct[ 'title' ]; ?>]]></title>
        <link><?php echo $_aProduct[ 'product_url' ]; ?></link>
        <comments></comments>
        <pubDate><?php // echo date( 'D, d M Y H:i:s +0000', $_aProduct[ 'created_at' ] ); ?></pubDate>
        <dc:creator><![CDATA[<?php // echo $_aProduct['user']['name']; ?>]]></dc:creator>
        
        <category><![CDATA[<?php echo $sLabels; ?>]]></category>
        <guid isPermaLink="false"><?php echo $_sGUID; ?></guid>        
        <description><![CDATA[<?php echo $_aProduct[ 'description' ]; ?>]]></description>
        <content:encoded><![CDATA[<?php echo "<div class='amazon-product-container'>" . $_aProduct[ 'formatted_item' ] . "</div>"; ?>]]></content:encoded>
        <wfw:commentRss></wfw:commentRss>
        <slash:comments></slash:comments>

    <?php do_action( 'rss2_item' ); ?>
    </item>
    
    <?php endforeach; ?>
</channel>
</rss>