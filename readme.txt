=== Auto Amazon Links - Amazon Associates Affiliate Plugin ===
Contributors:       Michael Uno, miunosoft
Donate link:        http://en.michaeluno.jp/donate
Tags:               amazon, amazon affiliate, amazon associate, amazon affiliates, amazon associates, amazon ads, automation, ads, advertisement, affiliate, affiliates, marketing, monetization, monetize, revenues, revenue, income, widget, widgets
Requires at least:  3.4
Requires PHP:       5.2.4
Tested up to:       6.2.0
Requires MySQL:     5.0.3
Stable tag:         5.3.0
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

Generates Amazon affiliate links of products just coming out today. Pick categories and they appear even in JavaScript-disabled browsers.

== Description ==

= Display Amazon Associates Affiliate Links with Minimal Effort =

Still manually searching products and pasting Amazon affiliate links in WordPress posts? What happens if the products get outdated? With this plugin, you do not have to worry about it nor trouble doing such repetitive tasks. Just pick categories that suit your site and it will automatically display the links of decent products just coming out from Amazon today.

Auto Amazon Links is a WordPress plugin for affiliates to create Amazon affiliate links. The links are tagged with your Amazon Associates ID. All the Amazon locales are supported and it works even on JavaScript-disabled browsers. Insert the ads as widget, place shortcode, or use auto-insert to display units automatically where the links should appear.

If you want to search for a specific product, yes, you can do that too. If you are good at HTML and CSS coding and know a little about PHP, you can create your own template! That means you can design the layout.

Display Amazon affiliate links along with your posts with this plugin to earn commissions with minimal effort.

= Display a particular product in a post =
If you want to simply display your desired specific products in a post, don't worry. You can do that too. Just paste the product URL into the post editor. No shortcode is required.

= See How Amazon Affiliate Links are Displayed =

