<?php
declare(strict_types=1);

/*
 * Points FreshRSS at this app's persistent storage.
 *
 * Everything FreshRSS keeps between runs lives below DATA_PATH: its own
 * configuration, the configuration and SQLite database of every user, the feed
 * cache and the favicons. It is handed a directory of its own inside /data,
 * rather than /data itself, so that it never meets the app's options.json.
 *
 * Extensions installed from within FreshRSS are put alongside it, instead of
 * next to the application, so that they survive an update of this app. That
 * replaces the whole application directory, and would take any extension
 * stored there with it.
 *
 * FreshRSS reads this file from constants.php before it works out its own
 * defaults. It also honours a DATA_PATH environment variable at that point,
 * and would redefine the constant if one were set, so nothing in this app
 * sets that variable.
 */

define('DATA_PATH', '/data/freshrss');
define('THIRDPARTY_EXTENSIONS_PATH', DATA_PATH . '/extensions');
