<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Available variables.
 *
 * @var array $aOptions the plugin options
 * @var array $aProducts the fetched product links
 * @var array $aArguments the user defined unit arguments such as image size and count etc.
 */
echo json_encode( $aProducts );