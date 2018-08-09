=== Amazon Auto Links - Amazon Associates Affiliate Plugin ===
Contributors:       Michael Uno, miunosoft
Donate link:        http://en.michaeluno.jp/donate
Tags:               amazon, amazon associates, amazon affiliate, amazon ads, automation, ads, advertisement, affiliate, marketing, monetization, revenue
Requires at least:  3.4
Requires PHP:       5.2.4
Tested up to:       4.9.8
Stable tag:         3.6.6
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html

Generates links of Amazon products just coming out today. Pick categories and they appear even in JavaScript disabled browsers.

== Description ==

= Display Amazon Associates Links with Minimal Effort = 

Still manually searching products and pasting affiliate links? What happens if the products get outdated? With this plugin, you do not have to worry about it nor trouble to do such repetitive tasks. Just pick categories which suit your site and it will automatically display the links of decent products just coming out from Amazon today.

The links are tagged with your Amazon Associate ID. The plugin supports 10 Amazon locales and works even on JavaScript disabled browsers. Insert the ads as widget or place generated shortcode or PHP code where the links should appear.

If you want to search a specific product, yes, you can do that too. If you are good at HTML and CSS coding and know a little about PHP, you can create your own template! That means you can design the layout.

Display affiliate links along with your posts with this plugin to generate actual income with minimal efforts.

= See How Amazon Affiliate Links are Displayed =

