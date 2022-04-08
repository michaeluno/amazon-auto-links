#### 5.2.2 - 04/08/2022
- Optimized the method of renewing caches in the background.
- Fixed a bug that caused a PHP notice of an undefined index occurred in the background.

#### 5.2.1 - 04/04/2022
- Added the ability to attach `.log` files to the bug report form.
- Optimized some HTTP requests.
- Fixed a bug that some setting notices were not displayed in some setting pages including the pages of the listing table of units, auto-inserts, and buttons.

#### 5.2.0 - 04/01/2022
- Added the `Category` unit option for the `Product Search` units.
- Added the `Flat` and `Image` button types and the existing buttons are categorized as `Classic`.
- Tweaked the UI of the button previews.
- Moved the button unit options in the `Common Advanced` section to the new `Button` section.

#### 5.1.6 - 03/19/2022
- Added the ability to set custom text to the plugin credit links.
- Fixed an issue of unnecessary translation items by removing them.
- Updated the Spanish translation.
- Updated the Japanese translation.

#### 5.1.5 - 03/15/2022
- Fixed a bug with Contextual units that some options were not parsed properly.
- Fixed an issue of unnecessary translation items by removing them.

#### 5.1.4 - 03/06/2022
- Fixed an issue with the List template that the layout gets broken with block widgets.
- Fixed a bug that the width of the block widget preview in the Widget page was too narrow.
- Fixed a bug that request counter data for the Poland locale was not stored properly.

#### 5.1.3 - 02/20/2022
- Fixed a bug of incorrect discount percentages.
- Fixed a bug that prices were not retrieved properly in some cases.
- Fixed raw query strings passed from user inputs.

#### 5.1.2 - 02/09/2022
- Fixed a bug that the theme button preview in unit editing pages failed in recent WordPress versions.
- Fixed a bug that caused a warning, "Theme without header.php is deprecated" in the background when loading a unit editing screen.
- Fixed a bug that updating units caused a PHP fatal error in the background when a certain template is selected.
- Fixed a bug that caused a PHP fatal error when saving a custom post type slug for the `Unit Preview` option.
- Fixed a bug that 404 errors appeared for unit preview pages after plugin activation when a custom post type slug is set.
- Fixed an issue that button label text were not sanitized.

#### 5.1.1 - 02/02/2022
- Fixed a bug that search type units could not be created without entering PA-API keys.

#### 5.1.0 - 01/30/2022
- Added the Gutenberg block, "Auto Amazon Links: Unit", that displays selected unit outputs.
- Fixed a bug that SVG definition elements affecting the content height.

#### 5.0.8 - 01/27/2022
- Fixed a bug that oEmbed output height has not been automatically adjusted in the front-end in recent WordPress versions.
- Fixed an issue that embedded frame heights in Gutenberg were not automatically adjusted properly.
- Tweaked the `List` template regarding bottom margin of last products.

#### 5.0.7 - 01/12/2022
- Fixed a bug with the contextual widget that the setting fields did not appear for newly added widgets, which started since 5.0.0.
- Fixed an issue that active templates of deactivated plugins were still loaded.

#### 5.0.6 - 12/21/2021
- Fixed a bug with the Product Search unit type that the `Sort Order` unit option value could not be saved.
- Fixed a bug with the PA-API Product Search unit type that the `Shuffle` unit option did not take effect, which started in v5.0.0.

#### 5.0.5 - 12/12/2021
- Fixed a bug with the PA-API Product Search unit type that empty value of the Keywords unit option with the Additional Attribute option always resulted in no results.

#### 5.0.4 - 12/10/2021
- Fixed an issue that category lists have not been retrieved for some locales.
- Fixed a bug that the `Preview of This Category` section had always no products, which started in v5.0.0.

#### 5.0.3 - 11/08/2021
- Fixed a bug that caused a PHP fatal error in the unit creation pages in some cases.

#### 5.0.2 - 11/05/2021
- Fixed a bug that some API requests failed due to invalid search keyword characters.
- Optimized unit argument handling of Product Search units.
- Optimized HTTP requests.

#### 5.0.1 - 11/02/2021
- Fixed a bug that caused a fatal error in PHP 7.2.x or below due to a trailing comma in a function call, which started in v5.0.0.

#### 5.0.0 - 10/29/2021
- Added the new unit type, `Product Search`, which performs product searches without PA-API for some locales.
- Added the `%discount%` item format tag which displays product discount percentage.
- Added the sub-arguments of the shortcode `[amazon_auto_links]` for the `search` argument, `MinReviewsRating`, `Merchant`, `MinPrice`, `MaxPrice`, `BrowseNodeId`, `SortBy`, `CurrencyOfPreference`, and `LanguagesOfPreference` (Requires PA-API).
- Added the `Hook Priority` option in the `Link Converter` section of the `Tools` page.
- Added the `%author_text%` Item Format tag.
- Added the ability to include names other than authors such as artist, writer and producer with the `%author%` Item Format tag.
- Tweaked UI elements.
- Tweaked the style of product author elements.
- Fixed an issue that the post slug field is missing in the Quick Edit form when a custom post type slug for the unit preview post type is given.
- Fixed a bug that the sort option for the feed unit type did not take effect.
- Fixed a bug that an admin notice that asks to enable Web Page Dumper appeared when unnecessary.
- Optimized HTTP requests for Web Page Dumper.
- Changed some option default values.
- Changed the name of the unit type, `Product Search`, to `PA-API Product Search`.
- Changed the name of the unit type, `Item Look-up`, to `PA-API Item Look-up`.
- Changed the name of the unit type, `ScratchPad Payload`, to `PA-API Custom Payload`.

#### 4.7.9 - 09/21/2021
- Fixed a bug that kept trying updating product elements for some non-existing products.
- Fixed a bug that the wrong page title was displayed for the `Add Feed Unit` and `Add Unit by ScratchPad Payload` pages.
- Fixed a bug that pop-up thumbnails sometimes got stuck when multiple of them are displayed at the same time.

#### 4.7.8 - 09/19/2021
- Optimized the method of displaying prices with Category units to be more accurate when the PA-API keys are set.
- Fixed a bug that inaccurate prices were stored with search-type units resulting in miscalculated discount rates.
- Fixed a bug that malformed prices were stored with some locales resulting in miscalculating discount rates, started since v4.6.9.
- Fixed the no-image-available images for the Swedish locale.
- Fixed a bug that the `Discount Rate` unit filter option produced inaccurate results.
- Fixed an issue that HTTP requests with Web Page Dumper failed in some cases.

#### 4.7.7 - 09/16/2021
- Fixed a bug that caused a syntax error in PHP 7.2 or below, started since 4.7.5.
- Fixed an issue that HTTP requests with Web Page Dumper failed in some cases.

#### 4.7.6 - 09/15/2021
- Added the `Form` setting section and the `Input Cache` field in the `Opt` screen.
- Fixed an issue that loading a category list for the first time sometimes failed in the category selection screen.
- Fixed an issue that the no-image-available images were not in their languages for some locales.
- Fixed a PHP notice, "Undefined index: post_type" occurred in some Ajax calls.
- Fixed a bug that spinner images kept appearing even after Ajax requests are done in the category selection screen.
- Fixed a bug that an unnecessary checkbox appeared in the category selection screen.

#### 4.7.5 - 09/10/2021
- Added the `No Need` field in the `Affiliate Disclosure` setting section.
- Added the `Update Required` field in the `Web Page Dumper` setting section.
- Added a setting notice for outdated Web Page Dumper instances.
- Tweaked setting UI elements.
- Fixed a bug that disclaimer links were linking to the Affiliate Disclosure page even when the page is not accessible.
- Fixed a bug that some HTTP requests were not parsed properly.

#### 4.7.4 - 09/04/2021
- Tweaked setting UI elements.
- Fixed a bug that search, contextual, and URL units did not support some locales.

#### 4.7.3 - 09/01/2021
- Tweaked setting UI elements.
- Fixed a bug that some HTTP responses were not parsed properly.

#### 4.7.2 - 08/28/2021
- Added the `Opt` checkbox in the `Restore Defaults` setting field.
- Tweaked UI elements.
- Fixed a bug that Web Page Dumper caused the error, "a valid URL was not provided", when the list was empty.
- Fixed a bug that a default option value of the Web Page Dumper section failed to be set after Tools options were reset.
- Fixed a bug that caused the PHP Fatal error, "Uncaught Error: Call to a member function get_page_permastruct() on null".
- Fixed a bug that the component checkboxes did not take effect in the `Reset` screen and resulted in restoring all the settings.

#### 4.7.1 - 08/27/2021
- Added a button to create a disclosure page when it does not exist in the `Disclosure` screen.
- Tweaked the style of buttons regarding text decoration.
- Tweaked setting UI.
- Fixed a bug that pop-up images for product thumbnails did not have the correct links.

