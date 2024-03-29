<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Provides PA-API 5.0 common parameters.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_PAAPI50___Payload extends AmazonAutoLinks_PluginUtility {

    static public $aResources = array(
            'BrowseNodeInfo.BrowseNodes',
            'BrowseNodeInfo.BrowseNodes.Ancestor',
//                    'BrowseNodeInfo.BrowseNodes.SalesRank',
        'BrowseNodeInfo.WebsiteSalesRank',
        'CustomerReviews.Count',
        'CustomerReviews.StarRating',
//                'Images.Primary.Small',
        'Images.Primary.Medium',
        'Images.Primary.Large',
//                'Images.Variants.Small',
        'Images.Variants.Medium',
        'Images.Variants.Large',
        'ItemInfo.ByLineInfo',
        'ItemInfo.ContentInfo',
        'ItemInfo.ContentRating',
        'ItemInfo.Classifications',
        'ItemInfo.ExternalIds',
        'ItemInfo.Features',
        'ItemInfo.ManufactureInfo',
        'ItemInfo.ProductInfo',
        'ItemInfo.TechnicalInfo',
        'ItemInfo.Title',
        'ItemInfo.TradeInInfo',
//                    'Offers.Listings.Availability.MaxOrderQuantity',
//                    'Offers.Listings.Availability.Message',
//                    'Offers.Listings.Availability.MinOrderQuantity',
//                    'Offers.Listings.Availability.Type',
//                    'Offers.Listings.Condition',
//                    'Offers.Listings.Condition.SubCondition',
        'Offers.Listings.DeliveryInfo.IsAmazonFulfilled',
        'Offers.Listings.DeliveryInfo.IsFreeShippingEligible',
        'Offers.Listings.DeliveryInfo.IsPrimeEligible',
//                    'Offers.Listings.DeliveryInfo.ShippingCharges',
//                    'Offers.Listings.IsBuyBoxWinner',
//                    'Offers.Listings.LoyaltyPoints.Points',
//                    'Offers.Listings.MerchantInfo',
            'Offers.Listings.Price',
//                    'Offers.Listings.ProgramEligibility.IsPrimeExclusive',
//                    'Offers.Listings.ProgramEligibility.IsPrimePantry',
//                    'Offers.Listings.Promotions',
            'Offers.Listings.SavingBasis',
//                    'Offers.Summaries.HighestPrice',
            'Offers.Summaries.LowestPrice',
//                    'Offers.Summaries.OfferCount',
//                    'ParentASIN',
//                    'RentalOffers.Listings.Availability.MaxOrderQuantity',
//                    'RentalOffers.Listings.Availability.Message',
//                    'RentalOffers.Listings.Availability.MinOrderQuantity',
//                    'RentalOffers.Listings.Availability.Type',
//                    'RentalOffers.Listings.BasePrice',
//                    'RentalOffers.Listings.Condition',
//                    'RentalOffers.Listings.Condition.SubCondition',
//                    'RentalOffers.Listings.DeliveryInfo.IsAmazonFulfilled',
//                    'RentalOffers.Listings.DeliveryInfo.IsFreeShippingEligible',
//                    'RentalOffers.Listings.DeliveryInfo.IsPrimeEligible',
//                    'RentalOffers.Listings.DeliveryInfo.ShippingCharges',
//                    'RentalOffers.Listings.MerchantInfo',
//                    'SearchRefinements'
    );
}