[youtube https://www.youtube.com/watch?v=mpDCcp4KBZg]

= Supports All Amazon Associates Locales =
Includes Germany, Japan, Italy, Spain, UK, US, Canada, France, Australia, India, China, Mexico and Brazil.

= Works without JavaScript =
Some visitors turn off JavaScript for security and most ads including Google Adsense will not show up to them. But this one works!

= Automatic Insertion in Posts and Feeds = 
Just check where you want the product links to appear with auto-insert. 

- **Static Contents Conversion** - If you want the product link to be static, it is possible. This means if you deactivate the plugin, the converted contents will remain.
- **Detailed Visibility Criteria** - You can enable/disable product links on the pages you want or do not want by post ID, taxonomy, page type, and post type. 

= Widgets = 
Place the widget in the sidebar and select the unit you created. The product links will appear in where you want. 

- **By Units** - choose the created units to display in the widget.
- **Contextual Search** - with this, you don't have to create a unit. It will automatically searches products relating to the currently displayed page contents.

= Shortcode and PHP Function = 
Insert the ads in specific posts and pages with the shortcode. If you want to insert in the theme template, use the PHP code the plugin provides to produce the outputs.

= Filter Unwanted Products = 
If there are some items you don't want to display, you can create a black and white list by description, title, and ASIN.

= Custom Buttons =
Visitors more likely click buttons than regular text hyper links. Define your custom buttons and insert it to the unit output. 

= Export Ads into External Sites with Feed = 
By subscribing to the product feed produced with the units you create as RSS or JSON, you can import them from other sites. 

If you have a web site that can display RSS feed contents, just create a WordPress site somewhere with this plugin and fetch the feed from the site. If you are an App developer, you can just display the items from the feed without programming an API client.

= Various Unit Options = 

- **Image Size** - The size of thumbnails can be specified. It supports up to 500 pixel large with a clean resolution.
- **Sort Order** - Shuffle the product links so that the visitor won't get bored as it gives refreshed impression.
- **URL cloaking** - You can obfuscate the link urls so it helps to prevent being blocked by browser Ad-blocking add-ons. 
- and more.

= Customize Outputs =
Besides the **Item Format** unit option which lets you design the output of a unit, you can create a custom template. This gives you freedom of customization and lets you achieve more advanced and detailed design.

= Unit Types =
- **Category** - pick your category that matches your site topic.
- **Product Search** - create a unit of a search result.
- **Item Look-up** - display specific products.
- **Similarity Look-up** - display similar products.
- **URL** - list items from an external web source.

= Getting Started =    
To get started, create a unit first and display it with widgets, shortcode, or auto-insert.

= Supported Language =
- English
- Japanese  
  
== Installation ==

= Install =
1. Upload **`amazon-auto-links.php`** and other files compressed in the zip folder to the **`/wp-content/plugins/`** directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

= Getting Started = 
1. Go to **Dashboard** -> **Amazon Auto Links** -> **Add Unit by Category**.
1. Configure the options and select categories.
1. After saving the unit option, go to **'Manage Units'** to get the shortcode or if you check one of the insert option, the links will automatically appear in posts or feeds depending on your choice. The widget is available in the **Appearance** -> **Widgets** page as well.

== Frequently asked questions ==

= Do I need Amazon Associate ID to use this plug-in? =

Yes. Otherwise, you don't get any revenue. You can get it by signing up for [Amazon Associates](https://affiliate-program.amazon.com/).  

= Do I need Amazon Access Keys? = 

For the *Category* and *Tag* unit types, no, you don't need them. However, for the *search* unit type, you need them as the plugin uses Amazon API.

However, for the *Search* unit type, and for some items such as `%image_set%` in the *Item Format* option, setting up API keys is required. 

To use the keys you need to have an account with [Amazon Product Advertising API](https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html). The keys can be obtained by logging in to [Amazon Web Services](http://aws.amazon.com/) and you need to get **Access Key ID** (public key) and **Secret Access Key** (private key).

More detailed instruction, refer to the [**How to Obtain Access Key and Secret Key**](http://wordpress.org/plugins/amazon-auto-links/other_notes/) section.

= What does Unit mean? =

A unit is a set of rules that defines how Amazon products should be displayed.

When you display Amazon products, you would specify a unit and the plugin will generate outputs based on the rules defined for the unit.

= What would be the benefit to upgrade to the pro version? =

With the pro version, unlimited numbers of units can be created. Also the number of categories per unit, the number of items to display per unit are unrestriceted as well. Please consider upgrading it. [Amazon Auto Links Pro](http://en.michaeluno.jp/amazon-auto-links/amazon-auto-links-pro) As of Pro 2.0.6, links can be displayed in multiple columns.

= I get a blank white page after adding a unit to the theme. What is it? What should I do? =

It could be the allocated memory capacity for PHP reached the limit. One way to increase it is to add the following code in your wp-config.php or functions.php
`define( 'WP_MEMORY_LIMIT', '128M' );`
The part, 128M, should be changed accordingly.

= I want to display product links horizontally in multiple columns. Is it possible? = 

Yes, with [Pro](http://en.michaeluno.jp/amazon-auto-links/amazon-auto-links-pro)! 

= I have a feature request. Would you implement that? = 
Post it in the [support section](http://wordpress.org/support/plugin/amazon-auto-links). If it is feasible, it will be included in the to-do list in the Other Notes section.

= I get Amazon product links everywhere on the site after creating some units. How can I restrict them to certain pages? =
Go to `Dashboard` -> `Amazon Auto Links` -> `Manage Auto-insert`. There turn off unnecessary auto-insert items. You can edit their definitions and define where units should be displayed.

= My credentials do not seem to be authenticated. How can I check if my access keys are the correct ones? = 
Try [Scratchpad](http://webservices.amazon.com/scratchpad/) to make sure your keys work there as well.


== Other Notes ==

= Shortcode and Function Parameters =
The following parameters can be used for the shortcode, `[amazon_auto_links]` or the PHP function of the plugin, `AmazonAutoLinks()`

<h5><strong>id</strong> - the unit ID</h5>

`
[amazon_auto_links id="123"]
`

`
<?php AmazonAutoLinks( array( 'id' => 123 ) ); ?>
`

<h5><strong>label</strong> - the label associated with the units</h5>

`
[amazon_auto_links label="WordPress"]
`

`
<?php AmazonAutoLinks( array( 'label' => 'WordPress' ) ); ?>
`

<h5><strong>asin</strong> - ASINs (product IDs) separated by commas (`,`).</h5>

`
[amazon_auto_links asin="B016ZNRC0Q, B00ZV9PXP2"]
`

`
<?php AmazonAutoLinks( array( 'asin' => 'B016ZNRC0Q, B00ZV9PXP2' ) ); ?>
`

<h5><strong>search</strong> - Search keywords separated by commas (`,`).</h5>

`
[amazon_auto_links search="WordPress"]
`

`
<?php AmazonAutoLinks( array( 'search' => 'WordPress' ) ); ?>
`

The `id`, `asin` and `search` arguments cannot be used together.

Optionally, the following arguments may be set.

- `country` - (string) the locale of the store. Accepted values are `CA`, `CN`, `FR`, `DE`, `IT`, `JP`, `UK`, `ES`, `US`, `IN`, `BR`, and `MX`.
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
- `similar_product_image_size` - (integer) the image size of similar products in pixels. Default: `100`.
- `similar_product_max_count` - (integer) the maximum number of similar products.
- `customer_review_max_count` - (integer) the maximum number of customer reviews.
- `show_now_retrieving_message` - (boolean|integer) whether to show the "Now retrieving..." message when sub-elements are pending to be fetched. `true`/`1` to show `false`/`0` to hide.
- `button_type` - (integer) The type of buttons. The following values are accepted. Default: `1`.
    - `0` - Link to the product page.
    - `1` - Add to cart.
- `load_with_javascript` - [3.6.0+] (boolean|integer) whether to load the unit with JavaScript. `true`/`1` to yes, `false`/`0` to no.

These values can be pre-defined from the setting page via `Dashboard` -> `Amazon Auto Links` -> `Settings` -> `Default`.
If these arguments are omitted, the values set in the setting page will be used.

= How to Create Own Template =

<h5><strong>Step 1</strong></h5>

Copy an existing template that is located in `...wp-content/plugins/amazon-auto-links/template` and rename the copied folder.

<h5><strong>Step 2</strong></h5>

Remove the files besides `style.css` and `template.php` as other files are optional.

<h5><strong>Step 3</strong></h5>
 
Edit `style.css` and `template.php` to customize the layout.

<h5><strong>Step 4</strong></h5>

Create a folder named `amazon-auto-links` in your theme's folder. If you are using Twenty Thirteen, the location would be `...wp-content\themes\twentythirteen\amazon-auto-links`.

<h5><strong>Step 5</strong></h5>

Move the working folder(the copied one) to it (the `amazon-auto-links` folder you just created).

<h5><strong>Step 6</strong></h5>

The plugin will automatically detect your template and add it in the template listing table. So activate it.

= Upgrading V1 to V2 = 
When upgrading v1 to v2, a few options will be lost. That includes:

* Sidebar widget 
* The positions of the inserting area

These options need to be reconfigured.

= How to Obtain Access Key and Secret Key =
For the search unit type, an access key and its secret key are required to perform API requests. 

We need to deal with additional two separate Amazon services apart from the Amazon Associates.

<h5><strong>Step 1</strong> - Create a Product Advertising API Account</h5>
Before you create an access key, you have to make sure you have singed up with [Amazon Product Advertising API](https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html).

<h5><strong>Step 2</strong> - Sign up with Amazon Web Service</h5>
Next, create an account with Amazon Web Service(http://aws.amazon.com/). At the top right pull-down menu, you can navigate to `My Account/Console` -> `Security Credential`.

<h5><strong>Step 3</strong> - Create an Access Key</h5>
In the [Security Credentials](https://console.aws.amazon.com/iam/home?#security_credential) page, find the section named `Access Keys (Access Key ID and Secret Access Key)` and click on that.

Then press the `Create New Access Key` button to create a key. Don't forget to keep the secret access key as well. Amazon has changed the policy not to let the user to obtain the secret key later on.

Also note that at the point that an access key is issued, if you have not created an account with Product Advertising API, the key will be invalid. If that happens, delete the created access key and go back to the previous step.

You can check if your access key is valid or not with [Scratchpad](http://webservices.amazon.com/scratchpad/).

== Screenshots ==

1. **Setting Page** (Creating New Unit)
2. **Setting Page** (Selecting Categories)
3. **Embedding Links below Post**
4. **Widget Sample**

== Changelog ==

= 3.6.6 - 08/09/2018 =
- Added the `Data` section in the `Reset` setting page, which handles export/import options.
- Added the ability to clean up used custom post type posts upon plugin uninstall.

= 3.6.5 - 08/04/2018 =
- Added the translation items for the Japanese and default language file.

= 3.6.4 - 07/29/2018 =
- Added the `aal_filter_product_link` filter hook to allow third-parties to modify product links.

= 3.6.3 - 07/17/2018 =
- Fixed a compatibility issue with third party plugins/themes which attempt to instantiate the plugin widgets.

= 3.6.2 - 07/09/2018 =
- Fixed a bug that the default button is not created.

= 3.6.1 - 07/04/2018 =
- Added `rel='nofollow'` to the button links.

= 3.6.0 - 06/22/2018 =
- Added the `Load with Javascript` unit option that lets the user decide whether to display the unit with JavaScript.

= 3.5.7 - 06/09/2018 =
- Fixed an issue that some categories could not be recognized when creating a category unit.

= 3.5.6 - 06/05/2018 =
- Added SSL support for impression counter scripts.
- Added the API response error message to be displayed for API authentication in the setting page.

= 3.5.5 - 05/11/2018 =
- Fixed a bug that incorrect categories were displayed when creating a new product search and item search unit.
- Added support for the Australia locale.
- Added support for the locales of Mexico and Brazil for the search units.

= 3.5.4 - 05/02/2018 =
- Fixed an issue that category units could not detect some sub-categories when creating a unit.
- Added custom filter hooks in the feed template to allow third-parties to modify the RSS feed outputs.

= 3.5.3 - 09/10/2017 =
- Changed the default value of the Credit Link unit option.

= 3.5.2 - 06/08/2017 =
- Fixed an issue that the search unit types missed product thumbnails in rare cases.

= 3.5.1 - 05/03/2017 =
- Fixed an issue that the random sort order for URL and Item Look-up units was applied after the product data were retrieved which caused the same items to constantly appear.

= 3.5.0 - 01/23/2017 =
- Added the `Renew Cache` action link in the unit listing table.
- Added the `search` shortcode and function argument which performs a keyword search with the set keywords.
- Added the `asin` shortcode and function argument which list products of the set ASINs.
- Added the `Contextual` unit type.
- Added the `Sort Order` option to the `Item Look-up` unit type.
- Refined the API request caching mechanism.
- (breaking change) Fixed typos in option key names. This fix affects the stored option values of `Interval for Removing Expired Caches` and `Caching Mode`. Some users may need to re-save the options.
- Fixed a bug that produced invalid RSS2 and JSON formats.

= 3.4.13 - 01/05/2017 =
- Fixed a bug that an incorrect offered price was displayed with the `%price%` variable of the `Item Format` option.
- Tweaked the accuracy of detecting products with URL units.

= 3.4.12 - 12/24/2016 =
- Fixed a bug that invalid user inputs for the `Item ID` option were saved with the `Item Look-up` unit.

= 3.4.11 - 12/16/2016 =
- Fixed a warning `wp_kses_js_entities is deprecated since version 4.7.0`.
- Fixed an issue that static auto-insert was not performed when a draft is saved.
- Changed the `%price%` variable for the `Item Format` unit option to show the lowest offered price from just a discounted price when available.

= 3.4.10 - 11/28/2016 =
- Fixed a bug with static auto-insert.

= 3.4.9 - 11/27/2016 =
- Fixed an issue that some API requests were not cached properly.

= 3.4.8 - 11/25/2016 =
- Added cache size indications in the `Cache` setting section.
- Fixed a bug that unexpired caches were deleted when deleting expired caches.
- Fixed an issue that some ASINs were not detected accurately in URL units.
- Fixed a bug that PHP warnings occurred in the background in some rare occasion.

= 3.4.7 - 11/06/2016 =
- Fixed a bug that the Contextual Products widgets were no longer displaying any products, introduced in 3.4.6.
- Fixed a bug occurred in PHP 5.3 that caused a warning `debug_backtrace() expects at most 1 parameter, 2 given`.

= 3.4.6 - 11/02/2016 =
- Fixed a bug with the shortcode that some direct product search arguments were not recognized.

= 3.4.5 - 10/27/2016 =
- Fixed an issue that reaches PHP max input vars in the auto-insert definition page on some servers.

= 3.4.4 - 09/16/2016 =
- Fixed an issue that some locale specific API keys were not connected to the API server by adding the `Server Locale` option in the `Authentication` section.

= 3.4.3 - 09/02/2016 =
- Fixed a bug of discount prices displayed with the `%price%` variable for the `Item Format` unit option.

= 3.4.2 - 06/09/2016 =
- Fixed a bug that products of Category units were not displayed on some servers introduced in v3.4.1.

= 3.4.1 - 05/31/2016 =
- Fixed a bug that the `%rating%` variable in the `Item Format` option produced HTML outputs with an invalid structure.

= 3.4.0 - 03/17/2016 =
- Added the ability to automatically extract ASINs for the Item Look-up and Similarity search unit types.
- Added the ability to set a custom label for the unit preview page.
- Added the `Interval for Removing Expired Caches` option.
- Added the default unit options.
- Changed the `%price%` variable in the `Item Format` option to display a discounted price when available.
- Fixed a bug that `ISBN` could not be set with the `Item Look-up` unit type even the locale was set to `US`.
- Fixed a bug that expired caches were not cleared automatically.
- Fixed a bug that the custom data base tables did not have the proper character set and collation. 

= 3.3.6 - 01/14/2016 =
- Fixed PHP warnings caused by using a deprecated method.

= 3.3.5 - 01/13/2016 =
- Tweaked the style of an Auto-insert option. 
- Fixed a bug that caused a warning when setting the Post Type Slug option ( `Settings` -> `General` -> `Unit Preview` -> `Post Type Slug`).
- Optimized performance in the admin area.

= 3.3.4 - 01/02/2016 = 
- Fixed a bug that a fatal error occurred with category units on sites enabling SSL.
- Improved performance of the setting pages.

= 3.3.3 - 12/31/2015 =
- Fixed a bug that caused illegal string offset warnings with stored template data.
 
= 3.3.2 - 12/29/2015 =
- Fixed an issue that the setting forms could not be displayed when third-party plugins or themes have JavaScript errors in the same page.
 
= 3.3.1 - 12/25/2015 = 
- Fixed invalid offset warnings in PHP 7. 
- Fixed a bug that an invalid character was inserted in the RSS feed.
 
= 3.3.0 - 12/23/2015 =
- Improved the performance of the auto-insert functionality. 
- Added the `Description Suffix` unit option that let the uset set own text for the `read more` label.
- Added the `Max Image Size for Similar Product Thumbnails` and `Max number of Similar Products` unit options.
- Added the `%meta%` variable to the Item Format unit option.
- Added the `%similar%` variable to the Item Format unit option.
- Added the Unit Options Converter in the Tools page.
- Added the `%content%` variable for the full product description to the Item Format unit option.
- Added the RSS Content Tag option that lets the user decide whether a complete product output should be in the `<description>` tag or the `<content>` tag in the feed.
- Added the Allowed HTML Tag Attributes option and the Allowed Inline CSS Properties option in the Misc setting page.
- Fixed a bug that deactivated templates were loaded as active.
- Fixed some incompatibility issue with PHP 7.
- Fixed a bug that the unit options could not load when there is no button created.
- Fixed a bug with the contextual product widget which caused Item Format option to be empty when the user first add the widget without connecting to the API.
- Fixed a bug with the contextual product widget which caused a fatal error when the user enabled `Breadcrumb` in the `Additional Criteria` option and enabled `The home page` in the `Available` Page Type` option.
- Fixed a bug that custom database table versions were not saved properly and caused extra database queries.
- Reduced the number of database queries in widget forms.
- Changed the Item Format option to be available. 
- Changed the `%description%` variable of the `Category` unit type to include description produced by Amazon Product Advertising API if the user has authenticated the plugin.
- Deprecated the Template Options Converter.

= 3.2.4 - 12/13/2015 =
- Fixed an issue that unit preview could not be displayed without re-saving the site permalink options after setting a custom unit preview post type slug.
- Fixed a bug that a `View` action link was inserted in different post type listing table when a custom unit preview post type slug was set.
- Fixed a bug in the contextual products widget that the Credit Link option was not displayed properly.
- Fixed an issue of a fatal error `Maximum function nesting level of 'x' reached` when the server enables the XDebug extension and sets a low value for the `xdebug.max_nesting_level` option. 
- Tweaked the appearance of the auto-insert setting page.
 
= 3.2.3 - 12/11/2015 =
- Fixed a compatibility issue with WordPress 4.4 that some widget options could not be saved.

= 3.2.2 - 12/09/2015 =
- Added a unit option to select credit link type.
- Fixed a bug that reselecting categories via the Select Categories button in the Category unit editing page let to a fatal error, introduced in v3.2.0.
- Changed the minimum required cache duration to `600`.
- Changed the Found Items field in the URL unit definition page to display Not Found message for finding no item.

= 3.2.1 - 12/04/2015 =
- Fixed a bug with the `Item Look-up` and `URL` unit types that the `Number of Items` option did not take effect.
- Fixed a bug that some Amazon Product Advertising API response errors could not be displayed when the `Query per Item` option was enabled.
- Fixed incorrect inline CSS values in the default Image Format unit option.
- Changed the default template of the Contextual Products Widget to `Search`.
- Tweaked the style of `Search` and `Category` templates for disclaimer elements in widgets.
- Tweaked the style of `Search` template to wrap descriptions.
- Removed some advanced options of the URL unit type as their values could not be used rather led to errors.

= 3.2.0 - 12/02/2015 = 
- Enhanced the ability of the contextual product search for the site search terms.
- Added the `URL` unit type which enables to search products and list them with given urls.
- Added the `Query per Term` unit option for the `Search`, `Item Look-up`, `Similarity Look-up` unit types.
- Added the `%disclaimer%` variable in the `Item Format` unit option.
- Added a unit option to toggle the visibility of `Now Retrieving...` message.
- Added the internal ability to set previous unit option values when creating a new unit.
- Changed the some option inputs larger including product filter options and search items of the Item look-up and Similarity look-up unit type s.
- Changed the initial position of the `Template` unit option section.
- Tweaked the style of setting pages and forms. 
- Deprecated the `tag` unit type as it is no longer supported by [Amazon](https://www.amazon.com/gp/help/customer/display.html?nodeId=16238571).

= 3.1.4 - 11/27/2015 =
- Added the `search_per_keyword` argument for the shortcode which can be set to `true` when performing search with multiple keywords.
- Enhanced the ability of the contextual product widget.

= 3.1.3 - 11/26/2015 =
- Changed the unit template formatting options to accept some inline CSS properties.
- Tweaked the style of rating images of the `Category` and `Search` templates.
- Tweaked the style of sub-images in widgets of the `Category` and `Search` templates.
- Tweaked the style of some option fields in the plugin setting pages.
- Fixed a bug that some options with numbers could not set more than `1`.

= 3.1.2 - 11/25/2015 =
- Changed the default value of the page type option in the Contextual Search widget.
- Tweaked the style of thumbnails of the Category and Search templates.

= 3.1.1 - 08/09/2015 =
- Fixed a fatal error `Call to undefined function mb_detect_encoding()...` in the category select page on the server that does not install the multibite string extension.
- Fixed a bug that the `%price%` variable in the `Item Format` unit option was not functional.

= 3.1.0 - 07/27/2015 =
- Added the ability to skip no thumbnail items.
- Added the `Button Type` unit option that lets the user add a product to the Amazon shopping cart.
- Added the ability to produce RSS and JSON feeds by unit id.
- Added the home and front page criteria for the `Available Page Types` option in the widget form.
- Tweaked the style of built-in templates.
- Tweaked the style of the credit link.
- Fixed PHP warnings related file path lengths set to the `PHP_MAXPATHLEN` constant. 
- Fixed an issue that widget by unit could not be displayed in the front/home page, introduced in 3.0.5.

= 3.0.5 - 07/14/2015 =
- Added visibility options to the widget by unit.
- Fixed a credit link that pointed the plugin directory which occurs when the user does not update the options to v3.

= 3.0.4 - 07/07/2015 =
- Changed debug methods not to function when the site debug mode is off.
- Changed not to redirect the user to the listing table page after editing an auto-insert definition.
- Fixed strict standard PHP warnings.

= 3.0.3 - 07/05/2015 =
- Fixed a bug in the contextual product widget that product filter options did not take effect.
- Fixed a bug that setting `0` for the `Max Image Size for Sub-images` option did not disable the images.
- Fixed a bug that templates inherited from v2 options were listed twice in the template listing table.

= 3.0.2 - 07/04/2015 =
- Fixed an issue that templates were not properly loaded if the user did not upgrade the options to v3.
 
= 3.0.1 - 06/30/2015 =
- Tweaked the formatting of a product element.
- Fixed an issue that translation files were not loaded in the front-end.
- Updated the base translation file.

= 3 - 06/29/2015 =
- Added the `Tools` setting page.
- Added the contextual products widget.
- Added the ability to design buttons and insert them in units.
- Added black and white lists per unit.
- Added common advanced unit options.
- Added the `%button%`, `%review%`, `%rating%`, and %image_set% variables to the template option.
- Refined the method of processing RSS feed data by getting rid of SimplePie.
- Refined the caching mechanism by introducing custom tables.
- Refined the setting pages.
    - Changed the behaviour of the white-list to only allow certain products from being blocked, from only showing certain products.
    - Added a unit preview link to a unit name in the Auto-insert listing table.
    - Tweaked the `Manage Auto-insert` admin menu that did not appear in the Auto-insert listing page.
    - Remove the `Template` option section when creating a new unit.
    - Removed the `Add Auto insert` admin menu link.
    - Removed the `Support` section from the settings.
    - Updated Admin Page Framework.
- Updated the Amazon Product Advertising API version.

= 2.2.1 - 04/25/2015 =
- Fixed a compatibility issue with WordPress 4.2 in template listing page.
- Tweaked the styling of plugin template listing pages.

= 2.2.0 - 04/24/2015 =
- Added the `Preview Unit` options including one that allows the user to set a custom url slug to the unit preview page.

= 2.1.2 - 12/15/2014 =
- Added the `aal_action_loaded_plugin` action hook.
- Changed the timing of a localization function call.
- Tweaked the way to display product prices of the search unit type.
- Updated the Japanese translation file.

= 2.1.1 - 12/04/2014 =
- Added the ability to automatically remove auto-insert items with no unit associated when a unit is removed.
- Changed the displayed product price of the `Search` unit type to use the discount price if there is an offered price.
- Optimized the performance.

= 2.1.0 - 11/24/2014 = 
- Added the India locale for the search unit type.
- Fixed an issue that when `Marketpalce` is selected in the `Category` option of the Select unit type, an error was shown.

= 2.0.7 - 11/14/2014 =
- Added a error message when a template is not found.
- Added the `Merchant ID` option in the advanced options for the `Search` unit type.
- Fixed an issue that options were not saved with sites that enables object caching in the admin area.

= 2.0.6 - 09/27/2014 =
- Made a small optimization on the background caching routines.
- Added the `%price%` variable for the search unit type.
- Fixed widget output formatting.

= 2.0.5.4 - 06/07/2014 =
- Tweaked the styling to horizontally center the Not Found image of the `Search` template.
- Fixed the label of one of the advanced options of the search unit type.

= 2.0.5.3 - 05/30/2014 =
- Fixed the `warning: Array to string conversion in...` message when product links were displayed with the auto-insert.

= 2.0.5.2 - 05/27/2014 =
- Tweaked a form validation method to prevent no type option item from getting selected.
- Changed the file structure of including files.
- Tweaked the styling of the `Category` template to center the not found image.

= 2.0.5.1 - 04/24/2014 =
- Changed the default option value of `Prevent Duplicates`.

= 2.0.5 - 03/22/2014 =
* Fixed a bug that templates were deactivated when the plugin was deactivated and reactivated.
* Fixed an issue that the `the_content` filter was ignored in the plugin custom post type page.
* Added the `Caching Mode` option.
* Fixed a compatibility issue with a third-party plugin that hooks the `posts_where` filter.
* Fixed a PHP warning that occurs when a user with an insufficient access level logs in to the admin page.
* Added the class selectors representing unit ID and the label to the products container element.
* Fixed a bug that the `column` option was saved always as 1 for the first time.
* Refactored the code.

= 2.0.4.1 - 03/03/2014 =
* Fixed a bug with the Auto-insert feature that a set page type for the `Where to Enable` section did not take effect.
* Added the ability to use an image when no product is found for the search unit type.
* Fixed a bug with the search unit type that results of the same API request but with a different locale were saved with the same name.
* Fixed a bug with the search unit type that same products were stored in the response array when more than 10 items were set.

= 2.0.4 - 02/27/2014 =
* Improved the caching mechanism.
* Fixed a bug that caches for the search unit type renewed in the background were not saved properly with the right name.
* Fixed a bug that caches durations for the search unit type were not set properly.

= 2.0.3.5 - 02/01/2014 =
* Fixed a possible security issue in the plugin admin pages.
* Tweaked the form text input length.
* Fixed an issue that `(recently, newly) tagged...` message was inserted in the title of product links of Tag unit type.
* Fixed the warning: `Strict standards: Declaration of ... should be compatible with ...` when displaying the output of the tag unit type.

= 2.0.3.4 - 01/25/2014 =
* Fixed: a bug that the Settings link of the plugin in the plugin listing table did not point to the Setting page.
* Fixed: a bug that caused a broken output in the search unit type due to an undefined index in an array when the authentication keys are not properly set in some PHP versions.
* Added: the `Number of Items` option for the Similarity Look-up search unit type.
* Fixed the warning: `Strict standards: Declaration of ... should be compatible with ...`.
* Updated: the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.7.2.

= 2.0.3.3 - 01/17/2014 =
* Fixed: a bug that the `Single Post` check option did not take effect in the Page post type.
* Updated: the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.7.1.
* Fixed: a bug that taxonomy check list boxes were gone since v2.0.2.

= 2.0.3.2 - 01/07/2014 = 
* Fixed: an issue that an image element was inserted when no image of the product was found in the search unit type.
* Fixed: undefined index warnings with the search unit types.
* Fixed: a bug in the advanced search option that the Title option did not take effect.

= 2.0.3.1 - 01/03/2014 =
* Fixed: an issue that the stylesheet urls included the characters, `/./`, which may have caused a problem for third-party plugins that minifies CSS.
* Fixed: an issue that some outputs broke html tags due to unescaped characters.
* Fixed: a bug that search units did not return results with the correct item count.

= 2.0.3 - 12/20/2013 = 
* Added: the `Similarity Look-up` unit type which allows to display similar products by ASIN.
* Changed: the message "Could not identify the unit type." to be inserted as an HTML comment.

= 2.0.2 - 12/17/2013 = 
* Fixed: an issue that too many database queries were performed in the plugin setting pages as of v2.0.1 due to disabling object caching.
* Added: the `Item Look-up` unit type which allows to pick one or more items by item ID.
* Fixed: the method handling Amazon Product Advertising API to treat invalid XML returned by the API as an error.
* Updated: the information regarding obtaining an Amazon access key since the linked documentation page has been closed.
* Added: a help page and some information pages in the plugin admin pages.
* Tweaked: the style of the option elements in the unit definition page.
* Updated: the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.6.

= 2.0.1 - 11/30/2013 =
* Improved: the method to load template stylesheets.
* Disabled: object caching in the plugin pages and the options.php (the page that stores the settings) in order to avoid conflicts with caching plugins.
* Updated: the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.4.
* Added: the `aal_filter_unit_output` filter.
* Fixed: a bug in form filed layout that an enclosing tag was missing.
* Fixed: the warning, Creating default object from empty value.

= 2 - 11/01/2013 =
* Changed: to ask user permission to display ads in the settings page and the support rate when the plugin is activated for the first time.
* Changed: the unit label option to a taxonomy.
* Changed: the url cloak to use less characters (moved to the link style option).
* Added: the ability to limit auto-static-insertion by taxonomy and post type.
* Added: the ability to limit auto-insertion by taxonomy, post type, and post ID.
* Added: the ability to prevent duplicated products from being displayed throughout the page load.
* Added: the ability to create units by tag.
* Added: the Brazil and Mexico locales.
* Added: the case sensitive option for the black and white list options.
* Added: the white list option.
* Added: the auto-insert feature. Accordingly, the Where to Insert option and Where to Disable option were deprecated.
* Added: the tag unit type.
* Added: the search unit type.
* Added: the template system. Accordingly, the Container, Item, Image format options were deprecated.
* Renewed: (***Breaking Changes***)the entire option structure. Accordingly, after running the option importer script, which is displayed as a link in the admin message, the insert position options need to be reconfigured.
* Renewed: the background-caching system.
* Renewed: the icon.
* Renewed: the admin interface. 

= 1.2.6 - 09/01/2013 =
* Added: the ability to use SSL images if the site runs on SSL.
* Added: the Indian locale.

= 1.2.5.2 - 03/08/2013 =
* Added: an error message for servers which does not have the DOM XML extension which appears upon plugin activation.
* Disabled: completely DOM related errors.
* Tweaked: some code for faster loading in the admin settings pages.
* Changed: the option object to be global for plugin extensions.
* Added: filters for plugin extensions.

= 1.2.5.1 - 02/23/2013 =
* Fixed: the warning, Undefined variable: oAAL.

= 1.2.5 - 02/23/2013 =
* Added: the version number to appear in the footer of the plugin setting pages.
* Added: the ability to remove all used option values of the plugin upon plugin deactivation, which can be set in General Settings.
* Fixed: the warning message, undefined index, save, which occurred in the debug mode when posting a new post.

= 1.2.4 - 02/22/2013 =
* Added: the "Access Right to Setting Page" option in the General Settings page that sets the access level for the admin page of the plugin.

= 1.2.3 - 02/21/2013 =
* Fixed: a bug that title sorting was not accurately performed when the title contained HTML tags or encoded differently from other titles.
* Changed: the name of the sort option, Title, to Title Ascending.
* Added: the Title Descending sort order option.

= 1.2.2 - 02/21/2013 =
* Added: the Debug Log option and page. 
* Changed: the default value of the Prefetch Category Lists option to Off.
* Fixed: the Japanese localization file name.
* Disabled: the warining message to occur:  Warning: DOMElement::setAttribute() [domelement.setattribute]: string is not in UTF-8.

= 1.2.1 - 02/18/2013 = 
* Fixed: a bug that changing unit option values did not take effect when the Proceed button was pressed via the Manage Unit page.
* Changed: product links to be disabled on the front page ( not only on the home page ) if the "Disable on the home page" option is enabled.
* Fixed: warning messages, undefined index, which appeared in the debug mode when the Delete Selected Units button was pressed.

= 1.2.0 - 02/12/2013 =
* Fixed: some lines of code which caused warning messages when the WordPress debug mode was turned on.
* Added: the ability to clean up remaining once-off events upon plugin deactivation.
* Added: the option to disable product links per unit basis on certain pages including the home page.
* Fixed: a bug that url cloak had not been working as of 1.1.9.

= 1.1.9 - 01/24/2013 =
* Updated: the Japanese localization file. 
* Added: the Above and Below Post on Publish check boxes for the auto insert option. 
* Fixed: the styles of the Manage Unit table by loading the stylesheet in all the setting tab pages of the plugin.
* Changed: the file name to amazon-auto-links.php from amazonautolinks.php
* Changed: the text domain to amazon-auto-links from amazonautolinks.
* Changed: the timimng of registering classes to be extension-friendly.
* Changed: code formatting to extend the plugin more easily.

= 1.1.8 - 01/19/2013 = 
* Added: the icons for the Operation column of the Manage Unit table .
* Adjusted: the styles of the Manage Unit page in the admin page.
* Fixed: an issue that R18 categories requiring additional redirects could not be browsed in the category selection page.

= 1.1.7 - 01/16/2013 =
* Fixed: a bug that caches were not cleared with database tables that have a custom prefix.
* Fixed: a bug that the Prefetch Category Lists option had not take effect since v1.1.3 removing the iframe preview page.

= 1.1.6 - 01/14/2013 =
* Fixed: a minor bug that an error message did not appear properly when category links cannot be retrieved.
* Added: Blacklist by title and description set in the General Settings page.

= 1.1.5 - 12/14/2012 =
* Changed: to force the unit output to close any unclosed HTML tags.
* Fixed: a bug that the plugin requirement check did not work as of v1.1.3.
* Improved: the response speed when first accessing the setting page.

= 1.1.4 - 12/13/2012 =
* Fixed: a bug that shortcode did not work as of v1.1.3.

= 1.1.3 - 12/13/2012 =
* Supported: WordPress 3.5
* Changed: the preview page not to use iframe so that "Could not locate admin.php" error would not occur.
* Fixed: a bug that the style was not loaded in one of the tab page in the plugin setting page.
* Fixed: a bug that the arrow images which indicate where to click did not appear in the category selection page.
* Added: the ability to delete transients for category caches when the pre-fetch option is set to off.
* Added: the unit memory usage in the unit preview page.
* Added: the ability to remove transients when the plug-in is deactivated. 

= 1.1.2 - 11/11/2012 =
* Fixed: a bug which displayed the plugin memory usage in the page footer.

= 1.1.1 - 11/02/2012 = 
* Added: the prefetch category links option, which helps in some servers which sets a low value to the max simultaneous database connections.

= 1.1.0 - 10/26/2012 =
* Fixed: a bug that url cloak option was forced to be unchecked in the option page.
* Fixed: a bug that credit option was forced to be checked in the option page.
* Fixed: an issue that encryption did not work on servers which disables the mcrypt extension.
* Fixed: an issue that some form elements of the admin page did not appear on servers with the short_open_tag setting disabled.
* Fixed: a bug that the AmazonAutoLinks() function did not retrieve the correct unit ID. 

= 1.0.9 - 10/06/2012 =
* Added: the link cloaking feature.

= 1.0.8 - 10/03/2012 =
* Fixed: a bug that shortcode failed to display the unit saved in version 1.0.7 or later.
* Added: the title length option.
* Added: the link style option.
* Added: the credit insert option.

= 1.0.7 - 10/02/2012 =
* Fixed: an issue that the widget got un-associated when the unit label got changed.
* Fixed: an issue that category caches were saved with the wrong name which resulted on not using the cache when available.
* Fixed: an issue that the format of the img tag got changed when the validation failed when setting up a unit.
* Added: a donation link in the plugin listing page.

= 1.0.6 - 09/24/2012 =
* Added: the rel attribute, rel="nofollow", in the a tag of product links.
* Re-added: the widget which enables to add units easily on the sidebar.

= 1.0.5 - 09/20/2012 =
* Improved: the caching method. Now the caches of links are renewed in the background.

= 1.0.4 - 09/18/2012 =
* Added: the settings link in the plugin list page of the administration panel.
* Improved: the page load speed in the category selection page by reducing the cache elements.

= 1.0.3 - 09/16/2012 =
* Fixed: an issue that in below PHP v5.2.4, the link descriptions could not be retrieved properly; as a result, the edit and view page links were broken.
* Improved: the page load speed in the category selection page with caches.
* Removed: the widget functionality since it produced a blank page in some systems and the cause and solution could not be found.

= 1.0.2 - 09/12/2012 =
* Fixed: an issue that form buttons did not appear in the category selection page in WordPress version 3.1x or ealier.

= 1.0.1 - 09/10/2012 =
* Added: the Widget option.

= 1.0.0 - 09/10/2012 =
* Initial Release