#### 4.7.0 - 08/26/2021
- Added the `Disclosure` setting screen that allows the user to configure affiliate disclosure.
- Added the `Pop-up Image Preview` unit option.
- Added the `Opt` screen of the `Settings` page.
- Added the ability to reset meta box order and screen layouts of the plugin setting pages.
- Added the `Feedback` and `Report Issues` contact forms in the `Support` screen of the `Help` page.
- Added the `Shuffle` unit option for Product Search units.
- Added the `HTTP Requests` screen in the `Reports` page, which appears when the `backend` debug mode is turned on and allows the user to display or delete HTTP request caches.
- Added the sub-options to the `Debug Mode` option to decide which debug components to enable.
- Moved the `Converter` screen to the `Tools` page and renamed it to `Link Converter`.
- Tweaked UI elements.
- Fixed a bug that showing field tooltips affected the screen width.
- Fixed an issue that disclaimer pop-up tooltips were often cut off.

#### 4.6.24 - 08/21/2021
- Fixed a bug that debug log files were created when the Web Page Dumper option is enabled, started in v4.6.23.

#### 4.6.23 - 08/21/2021
- Tweaked the UI of the category selection screen regarding the `Reload` button.

#### 4.6.22 - 08/18/2021
- Optimized the method of renewing HTTP request caches.
- Deprecated the `Query per Item` unit option for the `Item Look-up` and `URL` unit types.

#### 4.6.21 - 08/17/2021
- Tweaked the default `Item Format` option value.
- Fixed a bug that plugin options were not properly loaded causing inaccessible setting pages, started in v4.6.19.
- Fixed a bug that a last ASIN set to the `ASINs` unit option of the `Item Look-up` unit type got removed, started from v4.6.9.

#### 4.6.20 - 08/12/2021
- Fixed raw HTTP request values, which could cause possible security issues.
- Fixed a bug that selected locale in the `Associates` screen was not reflected, started in v4.6.18.

#### 4.6.19 - 08/12/2021
- Added the `Security` setting section in the `Misc` screen.
- Deprecated the `Form` setting section in the `Misc` screen.
- Fixed a bug that the `Output Formats` options were not saved properly.
- Fixed some unloaded images in the plugin setting pages.
- Fixed a bug that restoring default options for the general options was not properly processed, started in v4.6.17.
- Updated JavaScript libraries.
- Fixed some unescaped HTML outputs.
- Fixed raw HTTP request values, which could cause possible security issues.

#### 4.6.18 - 08/11/2021
- Changed the plugin to `Auto Amazon Links`.
- Tweaked the UI of the `Get New` screen of the `Templates` page.
- Fixed unnecessary browser console log items.
- Fixed raw HTTP request values, which could cause possible security issues.

#### 4.6.17 - 08/09/2021
- Added checkbox items to the `Restore Defaults` option which allows the user to select which options to delete.
- Fixed an incompatibility issue with sites with custom `WP_CONTENT_DIR` and `WP_CONTENT_URL`.
- Fixed a setting message that appears when the user performs an action with no item in the template listing page.
- Tweaked the date format of the `%date%` and `%disclaimer%` `Output Format` tags.
- Tweaked unit error messages.
- Tweaked the layout of the `List` template in widgets.
- Tweaked the unit listing table UI to display a warning when the selected template is deactivated.
- Tweaked the template listing table UI.
- Tweaked the debug output UI.

#### 4.6.16 - 08/06/2021
- Fixed a bug that caused a critical error on some sites with v4.6.15 by reverting the method of generating template IDs.
- Added the Paths and URLs section in the `About` page.
- Tweaked unit warning messages.

#### 4.6.15 - 08/05/2021
- Tweaked the way to generate template IDs.
- Fixed an incorrect UI label.
- Fixed a bug that product updated dates became unavailable when selection multiple sources in category units.
- Fixed a bug that using a combination of White and Black List options produced duplicated items in some cases.
- Fixed PHP notices that says calling functions incorrectly which appear when the unit is saved with the Load with JavaScript unit option enabled.

#### 4.6.14 - 08/04/2021
- Fixed a bug that using a combination of White List ASIN and Black List Title/Description options caused too many database queries.
- Fixed a bug with the White List options that did not whitelist products to be displayed with specified keywords.

#### 4.6.13 - 08/01/2021
- Tweaked the `List` template regarding a responsive width.
- Optimized the translation items by reducing some of them.
- Fixed an issue that category selection failed to generate lists sometimes in the category selection page.
- Fixed an issue that category units failed to find products in the new design of best-seller pages.
- Fixed an issue that there were some remaining data in cleaning plugin data upon plugin uninstall.
- Fixed an issue that the layout of the L`ist` template for sites with the Japanese language was broken.

#### 4.6.12 - 07/30/2021
- Fixed a bug that rating stars were not displayed with the `Load with JavaScript` unit option enabled, started in 4.6.0.
- Fixed a bug that RSS and JSON feeds became invalid for logged-in users with the privilege of editing units, started in 4.6.11.

#### 4.6.11 - 07/29/2021
- Tweaked the behavior of showing unit errors to show them to the logged-in user with the privilege of edit units regardless of the `Show Errors` unit option.
- Tweaked a UI visual element.
- Fixed a bug that unnecessary background tasks were kept being created for some locales, started in v4.6.9.
- Optimized the method to retrieve product ratings and prices for some locales.

#### 4.6.10 - 07/28/2021
- Fixed a bug that the category list became unable to select after failing to load categories in the category selection page, which started in v4.6.4.
- Fixed a bug that product data was not cached properly when the currency or the language option was not the default one, started in v4.6.9.
- Fixed an issue that clicking on an action link from the paged view of custom post type post listing table resulted in landing on the initial view.
- Fixed a bug that the wrong query parameters for language and currency were set for category and embed units if PA-API keys were not set for the locale.

#### 4.6.9 - 07/27/2021
- Optimized the method to retrieve product ratings and prices for some locales.
- Optimized HTTP requests for embed units for some locales.
- Fixed vertical scrollbars not to appear with the List template in embedded views.

#### 4.6.8 - 07/24/2021
- Fixed an incompatibility issue with WordPress 5.8 regarding the plugin widget by unit with search units.
- Fixed an incompatibility issue with WordPress 5.8 regarding widget previews.

#### 4.6.7 - 07/19/2021
- Added a setting notice when activating/deactivating a template.
- Added the ability to reformat template data when activating a template.
- Tweaked how the template stylesheets are enqueued with a simpler handle ID and a version.

#### 4.6.6 - 07/12/2021
- Added warning messages in the unit listing table when the template unit option is not stored properly.
- Fixed a bug that arguments could not be passed in the test/scratch UI, started in v4.6.5.

#### 4.6.5 - 07/10/2021
- Tweaked the style of thumbnails in the category selection page.
- Fixed a bug that an initial selected button label was incorrect in the unit edit page.
- Fixed some `JQMIGRATE` warnings in the browser console.

#### 4.6.4 - 07/06/2021
- Fixed a bug that broke the layout in the category selection page when there is a product description in the preview.
- Fixed an issue that in some locales, categories were displayed in English in the category selection page.
- Fixed a bug that black rating stars were displayed with Feed units.
- Fixed a bug that the selected button label was not properly displayed in the unit editing page.
- Fixed a bug that labels of created buttons were not loaded.

#### 4.6.3 - 07/04/2021
- Fixed an issue that rating stars in `embed` type units were not vertically aligned properly, started in 4.6.0.
- Fixed a bug that encoded query parameters in a URL set to setting fields were removed.

#### 4.6.2 - 07/03/2021
- Fixed a bug that rating star icons did not appear in the category selection page, started in 4.6.1.
- Fixed a bug that rating values were not accurate in locales which use `,`.
- Fixed an issue that rating star icons were not filled in some cases.

#### 4.6.1 - 07/02/2021
- Fixed a bug that rating stars could not be displayed with Ajax, started in v4.6.0.
- Fixed a bug that the `formatted_rating` element of JSON feeds was not formatted properly for the change made in v4.6.0.
- Fixed a bug that the `Renew Cache` unit action link did not renew the feed cache.
- Tweaked line alignment of descriptions, rating stars, and prices of the `Category` template.
- Updated the base and Japanese translation files.

#### 4.6.0 - 06/26/2021
- Added the geotargeting feature.
- Added the Poland locale.
- Added SVG icons for the rating stars and the prime mark.
- Deprecated image files of the rating stars and the prime mark.

#### 4.5.9 - 06/11/2021
- Fixed a bug that re-entering a new pair of PA-API keys for a locale too soon resulted in the locked error message if the previous keys produced an error.

#### 4.5.8 - 05/09/2021
- Fixed a bug that unit custom text was inserted for RSS and JSON outputs which resulted in validation errors.
- Fixed a bug that a box shadow line around product thumbnails appeared in the `List` template for some themes.

#### 4.5.7 - 05/03/2021
- Tweaked the `List` template to adjust layout and font sizes for mobile screens.
- Tweaked the `List` template to give rounded corners to product thumbnails.