[youtube https://www.youtube.com/watch?v=mpDCcp4KBZg]

= Supporting All the Amazon Associates Locales =
Includes Australia, Brazil, Canada, China, France, Germany, India, Italy, Japan, Mexico, Netherlands, Poland, Singapore, Saudi Arabia, Spain, Sweden, Turkey, United Arab Emirates, United Kingdom, and the United States. China is supported for the category unit type.

= Works without JavaScript =
Some visitors turn off JavaScript in their browsers for security reasons and most ads including Google Adsense will not show up to them. But this one works!

= Automatic Insertion in Posts and Feeds =
Just check where you want the product links to appear with auto-insert.

- **Static Contents Conversion** - If you want the product link to be static, it is possible. This means that if you deactivate the plugin, the converted contents will remain.
- **Detailed Visibility Criteria** - You can enable/disable product links on the pages you want or do not want by post ID, taxonomy, page type, and post type.

= Customizable Buttons =
Your site visitors are more likely to click buttons than regular text hyperlinks. Define your custom buttons and insert them into the unit output.

The plugin lets you design buttons through UI and prepares several default buttons for you so that you can modify them rather than create your own from scratch.

= Geo-targeting =
You can transform your Amazon affiliate links into the ones of the locale that the site visitor resides, presumed by IP address. So you won't miss commission fees from visitors coming outside of your country.

= Auto Link Conversion =
Hyperlinks to Amazon products in posts and comments can be transformed into your associate affiliate links. This is useful if your site allows guests to post contents that include Amazon links.

= Gutenberg Block =
The plugin has a Gutenberg block that lets you pick units you created, which can save a little time than typing the shortcode.

= Widgets =
Place the widget in the sidebar and select the unit you created. The product links will appear where you want.

- **By Units** - choose the created units to display in the widget.
- **Contextual Search** - with this, you don't have to create a unit. It will automatically search products relating to the currently displayed page contents.

= Shortcode and PHP Function =
Insert the ads in specific posts and pages with the shortcode. If you want to insert in the theme template, use the PHP code the plugin provides to produce the outputs.

= Filtering Products =
You can filter out certain products you don't want to display with a black and white list by description, title, and ASIN.

= RSS and JSON Unit Feeds =
By subscribing to the product RSS/JSON feeds of the units you create, you can import them from other sites.

If you have a website that can display RSS feed contents, just create a WordPress site somewhere with this plugin and fetch the feed from the site. If you are an App developer, you can just display the items from the feed without programming an API client.

= Various Unit Options =

- **Image Size** - The size of thumbnails can be specified. It supports up to 500 pixels large with a clean resolution.
- **Sort Order** - Shuffle the product links so that the visitor won't get bored as it gives refreshed impression.
- **URL cloaking** - You can obfuscate the link URLs so it helps to prevent being blocked by browser Ad-blocking add-ons.
- **Load with Javascript** - Decides whether to display units with JavaScript.
- and more.

= Customizing Outputs =
Besides the **Item Format** unit option which lets you design the output of a unit, you can create a custom template. This gives you freedom of customization and lets you achieve a more advanced and detailed design.

= Unit Types =
- **Category** - picks your category that matches your site topic.
- **Product Search** - creates a unit by performing product searches.
- **URL** - lists items from an external web source.
- **PA-API Product Search** - creates a unit of a search result using PA-API.
- **PA-API Item Look-up** - displays specific products.
- **PA-API Custom Payload** - is for more complex PA-API queries.

= Supported Languages =
- English
- Japanese
- German
- Italian
- Spanish

= Getting Started =
Please see the **Installation** section.

== Installation ==

= Install =
<h5><strong>Installing through the UI of WordPress</strong></h5>
1. Navigate to **Dashboard** -> **Plugins** -> **Add New**.
1. Type "*Auto Amazon Links*" in the search bar.
1. *Auto Amazon Links* should be listed and click on **Install Now**.
1. The **Activate** button will appear and press it.

<h5><strong>Uploading the zip file</strong></h5>
1. Download [amazon-auto-links.zip](https://downloads.wordpress.org/plugin/amazon-auto-links.latest-stable.zip).
1. Navigate to **Dashboard** -> **Plugins** -> **Add New**.
1. Click on the **Upload Plugin** and upload the zip file.
1. The **Activate Plugin** button will appear and press it.

<h5><strong>Using FTP or Control Panel File Manager</strong></h5>
1. Extract the files of [amazon-auto-links.zip](https://downloads.wordpress.org/plugin/amazon-auto-links.latest-stable.zip) to the `wp-content` directory. The plugin directory named `amazon-auto-links` containing files should be placed inside `wp-content`. The structure should look like,
 - /wp-content/amazon-auto-links/amazon-auto-links.php
 - /wp-content/amazon-auto-links/readme.txt
 - continues...

= Getting Started =
To get started, set up your Amazon Associates ID and create a unit, then display it.

<h5><strong>Setting up Amazon Associates ID</strong></h5>
For the very beginning, you need to set-up your Amazon Associates ID (tag).

1. Navigate to **Dashboard** -> **Auto Amazon Links** -> **Settings** -> **Associates**.
1. There, select your locale and enter your Amazon Associates ID (tag). Then save.

<h5><strong>Creating a Unit</strong></h5>
There are several ways to display product links. You need to create a unit which defines what kind of products to display first. Then tell the plugin which unit you want to display. It is recommended to create a category unit to understand how it works.

1. Navigate to **Dashboard** -> **Auto Amazon Links** -> **Add Unit by Category**.
1. It will ask some options and lets you pick some categories. After selecting some categories and proceeding, a category unit will be created.
1. It will take you to unit editing screen. You'll see lots of options but you can leave them to the default.

<h5><strong>Shortcode</strong></h5>
1. Navigate to **Dashboard** -> **Auto Amazon Links** -> **Manage Units**.
1. Copy the shortcode in the list, looking like `[amazon_auto_links id="nnn"]` where _nnn_ is your unit ID.
1. Paste it in your post.

<h5><strong>Gutenberg Block</strong></h5>
1. After creating a unit, open the Gutenberg editor, the WordPress default post editor.
1. Type "Amazon" in the block search bar, then find "**Auto Amazon Links: Unit**".
1. When selecting it, you'll see a list of units you created. Pick one and that's it.

<h5><strong>Auto-insert</strong></h5>
Auto-inserts allows you to insert units on your specified area of your site.
1. After creating a unit, navigate to **Dashboard** -> **Auto Amazon Links** -> **Manage Auto-insert**.
1. Click on **Add New Auto-insert**.
1. Pick your units and check **Post / Page Content** in the **Areas** field.
1. Save and create an auto-insert. Check your site and see at the bottom of your posts whether Amazon product links are displayed.

<h5><strong>Widgets</strong></h5>
The plugin has two widgets, `Amazon Auto Links by Unit`, which lets you pick your units, and `Amazon Auto Links - Contextual Products` which displays Amazon products related to displayed contents on the page.

In order to use these widgets, you need to install the [Classic Widgets](https://wordpress.org/plugins/classic-widgets/) plugin.

1. After creating a unit, navigate to **Dashboard** -> **Appearance** -> **Widgets**.
1. From **Available Widgets**, drag the **Amazon Auto Links by Unit** widget and drop it to one of the sidebars.
1. Pick a unit and save.
1. Confirm the unit is displayed in the sidebar.

<h5><strong>oEmbed</strong></h5>
You can display a formatted Amazon product link by pasting a product URL in the post editor.

1. Navigate to **Dashboard** -> **Auto Amazon Links** -> **Settings** -> **Embed**.
1. Make sure the oEmbed option is enabled there.
1. Navigate to **Dashboard** -> **Posts** -> **Add New**.
1. Paste an Amazon product URL in the editor such as `https://www.amazon.com/dp/1118987241`.
1. You should see formatted product link with a thumbnail.

== Frequently asked questions ==

= Do I need Amazon Associate ID to use this plug-in? =

Yes. Otherwise, you don't get any revenue. You can get it by signing up for [Amazon Associates](https://affiliate-program.amazon.com/).

= Do I need API Keys? =

For the category unit type, no, but for PA-API unit types, yes. You need to issue a pair of API keys on the Amazon Associates logged-in page.

For that, you need to have an account with [Amazon Product Advertising API](https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html). The keys can be obtained by logging in to [Amazon Web Services](http://aws.amazon.com/) and you need to get **Access Key ID** (public key) and **Secret Access Key** (private key).

More detailed instruction, please see [Register for Product Advertising API](https://webservices.amazon.com/paapi5/documentation/register-for-pa-api.html).

= Is the plugin compatible with PA-API 5? =
Yes. The plugin is compatible with PA-API (Amazon Product Advertising API) 5.0. The PA-API 4 is no longer available as of 10/31/2019 so if you are still using API keys of the old API, you need to reissue them.

= I'm migrating from Amazon Associates Link Builder (AALB). Can this plugin display products with their shortcodes? =
Yes, enable the option by navigating to **Dashboard** -> **Auto Amazon Links** -> **Settings** -> **3rd Party** -> **Amazon Associates Link Builder**.

There you also want to set the __Template Conversion__ option. Make sure you enable your desired Auto Amazon Links templates in **Dashboard** -> **Auto Amazon Links** -> **Templates**. Then reload the **3rd Party** screen. There, you'll see active templates are listed for the conversion option.

= What does a Unit mean? =

A unit is a set of rules that defines how Amazon products should be displayed.

When you display Amazon products, you would specify a unit and the plugin will generate outputs based on the rules defined in the unit.

= What would be the benefit to upgrade to the Pro version? =

With the Pro version, unlimited numbers of units can be created. Also, the number of categories per unit, the number of items to display per unit are unrestricted as well. Please consider upgrading it. [Auto Amazon Links Pro](https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/) As of Pro 2.0.6, links can be displayed in multiple columns.

= I get a blank white page after adding a unit to the theme. What is it? What should I do? =

It could be the allocated memory capacity for PHP reached the limit. One way to increase it is to add the following code in your wp-config.php or functions.php
`define( 'WP_MEMORY_LIMIT', '128M' );`
The part, 128M, should be changed accordingly.

= I want to display product links horizontally in multiple columns. Is it possible? =

Yes, with [Pro](https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/)!

= I have a feature request. Would you implement that? =
Post it from [here](https://github.com/michaeluno/amazon-auto-links/issues/new?assignees=&labels=enhancement&template=feature_request.yml).

= I get Amazon product links everywhere on the site after creating some units. How can I restrict them to certain pages? =
Go to **Dashboard** -> **Auto Amazon Links** -> **Manage Auto-insert**. There turn off unnecessary auto-insert items. You can edit their definitions and define where units should be displayed.

= My credentials do not seem to be authenticated. How can I check if my access keys are the correct ones? =
Try [Scratchpad](https://webservices.amazon.com/paapi5/scratchpad/) to make sure your keys work there as well.

= Is the China locales supported? =
For the category unit type, yes. But for the search and contextual unit types, no as PA-API 5 does not support it.

= Is this plugin Amazon Auto Links? =
Yes, that is the former name of this plugin and it is now Auto Amazon Links.

== Other Notes ==

= Shortcode and Function Parameters =
The plugin provides means to display Amazon product links by manually inserting a piece of code into your post or a theme file. For posts, it's called _shortcode_. For theme files, you need to place a PHP function. Using these, you even don't have to create a unit.

- Shortcode:
  - `[amazon_auto_links]`
- PHP Functions:
  - `do_action( 'aal_action_output' );`
  - `apply_filters( 'aal_filter_output' );`

They both takes the following arguments.

<h5><strong>id</strong> - the unit ID</h5>

`
[amazon_auto_links id="123"]
<?php do_action( 'aal_action_output', array( 'id' => 123 ) ); ?>
<?php echo apply_filters( 'aal_filter_output', '', array( 'id' => 123 ) ); ?>
`

<h5><strong>label</strong> - the label associated with the units</h5>

`
[amazon_auto_links label="WordPress"]
<?php do_action( 'aal_action_output', array( 'label' => 'WordPress' ) ); ?>
<?php echo apply_filters( 'aal_filter_output', '', array( 'label' => 'WordPress' ) ); ?>
`

<h5><strong>asin</strong> - ASINs (product IDs) separated by commas (`,`).</h5>

`
[amazon_auto_links asin="B016ZNRC0Q, B00ZV9PXP2"]
<?php do_action( 'aal_action_output', array( 'asin' => 'B016ZNRC0Q, B00ZV9PXP2' ) ); ?>
<?php echo apply_filters( 'aal_filter_output', '', array( 'asin' => 'B016ZNRC0Q, B00ZV9PXP2' ) ); ?>
`

<h5><strong>search</strong> - Search keywords separated by commas (`,`).</h5>

`
[amazon_auto_links search="WordPress"]
<?php do_action( 'aal_action_output', array( 'search' => 'WordPress' ) ); ?>
<?php echo apply_filters( 'aal_filter_output', '', array( 'search' => 'WordPress' ) ); ?>
`

When the `search` argument is specified, the following arguments can be used.

- `SearchIndex` - Filters search results by category. For accepted values, see the [locale reference](https://webservices.amazon.com/paapi5/documentation/locale-reference.html#topics). For example, the [US locale](https://webservices.amazon.com/paapi5/documentation/locale-reference/united-states.html#search-index), e.g. `[amazon_auto_links search="Oven" SearchIndex="Electronics"]`
- `Sort`/`SortBy` - (PA-API required) Sort order. Accepts the following values: `AvgCustomerReviews`, `Featured`, `NewestArrivals`, `Price:HighToLow`, `Price:LowToHigh`, `Relevance`. For the details of each value, see [here](https://webservices.amazon.com/paapi5/documentation/search-items.html#sortby-parameter). e.g. `[amazon_auto_links search="WordPress" sort="AvgCustomerReviews"]`
- `BrowseNode`/`BrowseNodeId` - (PA-API required) Filters search results by category ID.
- `Availability` - (PA-API required) Filters search results to items with the specified product availability status. Accepts `Available` or `IncludeOutOfStock`. See [details](https://webservices.amazon.com/paapi5/documentation/search-items.html#availability-parameter).
- `MerchantId`/`Merchant` - (PA-API required) Filters search results to items with the specified merchant. See [details](https://webservices.amazon.com/paapi5/documentation/search-items.html#merchant-parameter).
- `Condition` - (PA-API required) Filters search results to items with the specified product condition. Accepts `Any`, `New`, `Used`, `Collectible` or `Refurbished`. See [details](https://webservices.amazon.com/paapi5/documentation/search-items.html#condition-parameter).
- `MaximumPrice`/`MaxPrice` - (PA-API required) Filters search results to items with a price below the specified price. The value needs to be formatted in lowest currency denomination. For example, in the US marketplace, set `1234` for $12.34.
- `MinimumPrice`/`MinPrice` - (PA-API required) Filters search results to items with a price above the specified price. The value needs to be formatted in lowest currency denomination. For example, in the US marketplace, set `1234` for $12.34.
- `MinPercentageOff`/`MinSavingPercent` - (PA-API required) Filters search results to items with a specified discount percentage. e.g. `[amazon_auto_links search="shoes" MinSavingPercent=20]` where `20` denotes 20 percent-off.
- `MinReviewsRating` - (PA-API required) Filters search results to items with a customer rating above the specified value. Accepts a positive integer from `2` to `5`. e.g. `[amazon_auto_links search="shoes" MinReviewsRating=4]` for products with a rating above 4.
- `CurrencyOfPreference` - (PA-API required) Preferred currency. For accepted values, see the [locale reference](https://webservices.amazon.com/paapi5/documentation/locale-reference.html).
- `LanguagesOfPreference` - (PA-API required) Preferred language specified in the ISO 639 language code. For accepted values, see the [locale reference](https://webservices.amazon.com/paapi5/documentation/locale-reference.html).

The `id`, `asin` and `search` arguments cannot be used together.

These shortcode argument names are case-insensitive, meaning `maxprice` is also accepted for `MaxPrice`.

Optionally, the following arguments may be set.

- `country` - (string) the locale of the store. Accepted values are `CA`, `CN`, `FR`, `DE`, `IT`, `JP`, `UK`, `ES`, `US`, `IN`, `BR`, `MX`, `AU`, `TR`, `AE`, `SG`, `SE`, and `NL`.
- `associate_id` - (string) the Amazon Associates ID for the affiliate.
- `count` - (integer) determines how many items should be displayed.
- `image_size` - (integer) the image size in pixels.
- `title_length` - (integer) the maximum title character length. Set `-1` for no limit. Default: `-1`.
- `description_length` - (integer) the maximum description character length. Set `-1` for no limit. Default: `250`.
- `link_style` - (integer) the link style. Accepted values are `1`, `2`, `3`, `4`, and `5`. Default: `1`.
    - `1` - http://www.amazon.[domain-suffix]/[product-name]/dp/[asin]/ref=[...]?tag=[associate-id]
    - `2` - http://www.amazon.[domain-suffix]/exec/obidos/ASIN/[asin]/[associate-id]/ref=[...]
    - `3` - http://www.amazon.[domain-suffix]/gp/product/[asin]/?tag=[associate-id]&ref=[...]
    - `4` - http://www.amazon.[domain-suffix]/dp/ASIN/[asin]/ref=[...]?tag=[associate-id]
    - `5` - http://localhost/wp47?productlink=[asin]&locale=[...]&tag=[associate-id]
- `credit_link` - (integer|boolean) whether to show the credit link. `1`/`true` to show, `0`/`false` to hide.
- `subimage_size` - (integer) the sub-image size in pixels. Default: `100`.
- `subimage_max_count` - (integer) the maximum number of sub-images to display.
- `customer_review_max_count` - (integer) the maximum number of customer reviews.
- `show_now_retrieving_message` - (boolean|integer) whether to show the "Now retrieving..." message when sub-elements are pending to be fetched. `true`/`1` to show `false`/`0` to hide.
- `button_type` - (integer) The type of buttons. The following values are accepted. Default: `1`.
    - `0` - Link to the product page.
    - `1` - Add to cart.
- `load_with_javascript` - [3.6.0+] (boolean|integer) whether to load the unit with JavaScript. `true`/`1` to yes, `false`/`0` to no.
- `product_title` - [4.0.0+] (string) An alternative text to alter the product title for anchor text. This is only supported when the `asin` argument is set.
- `show_errors` - [4.1.0+] (integer) Whether to show the output error. Default: `2`.
    - `0`: do not show error.
    - `1`: show the error.
    - `2`: show the error in an HTML comment.

These values can be pre-defined from the setting page via **Dashboard** -> **Auto Amazon Links** -> **Settings** -> **Default**.
If these arguments are omitted, the values set on the setting page will be used.

= Shortcode to Display Buttons =
Although the `%button%` tag in the `Item Format` unit option allows you to insert a button, the button can be displayed independently with the shortcode, `[aal_button ...]`.

It accepts the following parameters.

- `asin` - (required, string) Comma delimited ASINs.
- `type` - (optional, integer) 0: Link to the product page, 1: Add to cart button. Default: `1`.
- `id` - (optional, integer) The button ID. To use the button created via Dashboard -> Auto Amazon Links -> Manage Buttons, specify the button ID.
- `quantity` - (optional, integer) The quantity of the item to add to cart. When multiple ASINs are specified, separate the values by commas.
- `country` - (optional, string) The locale of the marketplace. If not set, the default value set in the Default setting tab will be applied.
- `associate_id` - (optional, string) The associate tag. If not set, the default value set in the Default setting tab will be applied.
- `access_key` - (optional, string) The public PA-API key. If not set, the default value set in the Associates tab will be applied.
- `label` - (optional, string) The button label. e.g. 'Buy Now'. Default: `Buy Now`.
- `offer_listing_id` - (optional, scalar) An offer listing id that Amazon gives.

= Creating Your Own Template =

Download the zip file from the [example templates](https://github.com/michaeluno/amazon-auto-links-example-templates) repository. Make sure it runs as a plugin and a few example templates are loaded in the template listing screen (Dashboard -> Auto Amazon Links -> Templates).

Follow the steps described in readme.md of the linked repository and start modifying them. You want to:
 - rename the root directory, `amazon-auto-links-example-templates`
 - rename the template directory names, `lightslider` and `minimal`
 - rename the main plugin file name, `amazon-auto-links-example-templates.php`
 - rename PHP namespaces, `AutoAmazonLinks\Templates\Examples`
 - replace `screenshot.jpg` in the template directory
 - replace the header comment of `style.css` in the template directory
 - modify CSS rules of `style.css`

= Obtaining PA-API Access Key and Secret Key =

To display more detailed product information, PA-API is required. A pair of access and secret key is required to perform API requests.

To get the keys,

1. login to [Amazon Associates](https://affiliate-program.amazon.com/) of your locale.
1. From the navigation menu at the top, navigate to Tools -> Product Advertising API.
1. If you haven't used it before, press the Join button. An access and a secret key should be issued and displayed after that.

You can check if your keys are valid with [Scratchpad](https://webservices.amazon.com/paapi5/scratchpad/).

== Screenshots ==

1. **Embedding Links below Post**
2. **Widget Sample**
3. **Setting Page** (Selecting Categories)
4. **Setting Page** (Creating New Category Unit)
5. **Setting Page** (Selecting Templates)
6. **Setting Page** (Buttons)
7. **Setting Page** (Editing Buttons)

== Changelog ==

#### 5.2.9 - 04/14/2022
- Fixed a bug that Classic button setting fields were not displayed after saving the button settings.
- Fixed a bug with the Classic button editing UI that the preview labels were not updated dynamically.
- Fixed a bug that custom plugin temporary directory paths introduced in v5.2.8 were not applied to PA-API request counter log.

#### Old Log
For old change logs, see [here](https://github.com/michaeluno/amazon-auto-links/blob/master/changelog.md).