#### 4.5.6 - 04/30/2021
- Fixed a bug that the `%description_text%` tag was not formatted properly for category and some other unit types.
- Fixed a bug that second or subsequent category units in the same page did not appear, started since v4.3.4.
- Updated Japanese translation items.
- Updated Spanish translation items.

#### 4.5.5 - 02/20/2021
- Fixed a bug that active auto-insert items were not updated properly when deleting a unit.
- Fixed a bug that auto-insert outputs were displayed even for deleted/trashed units.
- Fixed a bug that some prices for books were not displayed for the `embed` unit type.
- Fixed a bug that some product thumbnails did not load for the `embed` unit type.
- Fixed a bug that the `%feature%` tag displayed the wrong output for the `embed` unit type.

#### 4.5.4 - 02/11/2021
- Fixed a bug that the `associate_id` direct unit argument did not take effect, which started from v4.5.0.
- Fixed a bug that an Associate ID input value was lost when creating category units, which started from v4.5.0.
- Fixed a bug that the `Item Format` unit option was not saved properly, which started from v4.5.3.

#### 4.5.3 - 01/31/2021
- Added the `Creating Units` option under the `Access Rights` section of the `Misc` tab which lets the user set a user role to allow creating units.
- Fixed a bug that the wrong status was displayed when testing PA-API keys failed in the Associates tab.
- Fixed a bug that setting forms could not load in PHP 8.0.

#### 4.5.2 - 01/15/2021
- Fixed a bug that caused a PHP warning, "strpos(): Empty needle in "...AmazonAutoLinks_LinkConverter_Output.php on line 105".

#### 4.5.1 - 01/08/2021
- Fixed an issue that caused a PHP error, "Call to a member function getValue() on null in ...AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport.php:152" on some servers.

#### 4.5.0 - 01/07/2021
- Added the ability to set Associate IDs and PA-API key pairs for multiple locales.
- Added support for multiple locales for the link converter.
- Added the `Associates` setting tab.
- Added the ability to utilize Web Page Dumper to help HTTP requests.
- Optimized HTTP requests.
- Fixed an issue that the `Link Style Query Key` general option could be empty, which resulted in unit RSS and JSON feeds broken.
- Deprecated the `Associates IDs` option in the `oEmbed` section in place of `Amazon Associates` section.
- Deprecated the `Authentication` setting tab.
- Updated the base translation file.
- Updated the Japanese translation.
- Fixed a bug that Amazon site cookies were not stored properly.
- Tweaked the style of UI of the `3rd Party` tab.

#### 4.4.6 - 12/29/2020
- Fixed a bug that iframe elements of embedded outputs flickered when multiple instances of them were present in one page.
- Fixed a bug with embedded outputs that failed to display base64 encoded sub-images.
- Fixed a bug that embedded outputs were not possible with the locales of Sweden, Saudi Arabia, Singapore, UAE, Turkey, and China.
- Fixed a bug with embedded outputs that review counts were not accurate for some cases.
- Fixed a bug that the `%description%` and `%content%` were not functioning for the embed unit type when the PA-API keys are not set.
- Fixed a bug with embedded outputs that thumbnails were not displayed with some locales.

#### 4.4.5 - 12/22/2020
- Tweaked the style of the List template regarding vertical margins of buttons.
- Fixed a bug that unnecessary debug log was created when some unit options are enabled, started since 4.4.4.
- Fixed a bug with embedded outputs that did not display errors when no product is found.
- Fixed a bug that height of embedded outputs with iframe was not adjusted properly when multiple instances were present.
- Fixed a bug that using caches failed in some cases when an HTTP response contains an error.
- Fixed a bug with checks of setting PA-API keys which possibly resulted in unnecessary PA-API calls when they are not set.
- Fixed a bug that errors are not displayed for embedded outputs with iframe for a product of a different locale from the one set in the Authentication screen.
- Fixed a bug that unnecessary PA-API requests were performed when a product URL of a different locale from the one set in the Authentication screen is pasted in the post editor.
- Fixed an issue that user-defined custom CSS rules set via Additional CSS of Customizer did not load in plugin embedded pages for iframe.

#### 4.4.4 - 12/13/2020
- Added the `Tasks` tab in the `Reports` page.
- Added the `Site Debug Log` tab in the `Reports` page which appears when the site debug mode it turned on.
- Tweaked the style of the UI of the `Products` tab.
- Fixed a bug that background routines of updating products failed when the default country was not set.
- Fixed a bug that invalid plugin tasks were not cleared properly.
- Fixed a bug that a product filter was not functioning for category units.
- Fixed a bug that tests could not run via UI on some systems.
- Fixed a bug that caused a PHP notice, "Undefined index: view in ...AmazonAutoLinks_PostType_Unit_ListTable.php on line 54".

#### 4.4.3 - 12/10/2020
- Added the `Products` tab in the `Reports` page.
- Tweaked the UI of unit settings to have a warning message for some options that require PA-API keys when PA-API keys are not set.
- Fixed a bug in the chart of PA-API request counter which displayed the wrong step size and that the tooltip title was not set properly.
- Fixed a bug that the prime flag was not set properly for category units.

#### 4.4.2 - 11/27/2020
- Tweaked the UI of Manage Units listing table not to display feed icons for contextual units.
- Fixed a bug that the discount filter did not function.
- Fixed a bug that caused a PHP warning, "Division by zero in ...AmazonAutoLinks_UnitOutput__ProductFilter_ByDiscountRate.php on line 71.
- Fixed a bug that proper product prices were not displayed.

#### 4.4.1 - 11/18/2020
- Added the `Details` column in the unit listing table and removed the `Template` and `Unit Type` columns.
- Fixed a bug that dud not load stylesheets in the setting pages.

#### 4.4.0 - 11/13/20020
- Added the Sweden locale.
- Added the ability to process pasted Amazon URLs in the post editor when they are not of a product URL by performing like the URL unit type.
- Added the ability to log PA-API request counts.
- Added a warning in unit outputs when an Associate tag is not set.
- Added a warning when the site PHP version is below 5.6.20.
- Tweaked the style of the `List` template regarding the thumbnail width and the font size of now-retrieving elements in embedded iframe.
- Tweaked the style of the Error Log and Debug Log tab screens.
- Fixed a bug that unusable proxies were not cleared automatically.
- Fixed a bug that `Item Look-up` units failed to retrieve products with PA-API with a certain number of items.
- Fixed a bug that did not resume now-retrieving elements for iframe embedded outputs.
- Fixed a bug that caused a PHP notice, "file_get_contents(): read of ... bytes failed with errno=13 Permission denied in ...AmazonAutoLinks_VersatileFileManager.php on line 107."
- Fixed a bug that caused broken mark-ups in the Error Log tab when a log item includes a detail of some HTML elements.
- Fixed a bug that caused a PHP fatal error: "Uncaught Error: Wrong parameters for Exception ..." in the `Tests` page when an Ajax request failed.
- Changed the default value of the `Show Errors` unit option to `Show errors`.
- Moved the `Error Log` and `Debug Log` tabs to the `Reports` page.

#### 4.3.9 - 11/06/2020
- Added the Italian translation.
- Updated the Japanese translation items.
- Fixed a bug that caused a PHP warning saying the directory already exists with mkdir().
- Fixed a bug that some translation items were unable to translate.

#### 4.3.8 - 11/02/2020
- Added the `File Permissions` field in the `About` tab of the `Help` page.
- Regenerated the base language file.
- Fixed a bug that failed to create a plugin temporary directory on some sites causing a PHP Warning: file_put_contents(...) failed to open stream: No such file or directory in ...AmazonAutoLinks_VersatileFileManager.php on line 89.
- Removed the `Server Information` field in the `About` tab of the `Help` page.

#### 4.3.7 - 10/28/2020
- Fixed a bug that caused the PHP error, "Parse error: syntax error, unexpected ')' in ...AmazonAutoLinks_UnitOutput__ItemFormatter.php on line 130" in below PHP 7.3.

#### 4.3.6 - 10/24/2020
- Fixed a bug that some background routines often reached the PHP maximum execution time, started since v4.3.4.
- Fixed a bug that background routines which remained due to an error of another routine did not resume smoothly.

#### 4.3.5 - 10/21/2020
- Optimized HTTP requests.
- Fixed a bug that the contextual units were not finding products due to the wrong locale if the default unit options were not set, started since v4.3.4.
- Fixed a bug that caused the PHP notice, "Trying to access array offset on value of type bool in ...AmazonAutoLinks_TemplateOption.php on line 176."
- Fixed a bug that caused the PHP notice, "Trying to access array offset on value of type null in ...AmazonAutoLinks_DatabaseTable_aal_request_cache.php on line 227." in PHP 7.4.
- Fixed a bug that caused the PHP fatal error, "Uncaught Error: Cannot use object of type WP_Error as array." when trying to retrieve HTTP status code, started since v4.3.4.

#### 4.3.4 - 10/17/2020
- Added the Saudi Arabia locale.
- Fixed a bug that descriptions in the category unit only appeared for the first product.
- Fixed a bug that passing the `unit_format` direct arguments were not taking effects.
- Fixed a bug that passing the `item_format`, `image_format`, `title_format`, `unit_format` direct arguments were not taking effects, started since v4.0.0.
- Fixed a bug that country flags were not displayed in the `Embed` setting tab.
- Tweaked the default `Image Format` unit option regarding widths.
- Tweaked the style of Embed setting tab.
- Optimized HTTP requests to Amazon sites.
- Optimized PA-API requests.

#### 4.3.3 - 10/04/2020
- Fixed a bug that updating now-retrieving elements failed with a certain number of items.
- Fixed an issue that on some sites with a database of the utf8-mb4 type collation got an error " Specified key was too long; max key length is 1000 bytes...".
- Fixed a bug that caused an error "database error Duplicate entry '...' for key 'product_id' for query ..." when activating the plugin after downgrading it.

#### 4.3.2 - 09/28/2020
- Fixed a bug that caused a browser console error in the setting screens.
- Fixed a bug that Ajax requests for non-logged-in visitors failed with the message, "Could not get a user ID".

#### 4.3.1 - 09/26/2020
- Tweaked the setting UI to include a current tab title in the `<title>` tag.
- Optimized PA-API requests to prevent duplicated payloads.
- Optimized the process of updating pending elements labeled "Now retrieving..."
- Fixed an issue with the `List` template that some sub-images overlapped the thumbnail.
- Fixed a bug that button links were broken since 4.3.0.
- Reverted the change made in v4.3.0 regarding the default thumbnail layout to drop `max-height`.

#### 4.3.0 - 09/24/2020
- Added the ability to resume pending elements saying "Now retrieving..." with JavaScript.
- Added the `Language` and `Currency` options in the `Default` setting screen.
- Added the `Reset` button in the `Default` setting screen.
- Added the `Custom Text` unit option.
- Added the `Unit Format` option under the `Output Format` unit option.
- Added the ability to toggle the status of buttons.
- Added the `[aal_button ...]` short code to display buttons.
- Added the `Override the button label.` unit option that allows the user override a button label.
- Added the theme button option for the `Select Button` option.
- Added `target="_blank"` to the unit view links to open the link in a new tab
- Added the `Test` page and `Debug Log` tab which appear when the site debug mode is turned on.
- Added the mechanism to reuse HTTP caches if a new response contains an error.
- Fixed a bug that some internal styles were duplicated in the `Manage Units` page.
- Fixed an issue that the setting form could not be submitted after an hour passed.
- Fixed a bug that caused overhead in `admin-ajax.php`.
- Refined some setting form elements by updating Admin Page Framework.
- Refined the Error Log screen.
- Minified CSS files.
- Minified JavaScript scripts.
- Tweaked the style of the default thumbnail layout to drop `max-height`.
- Tweaked the style of setting form fields.
- Tweaked the button style.
- Optimized the embed unit type.
- Removed the deprecated unit types, `tag` and `similarity_lookup`.

#### 4.2.10 - 09/18/2020
- Optimized PA-API requests.
- Fixed a bug that descriptions were duplicated in `embed` unit type outputs, started from v4.2.9.
- Fixed an issue that the `%date%` value was not accurate.
- Fixed CSS rules that were not taking effect.
- Fixed duplicated id attribute values in HTML mark-ups in the category selection screen.
- Fixed an incompatibility issue with PHP v5.3.x or below that caused a syntax error.
- Fixed a mark-up error in an unit price disclaimer output element.

#### 4.2.9 - 09/12/2020
- Optimized the `embed` unit type to display elements as much as possible in the first load when the API keys are set.
- Fixed a bug with the embed unit type that iframe sources were loaded twice.

#### 4.2.8 - 09/08/2020
- Tweaked the default layout of the `List` template not to support the min width for the thumbnail.
- Fixed a bug that the default value of the `Language` unit option was not set properly.
- Fixed a bug that images and titles failed to show with the embed unit type in some cases.
- Optimized PA-API requests.

#### 4.2.7 - 09/03/2020
- Fixed a bug with the contextual widget that failed to retrieve products due to the incorrect currency and language arguments.

#### 4.2.6 – 08/29/2020
- Fixed a bug that Item Look-up and Contextual unit types could not display product titles, which started since v4.0.0.

#### 4.2.5 – 08/26/2020
- Fixed an incompatibility issue with PHP 5.4.x or below regarding the `empty()` language construct.

#### 4.2.4 - 08/24/2020
- Tweaked styling of `List`, `Category`, `Search` templates.
- Fixed a bug that in some cases `preferred_currency` and `language` unit options were not set.

#### 4.2.3 - 08/18/2020
- Optimized PA-API requests.

#### 4.2.2 - 08/16/2020
- Added the Spanish translation.
- Updated the translation files.
- Added debug information in the console.log of the category selection screen.
- Added more precise error messages to appear in the `embed` unit type outputs.
- Fixed incompatibility with WordPress 5.5 regarding input radio boxes that caused the unchecked state while selecting them.
- Fixed a bug that modified date of the product was not retrieved for non-cached results.

#### 4.2.1 - 08/06/2020
- Optimized performance in the category selection screen of the category unit type.

#### 4.2.0 - 08/03/2020
- Added the proxy option.
- Refined the category selection screen of the category unit type.

#### 4.1.0 - 07/11/2020
- Added the `ScratchPad Payload` unit type that allows custom API queries generated on [Scratchpad](https://webservices.amazon.com/paapi5/scratchpad/).
- Added the ability to accept `*` for the `Product Filters` options to apply to everything.
- Added support for `PhpZon` shortcodes.
- Added the `Show Errors` unit option under the `Common Advanced` section that allows the user to decide whether to display output errors.
- Added the `%author%` tag that displays product authors for the `Item Format` unit option.
- Added the `%image_size%` Item Format option tag.
- Added new translated items for the Japanese translation.
- Added the Netherlands locale for search and category unit types.
- Added the Singapore locale for search unit types.
- Tweaked the default `Item Format` option to set `min-width` for the thumbnail container.
- Tweaked the UI regarding the admin bar menu items.
- Renewed the base translation file.
- Fixed a bug that activated templates which are no longer exist were still loaded and caused PHP warnings in the template listing screen.

#### 4.0.6 – 06/26/2020
- Fixed a bug that the Item look-up unit creation screen was unreachable.
- Fixed a bug that caused an undefined index warning when a custom template is unexpectedly removed.

#### 4.0.5 – 04/09/2020
- Fixed a bug that caused a PHP error saying class not found in the post editing screen.

#### 4.0.4 - 03/08/2020
- Tweaked the default `Image Format` option layout value.
- Fixed an issue that units with the `Prevent Duplicates` unit option enabled produced no results in the unit preview page when some third-party plugins call the `the_content` filters in prior to rendering the post.
- Fixed a bug that the `Output Format` unit options were not loaded properly for some cases.
- Fixed a bug that template names were not displayed in Manage Units screen.
- Fixed a bug that caused a PHP fatal error "Uncaught Error: Call to undefined function register_block_type()" for WordPress v4.9.x or below.
- Fixed a bug that the default template was not listed in the `Template` unit option when no template was activated, started from v4.0.0.

#### 4.0.3 - 03/03/2020
- Changed the `Secret Access Key` option input field to be masked.
- Fixed a bug that templates were doubled in the `Templates` setting screen.
- Fixed a bug that cased the fatal PHP error in setting pages.

#### 4.0.2 - 02/26/2020
- Tweaked the `List` template regarding the width of sub-image container elements.
- Tweaked the default `Image Format` option layout value.
- Added the `Image` template.
- Changed the behavior of when the template path is not found to apply the default template instead of showing an error.
- Fixed a bug that templates were not properly loaded with some cases, started from v4.0.0.

#### 4.0.1 - 02/24/2020
- Changed the `Now retrieving...` message not to show when API keys are not set.
- Optimized the process of performing PA-API requests.
- Fixed a bug that last inputs were not properly restored when creating search type units.
- Fixed a bug that the `ASINs` option for the `Item Look-up` unit type was sanitized properly when saved, started in 3.9.0.
- Tweaked the styling of the `List` template for widget areas.
- Tweaked the default `Image Format` option layout value.
- Tweaked the process of retrieving ratings.

#### 4.0.0 - 02/20/2020
- Added the `Compress` option in the `Cache` section.
- Added the `Output Formats` unit options that support `Item Format`, `Image Format`, and `Title Format` options for each template.
- Added the `Feed` unit type that imports unit data from external sites and display them.
- Added the ability to automatically embed product links when an Amazon URL is pasted in a post editor.
- Added the `product_title` shortcode parameter which alters the product title and serves as anchor text.
- Added the new `Text` template.
- Tweaked the styling of the `List` template for widget areas and relatively small thumbnail sizes.
- Optimized the process of saving error log entries.
- Optimized the outputs of the `[amazon_textlink]` shortcode of Amazon Associates Link Builder to remove some outer container.
- Changed the default behavior of compressing caches.
- Changed the `Category` and `Search` templates not to be the default activated templates.
- Changed the default template to `List` for all the unit types.
- Fixed PHP warnings of deprecation when displaying buttons.
- Fixed a bug that some URL query parameters of buttons were not properly set.
- Fixed a bug that last inputs for creating units were not stored properly for some unit types.
- Fixed a bug that category units did not show the updated date in the price disclaimer text.
- Removed some array key elements and local variables passed to the template file which has been kept for backward-compatibility.
- Removed legacy code for v2 or earlier.
- Deprecated the `Item Format` unit option for the new `Output Formats` unit option that support item format option per template.

#### 3.12.0 - 02/05/2020
- Added the `Keywords to Exclude` unit option for the `Contextual` unit type.
- Optimized the caching mechanism to compress data.

#### 3.11.1 - 01/29/2020
- Added options to select cache tables when clearing caches from the `Cache` screen.
- Fixed a bug with some locales that rating stars were not displayed.
- Fixed a bug that `Country` unit option was not property set for some cases.
- Fixed a bug with some unit options for the `Product Search` unit type.

#### 3.11.0 - 01/25/2020
- Optimized item look-up (GetItems) PA-API requests.
- Added the ability to capture Amazon Associates Link Builder block contents and shortcodes.
- Tweaked the layout of the Error Log screen.

#### 3.10.1 - 12/13/2019
- Added the `Singapore` locale for the category unit type.
- Fixed a bug with JSON feeds that caused a JSON syntax error.
- Fixed a bug with RSS feeds that caused a validation error.
- Fixed an issue that for some rare cases, prices did not show up.

#### 3.10.0 - 11/23/2019
- Added the ability to specify preferred language and currency for units.
- Added the ability to filter items by Amazon Prime service eligibility.
- Added the ability to filter items by free shipping and FBA delivery eligibility.
- Added the `About` tab in the `Help` admin page which displays plugin information.
- Tweaked the styling of templates regarding prices, the prime mark, and rating stars, and margins of features and categories.
- Added the `Delivery Flags` unit option for the search unit type.
- Fixed an issue that `%content%` and `%description%` outputs were no longer available for category units since v3.9.0 due to the use of PA-API5.
- Fixed a bug in the template listing table that information of listed templates were showing outdated information even they were updated.
- Fixed a bug that API requests did not go through with some custom sort options.
- Fixed an issue that the `%description%` output created an empty element when the product description was not available.
- Fixed an issue that unit status was not updated when an error occurred until the cache was renewed.
- Fixed a bug that the `Raw` `Sort` unit option for the category unit type treated as `Random`.
- Fixed a bug that the `%prime%` Item Format variable did not show the prime mark.
- Fixed a bug that the advanced filter unit option for adult products caused some overhead.

#### 3.9.6 - 11/20/2019
- Fixed a bug that the `Raw` sort unit option for the category unit type was treated as `Random`, which has started since v3.9.3.

#### 3.9.5 - 11/16/2019
- Fixed a bug with the category unit type that caused excessive nested function calls when setting a large number of item count, which has started since 3.9.0.

#### 3.9.4 - 11/14/2019
- Added the `noopener` rel attribute value for generated hyperlinks.
- Fixed a bug that caused the PHP fatal error, `Uncaught Error: Call to undefined method` in the category select page of the category unit type, which has started since v3.9.3.
- Fixed a bug for category units that the same list of products were shown even when multiple categories are added, which has started since v3.9.0.
- Fixed a bug that multiple categories could not be added when creating a new category unit, which has started since v3.9.0.
- Fixed a bug that thumbnails of the category unit type were not displayed properly, which has started since v3.9.0.

#### 3.9.3 - 11/13/2019
- Fixed a bug that the `Sort` unit option of the category unit type did not take effect, which has started since v3.9.0.

#### 3.9.2 - 11/06/2019
- Fixed a bug that outputs with `%category%` and `%feature%` unit `Item Format` option variables were not properly formatted, which has started since v3.9.0.
- Fixed a bug with some advanced product filter options that did not retrieve product data properly.
- Fixed a bug that caused a PHP fatal error with advanced product filter options, which has started since v3.9.0.
- Fixed a bug that cached connectivity status was shown in the `Authentication` page.

#### 3.9.1 - 11/04/2019
- Added the `Minimum Review Rating` advanced unit option for the search unit type.
- Fixed a compatibility issue with PHP 7.3, occurred when browsing category lists.

#### 3.9.0 - 11/01/2019
- Added support for PA-API 5 (deprecated the use of PA-API 4, which is no longer functional as of Oct 31, 2019 due to the termination by Amazon).
- Added the ability for the category unit type not to require Product Advertising API keys.
- Added the `Error Log` page (Tools -> Error Log) that displays plugin errors.
- Added the a unit filter option to remove adult products.
- Added the option to disable widgets.
- Added the `%prime%` variable for the `Item Format` unit option which displays a prime icon.
- Fixed a bug that rating stars appeared in `category` units when the product did not have a review, started since v3.8.12.
- Deprecated `Similarity Look-up` unit type as PA-API 5 does not support similarity look-up.
- Deprecated `similar_product_image_size` and `similar_product_max_count` unit options.

#### 3.8.14 - 05/22/2019
- Fixed an issue with the `Types` option of category units that lead to find no products.

#### 3.8.13 - 05/19/2019
- Optimized the `category` and `url` unit types to reduce PA API requests when no items are found.
- Fixed an issue that some products of the `Types` option of category units have been no longer available.

#### 3.8.12 - 03/30/2019
- Fixed a bug that `Access Rights` -> `Capability` option did not take effect.
- Fixed a bug that truncating database tables were not successful in some cases.
- Fixed a bug that periodic background tasks of clearing expired caches were not functioning since v3.7.3.
- Optimized the `category` unit type to reduce unnecessary database queries and API requests.

#### 3.8.11 - 03/09/2019
- Optimized the use of Amazon Product Advertising API to reduce unnecessary API requests.

#### 3.8.10 - 03/04/2019
- Changed link styles other than the default one to preserve URL query parameters given by the Amazon Product Advertising API.

#### 3.8.9 - 02/26/2019
- Updated the Japanese translation.
- Fixed an issue of unnecessary function calls in `admin-ajax.php`.
- Fixed a misspelling in the `Manage Units` page regarding PHP codes.

#### 3.8.8 - 01/17/2019
- Added the `aal_ajax_loaded_unit` jQuery event for templates using JavaScript.
- Fixed an issue that plugin translations are not recognized by wordpress.org by adding `Text Domain` and `Domain Path` to the plugin comment header
- Fixed a broken link in a setting page.

#### 3.8.7 - 01/11/2019
- Updated the Japanese translation.
- Added the German translation.
- Fixed an issue of too few products shown due to product filters when setting a small number of the `Item Count` unit option.

#### 3.8.6 - 01/08/2019
- Fixed a bug of the malformed product category outputs with the `%category%` `Item Format` variable.
- Fixed a bug with the `%category%` variable for the `Item Format` unit option, which inserted debug outputs.
- Tweaked styling of the `List`, `Category` and `Search` templates.

#### 3.8.5 - 01/04/2019
- Fixed a bug with the `%feature%` variable for the `Item Format` unit option, which inserted debug outputs.
- Fixed an issue that sometimes pending product details did not complete.

#### 3.8.4 - 12/30/2018
- Tweaked styling of the template listing page.

#### 3.8.3 - 12/27/2018
- Tweaked styling of the `List`, `Category` and `Search` templates.

#### 3.8.2 - 12/22/2018
- Fixed an issue that category units often returned insufficient number of products.

#### 3.8.1 - 12/20/2018
- Changed the category unit type to require Product Advertising API keys.
- Added the `Raw` sort select option for the category unit type.
- Deprecated the `Date` sort select option for the category unit type.
- Deprecated the `Keep the raw title` unit option for the category unit type.
- Fixed an issue that category units no longer showed products due to recent deprecation of Amazon product feeds by the Amazon store sites.
- Fixed a bug that category unit caches were not deleted properly via the action link in the `Manage Units` page.
- Fixed a bug that internally stored URLs for category units were malformed in some occasions.

#### 3.8.0 - 12/03/2018
- Changed the default `Item Format` unit option value.
- Added the default template `List`.
- Added the `%category%`, `%feature%`, `%date%`, `%rank%` variables for the `Item Format` unit option.
- Added the ability to convert amazon links in posts, comments and possibly other areas into user's associate links.
- Fixed a bug that review numbers get broken characters for some locales.
- Fixed a bug that descriptions of category units were HTML-encoded.

#### 3.7.10 - 11/14/2018
- Fixed a bug that caused a PHP warning upon plugin uninstall.

#### 3.7.9 - 11/09/2018
- Fixed an issue with a contextual unit that the unit status became `error` due to no context in the preview.
- Fixed a bug that a unit status of the category unit type was not updated when it is created for the first time until the cache was renewed.
- Fixed a bug that ratings became an incorrect number for some cases.

#### 3.7.8 - 11/04/2018
- Added the `Toggle Status` bulk action for the Auto-insert listing table.
- Tweaked UI by not displaying some action links and bulk actions in the drop down list in the trash view of the listing table.
- Fixed an issue that unit status was not properly updated when an action link is clicked.

#### 3.7.7 - 10/29/2018
- Tweaked the visual of unit status and updating indications in the unit listing table.
- Added the ability to reset unit status of units when caches are deleted in the setting page.
- Optimized the process of performing API requests for product information by reducing the number of HTTP requests.

#### 3.7.6 - 10/25/2018
- Added the `Ready/Loading` unit status to be displayed when the unit has not been loaded yet.
- Added unit action links to the bulk action drop down list in the unit listing page.
- Added the ability to reduce each HTTP request cache size.
- Fixed a bug that debug outputs for units were not shown when the unit response had an error.
- Fixed a bug with the `Renew Cache` action link of Category units.
- Fixed a bug that caused `PHP Notice:  Undefined index: constructor_parameters...` in the background, started with v3.7.5.

#### 3.7.5 - 10/16/2018
- Added the ability to reduce URL unit cache sizes.
- Added the `Custom URL Query` unit option field.
- Fixed an issue that dates in the disclaimer output were not accurate.
- Fixed a bug with the `Renew Cache` action link of URL units.

#### 3.7.4 - 10/09/2018
- Added the `aal_action_activate_templates` and `aal_action_deactivate_templates` action hooks so that third parties can toggle the template status.
- Fixed some broken links in the admin area.

#### 3.7.3 - 10/02/2018
- Added the ability to limit the overall cache sizes.
- Added a field notice of whether the periodic check of cache removal is functional in the setting page.

#### 3.7.2 - 09/29/2018
- Fixed an issue that duplicated database queries were performed with category units.
- Fixed a bug that excluding sub-categories for category units did not fully take effect.

#### 3.7.1 - 09/24/2018
- Fixed an issue that the `No Products Found` message was moved to the top in the category selection screen of the Category unit type.
- Tweaked unit error outputs which include the change of the class selector to `warning` from `error`.

#### 3.7.0 - 09/13/2018
- Added the ability to display errors in the unit listing table of the `Manage Units` page.
- Optimized the number of API requests based on the `Item Format` option.
- Optimized API request parameters regarding similar products.

#### 3.6.7 - 09/07/2018
- Added the `aal_filter_api_request_uri` filter hook to allow third-parties to modify API request URI.
- Added the `Associate ID` field in the `Authentication` setting section for some locales.

#### 3.6.6 - 08/09/2018
- Added the `Data` section in the `Reset` setting page, which handles export/import options.
- Added the ability to clean up used custom post type posts upon plugin uninstall.

#### 3.6.5 - 08/04/2018
- Added the translation items for the Japanese and default language file.

#### 3.6.4 - 07/29/2018
- Added the `aal_filter_product_link` filter hook to allow third-parties to modify product links.

#### 3.6.3 - 07/17/2018
- Fixed a compatibility issue with third party plugins/themes which attempt to instantiate the plugin widgets.

#### 3.6.2 - 07/09/2018
- Fixed a bug that the default button is not created.

#### 3.6.1 - 07/04/2018
- Added `rel='nofollow'` to the button links.

#### 3.6.0 - 06/22/2018
- Added the `Load with Javascript` unit option that lets the user decide whether to display the unit with JavaScript.

#### 3.5.7 - 06/09/2018
- Fixed an issue that some categories could not be recognized when creating a category unit.

#### 3.5.6 - 06/05/2018
- Added SSL support for impression counter scripts.
- Added the API response error message to be displayed for API authentication in the setting page.

#### 3.5.5 - 05/11/2018
- Fixed a bug that incorrect categories were displayed when creating a new product search and item search unit.
- Added support for the Australia locale.
- Added support for the locales of Mexico and Brazil for the search units.

#### 3.5.4 - 05/02/2018
- Fixed an issue that category units could not detect some sub-categories when creating a unit.
- Added custom filter hooks in the feed template to allow third-parties to modify the RSS feed outputs.

#### 3.5.3 - 09/10/2017
- Changed the default value of the Credit Link unit option.

#### 3.5.2 - 06/08/2017
- Fixed an issue that the search unit types missed product thumbnails in rare cases.

#### 3.5.1 - 05/03/2017
- Fixed an issue that the random sort order for URL and Item Look-up units was applied after the product data were retrieved which caused the same items to constantly appear.

#### 3.5.0 - 01/23/2017
- Added the `Renew Cache` action link in the unit listing table.
- Added the `search` shortcode and function argument which performs a keyword search with the set keywords.
- Added the `asin` shortcode and function argument which list products of the set ASINs.
- Added the `Contextual` unit type.
- Added the `Sort Order` option to the `Item Look-up` unit type.
- Refined the API request caching mechanism.
- (breaking change) Fixed typos in option key names. This fix affects the stored option values of `Interval for Removing Expired Caches` and `Caching Mode`. Some users may need to re-save the options.
- Fixed a bug that produced invalid RSS2 and JSON formats.

#### 3.4.13 - 01/05/2017
- Fixed a bug that an incorrect offered price was displayed with the `%price%` variable of the `Item Format` option.
- Tweaked the accuracy of detecting products with URL units.

#### 3.4.12 - 12/24/2016
- Fixed a bug that invalid user inputs for the `Item ID` option were saved with the `Item Look-up` unit.

#### 3.4.11 - 12/16/2016
- Fixed a warning `wp_kses_js_entities is deprecated since version 4.7.0`.
- Fixed an issue that static auto-insert was not performed when a draft is saved.
- Changed the `%price%` variable for the `Item Format` unit option to show the lowest offered price from just a discounted price when available.

#### 3.4.10 - 11/28/2016
- Fixed a bug with static auto-insert.

#### 3.4.9 - 11/27/2016
- Fixed an issue that some API requests were not cached properly.

#### 3.4.8 - 11/25/2016
- Added cache size indications in the `Cache` setting section.
- Fixed a bug that unexpired caches were deleted when deleting expired caches.
- Fixed an issue that some ASINs were not detected accurately in URL units.
- Fixed a bug that PHP warnings occurred in the background in some rare occasion.

#### 3.4.7 - 11/06/2016
- Fixed a bug that the Contextual Products widgets were no longer displaying any products, introduced in 3.4.6.
- Fixed a bug occurred in PHP 5.3 that caused a warning `debug_backtrace() expects at most 1 parameter, 2 given`.

#### 3.4.6 - 11/02/2016
- Fixed a bug with the shortcode that some direct product search arguments were not recognized.

#### 3.4.5 - 10/27/2016
- Fixed an issue that reaches PHP max input vars in the auto-insert definition page on some servers.

#### 3.4.4 - 09/16/2016
- Fixed an issue that some locale specific API keys were not connected to the API server by adding the `Server Locale` option in the `Authentication` section.

#### 3.4.3 - 09/02/2016
- Fixed a bug of discount prices displayed with the `%price%` variable for the `Item Format` unit option.

#### 3.4.2 - 06/09/2016
- Fixed a bug that products of Category units were not displayed on some servers introduced in v3.4.1.

#### 3.4.1 - 05/31/2016
- Fixed a bug that the `%rating%` variable in the `Item Format` option produced HTML outputs with an invalid structure.

#### 3.4.0 - 03/17/2016
- Added the ability to automatically extract ASINs for the Item Look-up and Similarity search unit types.
- Added the ability to set a custom label for the unit preview page.
- Added the `Interval for Removing Expired Caches` option.
- Added the default unit options.
- Changed the `%price%` variable in the `Item Format` option to display a discounted price when available.
- Fixed a bug that `ISBN` could not be set with the `Item Look-up` unit type even the locale was set to `US`.
- Fixed a bug that expired caches were not cleared automatically.
- Fixed a bug that the custom data base tables did not have the proper character set and collation.

#### 3.3.6 - 01/14/2016
- Fixed PHP warnings caused by using a deprecated method.

#### 3.3.5 - 01/13/2016
- Tweaked the style of an Auto-insert option.
- Fixed a bug that caused a warning when setting the Post Type Slug option ( `Settings` -> `General` -> `Unit Preview` -> `Post Type Slug`).
- Optimized performance in the admin area.

#### 3.3.4 - 01/02/2016
- Fixed a bug that a fatal error occurred with category units on sites enabling SSL.
- Improved performance of the setting pages.

#### 3.3.3 - 12/31/2015
- Fixed a bug that caused illegal string offset warnings with stored template data.

#### 3.3.2 - 12/29/2015
- Fixed an issue that the setting forms could not be displayed when third-party plugins or themes have JavaScript errors in the same page.

#### 3.3.1 - 12/25/2015
- Fixed invalid offset warnings in PHP 7.
- Fixed a bug that an invalid character was inserted in the RSS feed.

#### 3.3.0 - 12/23/2015
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

#### 3.2.4 - 12/13/2015
- Fixed an issue that unit preview could not be displayed without re-saving the site permalink options after setting a custom unit preview post type slug.
- Fixed a bug that a `View` action link was inserted in different post type listing table when a custom unit preview post type slug was set.
- Fixed a bug in the contextual products widget that the Credit Link option was not displayed properly.
- Fixed an issue of a fatal error `Maximum function nesting level of 'x' reached` when the server enables the XDebug extension and sets a low value for the `xdebug.max_nesting_level` option.
- Tweaked the appearance of the auto-insert setting page.

#### 3.2.3 - 12/11/2015
- Fixed a compatibility issue with WordPress 4.4 that some widget options could not be saved.

#### 3.2.2 - 12/09/2015
- Added a unit option to select credit link type.
- Fixed a bug that re-selecting categories via the Select Categories button in the Category unit editing page let to a fatal error, introduced in v3.2.0.
- Changed the minimum required cache duration to `600`.
- Changed the Found Items field in the URL unit definition page to display Not Found message for finding no item.

#### 3.2.1 - 12/04/2015
- Fixed a bug with the `Item Look-up` and `URL` unit types that the `Number of Items` option did not take effect.
- Fixed a bug that some Amazon Product Advertising API response errors could not be displayed when the `Query per Item` option was enabled.
- Fixed incorrect inline CSS values in the default Image Format unit option.
- Changed the default template of the Contextual Products Widget to `Search`.
- Tweaked the style of `Search` and `Category` templates for disclaimer elements in widgets.
- Tweaked the style of `Search` template to wrap descriptions.
- Removed some advanced options of the URL unit type as their values could not be used rather led to errors.

#### 3.2.0 - 12/02/2015
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

#### 3.1.4 - 11/27/2015
- Added the `search_per_keyword` argument for the shortcode which can be set to `true` when performing search with multiple keywords.
- Enhanced the ability of the contextual product widget.

#### 3.1.3 - 11/26/2015
- Changed the unit template formatting options to accept some inline CSS properties.
- Tweaked the style of rating images of the `Category` and `Search` templates.
- Tweaked the style of sub-images in widgets of the `Category` and `Search` templates.
- Tweaked the style of some option fields in the plugin setting pages.
- Fixed a bug that some options with numbers could not set more than `1`.

#### 3.1.2 - 11/25/2015
- Changed the default value of the page type option in the Contextual Search widget.
- Tweaked the style of thumbnails of the Category and Search templates.

#### 3.1.1 - 08/09/2015
- Fixed a fatal error `Call to undefined function mb_detect_encoding()...` in the category select page on the server that does not install the multibite string extension.
- Fixed a bug that the `%price%` variable in the `Item Format` unit option was not functional.

#### 3.1.0 - 07/27/2015
- Added the ability to skip no thumbnail items.
- Added the `Button Type` unit option that lets the user add a product to the Amazon shopping cart.
- Added the ability to produce RSS and JSON feeds by unit id.
- Added the home and front page criteria for the `Available Page Types` option in the widget form.
- Tweaked the style of built-in templates.
- Tweaked the style of the credit link.
- Fixed PHP warnings related file path lengths set to the `PHP_MAXPATHLEN` constant.
- Fixed an issue that widget by unit could not be displayed in the front/home page, introduced in 3.0.5.

#### 3.0.5 - 07/14/2015
- Added visibility options to the widget by unit.
- Fixed a credit link that pointed the plugin directory which occurs when the user does not update the options to v3.

#### 3.0.4 - 07/07/2015
- Changed debug methods not to function when the site debug mode is off.
- Changed not to redirect the user to the listing table page after editing an auto-insert definition.
- Fixed strict standard PHP warnings.

#### 3.0.3 - 07/05/2015
- Fixed a bug in the contextual product widget that product filter options did not take effect.
- Fixed a bug that setting `0` for the `Max Image Size for Sub-images` option did not disable the images.
- Fixed a bug that templates inherited from v2 options were listed twice in the template listing table.

#### 3.0.2 - 07/04/2015
- Fixed an issue that templates were not properly loaded if the user did not upgrade the options to v3.

#### 3.0.1 - 06/30/2015
- Tweaked the formatting of a product element.
- Fixed an issue that translation files were not loaded in the front-end.
- Updated the base translation file.

#### 3 - 06/29/2015
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

#### 2.2.1 - 04/25/2015
- Fixed a compatibility issue with WordPress 4.2 in template listing page.
- Tweaked the styling of plugin template listing pages.

#### 2.2.0 - 04/24/2015
- Added the `Preview Unit` options including one that allows the user to set a custom url slug to the unit preview page.

#### 2.1.2 - 12/15/2014
- Added the `aal_action_loaded_plugin` action hook.
- Changed the timing of a localization function call.
- Tweaked the way to display product prices of the search unit type.
- Updated the Japanese translation file.

#### 2.1.1 - 12/04/2014
- Added the ability to automatically remove auto-insert items with no unit associated when a unit is removed.
- Changed the displayed product price of the `Search` unit type to use the discount price if there is an offered price.
- Optimized the performance.

#### 2.1.0 - 11/24/2014
- Added the India locale for the search unit type.
- Fixed an issue that when `Marketpalce` is selected in the `Category` option of the Select unit type, an error was shown.

#### 2.0.7 - 11/14/2014
- Added a error message when a template is not found.
- Added the `Merchant ID` option in the advanced options for the `Search` unit type.
- Fixed an issue that options were not saved with sites that enables object caching in the admin area.

#### 2.0.6 - 09/27/2014
- Made a small optimization on the background caching routines.
- Added the `%price%` variable for the search unit type.
- Fixed widget output formatting.

#### 2.0.5.4 - 06/07/2014
- Tweaked the styling to horizontally center the Not Found image of the `Search` template.
- Fixed the label of one of the advanced options of the search unit type.

#### 2.0.5.3 - 05/30/2014
- Fixed the `warning: Array to string conversion in...` message when product links were displayed with the auto-insert.

#### 2.0.5.2 - 05/27/2014
- Tweaked a form validation method to prevent no type option item from getting selected.
- Changed the file structure of including files.
- Tweaked the styling of the `Category` template to center the not found image.

#### 2.0.5.1 - 04/24/2014
- Changed the default option value of `Prevent Duplicates`.

#### 2.0.5 - 03/22/2014
- Fixed a bug that templates were deactivated when the plugin was deactivated and reactivated.
- Fixed an issue that the `the_content` filter was ignored in the plugin custom post type page.
- Added the `Caching Mode` option.
- Fixed a compatibility issue with a third-party plugin that hooks the `posts_where` filter.
- Fixed a PHP warning that occurs when a user with an insufficient access level logs in to the admin page.
- Added the class selectors representing unit ID and the label to the products container element.
- Fixed a bug that the `column` option was saved always as 1 for the first time.
- Refactored the code.

#### 2.0.4.1 - 03/03/2014
- Fixed a bug with the Auto-insert feature that a set page type for the `Where to Enable` section did not take effect.
- Added the ability to use an image when no product is found for the search unit type.
- Fixed a bug with the search unit type that results of the same API request but with a different locale were saved with the same name.
- Fixed a bug with the search unit type that same products were stored in the response array when more than 10 items were set.

#### 2.0.4 - 02/27/2014
- Improved the caching mechanism.
- Fixed a bug that caches for the search unit type renewed in the background were not saved properly with the right name.
- Fixed a bug that caches durations for the search unit type were not set properly.

#### 2.0.3.5 - 02/01/2014
- Fixed a possible security issue in the plugin admin pages.
- Tweaked the form text input length.
- Fixed an issue that `(recently, newly) tagged...` message was inserted in the title of product links of Tag unit type.
- Fixed the warning: `Strict standards: Declaration of ... should be compatible with ...` when displaying the output of the tag unit type.

#### 2.0.3.4 - 01/25/2014
- Fixed: a bug that the Settings link of the plugin in the plugin listing table did not point to the Setting page.
- Fixed: a bug that caused a broken output in the search unit type due to an undefined index in an array when the authentication keys are not properly set in some PHP versions.
- Added: the `Number of Items` option for the Similarity Look-up search unit type.
- Fixed the warning: `Strict standards: Declaration of ... should be compatible with ...`.
- Updated: the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.7.2.

#### 2.0.3.3 - 01/17/2014
- Fixed: a bug that the `Single Post` check option did not take effect in the Page post type.
- Updated: the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.7.1.
- Fixed: a bug that taxonomy check list boxes were gone since v2.0.2.

#### 2.0.3.2 - 01/07/2014
- Fixed: an issue that an image element was inserted when no image of the product was found in the search unit type.
- Fixed: undefined index warnings with the search unit types.
- Fixed: a bug in the advanced search option that the Title option did not take effect.

#### 2.0.3.1 - 01/03/2014
- Fixed: an issue that the stylesheet urls included the characters, `/./`, which may have caused a problem for third-party plugins that minifies CSS.
- Fixed: an issue that some outputs broke html tags due to unescaped characters.
- Fixed: a bug that search units did not return results with the correct item count.

#### 2.0.3 - 12/20/2013
- Added: the `Similarity Look-up` unit type which allows to display similar products by ASIN.
- Changed: the message "Could not identify the unit type." to be inserted as an HTML comment.

#### 2.0.2 - 12/17/2013
- Fixed: an issue that too many database queries were performed in the plugin setting pages as of v2.0.1 due to disabling object caching.
- Added: the `Item Look-up` unit type which allows to pick one or more items by item ID.
- Fixed: the method handling Amazon Product Advertising API to treat invalid XML returned by the API as an error.
- Updated: the information regarding obtaining an Amazon access key since the linked documentation page has been closed.
- Added: a help page and some information pages in the plugin admin pages.
- Tweaked: the style of the option elements in the unit definition page.
- Updated: the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.6.

#### 2.0.1 - 11/30/2013
- Improved: the method to load template stylesheets.
- Disabled: object caching in the plugin pages and the options.php (the page that stores the settings) in order to avoid conflicts with caching plugins.
- Updated: the [Admin Page Framework](http://wordpress.org/plugins/admin-page-framework/) library to v2.1.4.
- Added: the `aal_filter_unit_output` filter.
- Fixed: a bug in form filed layout that an enclosing tag was missing.
- Fixed: the warning, Creating default object from empty value.

#### 2 - 11/01/2013
- Changed: to ask user permission to display ads in the settings page and the support rate when the plugin is activated for the first time.
- Changed: the unit label option to a taxonomy.
- Changed: the url cloak to use less characters (moved to the link style option).
- Added: the ability to limit auto-static-insertion by taxonomy and post type.
- Added: the ability to limit auto-insertion by taxonomy, post type, and post ID.
- Added: the ability to prevent duplicated products from being displayed throughout the page load.
- Added: the ability to create units by tag.
- Added: the Brazil and Mexico locales.
- Added: the case sensitive option for the black and white list options.
- Added: the white list option.
- Added: the auto-insert feature. Accordingly, the Where to Insert option and Where to Disable option were deprecated.
- Added: the tag unit type.
- Added: the search unit type.
- Added: the template system. Accordingly, the Container, Item, Image format options were deprecated.
- Renewed: (***Breaking Changes***)the entire option structure. Accordingly, after running the option importer script, which is displayed as a link in the admin message, the insert position options need to be reconfigured.
- Renewed: the background-caching system.
- Renewed: the icon.
- Renewed: the admin interface.

#### 1.2.6 - 09/01/2013
- Added: the ability to use SSL images if the site runs on SSL.
- Added: the Indian locale.

#### 1.2.5.2 - 03/08/2013
- Added: an error message for servers which does not have the DOM XML extension which appears upon plugin activation.
- Disabled: completely DOM related errors.
- Tweaked: some code for faster loading in the admin settings pages.
- Changed: the option object to be global for plugin extensions.
- Added: filters for plugin extensions.

#### 1.2.5.1 - 02/23/2013
- Fixed: the warning, Undefined variable: oAAL.

#### 1.2.5 - 02/23/2013
- Added: the version number to appear in the footer of the plugin setting pages.
- Added: the ability to remove all used option values of the plugin upon plugin deactivation, which can be set in General Settings.
- Fixed: the warning message, undefined index, save, which occurred in the debug mode when posting a new post.

#### 1.2.4 - 02/22/2013
- Added: the "Access Right to Setting Page" option in the General Settings page that sets the access level for the admin page of the plugin.

#### 1.2.3 - 02/21/2013
- Fixed: a bug that title sorting was not accurately performed when the title contained HTML tags or encoded differently from other titles.
- Changed: the name of the sort option, Title, to Title Ascending.
- Added: the Title Descending sort order option.

#### 1.2.2 - 02/21/2013
- Added: the Debug Log option and page.
- Changed: the default value of the Prefetch Category Lists option to Off.
- Fixed: the Japanese localization file name.
- Disabled: the warning message to occur:  Warning: DOMElement::setAttribute() [domelement.setattribute]: string is not in UTF-8.

#### 1.2.1 - 02/18/2013
- Fixed: a bug that changing unit option values did not take effect when the Proceed button was pressed via the Manage Unit page.
- Changed: product links to be disabled on the front page ( not only on the home page ) if the "Disable on the home page" option is enabled.
- Fixed: warning messages, undefined index, which appeared in the debug mode when the Delete Selected Units button was pressed.

#### 1.2.0 - 02/12/2013
- Fixed: some lines of code which caused warning messages when the WordPress debug mode was turned on.
- Added: the ability to clean up remaining once-off events upon plugin deactivation.
- Added: the option to disable product links per unit basis on certain pages including the home page.
- Fixed: a bug that url cloak had not been working as of 1.1.9.

#### 1.1.9 - 01/24/2013
- Updated: the Japanese localization file.
- Added: the Above and Below Post on Publish check boxes for the auto insert option.
- Fixed: the styles of the Manage Unit table by loading the stylesheet in all the setting tab pages of the plugin.
- Changed: the file name to amazon-auto-links.php from amazonautolinks.php
- Changed: the text domain to amazon-auto-links from amazonautolinks.
- Changed: the timing of registering classes to be extension-friendly.
- Changed: code formatting to extend the plugin more easily.

#### 1.1.8 - 01/19/2013
- Added: the icons for the Operation column of the Manage Unit table .
- Adjusted: the styles of the Manage Unit page in the admin page.
- Fixed: an issue that R18 categories requiring additional redirects could not be browsed in the category selection page.

#### 1.1.7 - 01/16/2013
- Fixed: a bug that caches were not cleared with database tables that have a custom prefix.
- Fixed: a bug that the Prefetch Category Lists option had not take effect since v1.1.3 removing the iframe preview page.

#### 1.1.6 - 01/14/2013
- Fixed: a minor bug that an error message did not appear properly when category links cannot be retrieved.
- Added: Blacklist by title and description set in the General Settings page.

#### 1.1.5 - 12/14/2012
- Changed: to force the unit output to close any unclosed HTML tags.
- Fixed: a bug that the plugin requirement check did not work as of v1.1.3.
- Improved: the response speed when first accessing the setting page.

#### 1.1.4 - 12/13/2012
- Fixed: a bug that shortcode did not work as of v1.1.3.

#### 1.1.3 - 12/13/2012
- Supported: WordPress 3.5
- Changed: the preview page not to use iframe so that "Could not locate admin.php" error would not occur.
- Fixed: a bug that the style was not loaded in one of the tab page in the plugin setting page.
- Fixed: a bug that the arrow images which indicate where to click did not appear in the category selection page.
- Added: the ability to delete transients for category caches when the pre-fetch option is set to off.
- Added: the unit memory usage in the unit preview page.
- Added: the ability to remove transients when the plug-in is deactivated.

#### 1.1.2 - 11/11/2012
- Fixed: a bug which displayed the plugin memory usage in the page footer.

#### 1.1.1 - 11/02/2012
- Added: the prefetch category links option, which helps in some servers which sets a low value to the max simultaneous database connections.

#### 1.1.0 - 10/26/2012
- Fixed: a bug that url cloak option was forced to be unchecked in the option page.
- Fixed: a bug that credit option was forced to be checked in the option page.
- Fixed: an issue that encryption did not work on servers which disables the mcrypt extension.
- Fixed: an issue that some form elements of the admin page did not appear on servers with the short_open_tag setting disabled.
- Fixed: a bug that the AmazonAutoLinks() function did not retrieve the correct unit ID.

#### 1.0.9 - 10/06/2012
- Added: the link cloaking feature.

#### 1.0.8 - 10/03/2012
- Fixed: a bug that shortcode failed to display the unit saved in version 1.0.7 or later.
- Added: the title length option.
- Added: the link style option.
- Added: the credit insert option.

#### 1.0.7 - 10/02/2012
- Fixed: an issue that the widget got un-associated when the unit label got changed.
- Fixed: an issue that category caches were saved with the wrong name which resulted on not using the cache when available.
- Fixed: an issue that the format of the img tag got changed when the validation failed when setting up a unit.
- Added: a donation link in the plugin listing page.

#### 1.0.6 - 09/24/2012
- Added: the rel attribute, rel="nofollow", in the a tag of product links.
- Re-added: the widget which enables to add units easily on the sidebar.

#### 1.0.5 - 09/20/2012
- Improved: the caching method. Now the caches of links are renewed in the background.

#### 1.0.4 - 09/18/2012
- Added: the settings link in the plugin list page of the administration panel.
- Improved: the page load speed in the category selection page by reducing the cache elements.

#### 1.0.3 - 09/16/2012
- Fixed: an issue that in below PHP v5.2.4, the link descriptions could not be retrieved properly; as a result, the edit and view page links were broken.
- Improved: the page load speed in the category selection page with caches.
- Removed: the widget functionality since it produced a blank page in some systems and the cause and solution could not be found.

#### 1.0.2 - 09/12/2012
- Fixed: an issue that form buttons did not appear in the category selection page in WordPress version 3.1x or earlier.

#### 1.0.1 - 09/10/2012
- Added: the Widget option.

#### 1.0.0 - 09/10/2012
- Initial Release