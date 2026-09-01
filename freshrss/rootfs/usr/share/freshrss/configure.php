<?php

/*
 * Applies the settings this app owns to FreshRSS' own configuration.
 *
 * FreshRSS ships a Content-Security-Policy of frame-ancestors 'none', which no
 * iframe survives, same origin or not. Ingress renders the app in one, so it
 * has to be widened, and it stays as narrow as it can be: 'self' is the Home
 * Assistant page the iframe sits on, since Ingress serves the app from below
 * the root of that very same origin.
 *
 * auth_type follows the app's `ingress_auto_login` option and default_user its
 * `username` option, either of which may be changed between restarts, so both
 * are re-applied on every start rather than only at install time. Keeping
 * default_user in step matters beyond bookkeeping: FreshRSS grants
 * administrator rights to whoever it names.
 *
 * Everything else is left to be managed from within FreshRSS. The values are
 * read from the environment rather than from arguments, so that a run of this
 * script cannot be told to configure something it does not own.
 */

require_once '/var/www/freshrss/cli/_cli.php';

$authType = getenv('FRESHRSS_AUTH_TYPE');
$username = getenv('FRESHRSS_USER');

if (!in_array($authType, ['form', 'http_auth', 'none'], true)) {
    fail('Invalid authentication type: ' . var_export($authType, true));
}

if (!is_string($username) || !FreshRSS_user_Controller::userExists($username)) {
    fail('Unknown user: ' . var_export($username, true));
}

$conf = FreshRSS_Context::systemConf();
$changed = false;

if ($conf->auth_type !== $authType) {
    $conf->auth_type = $authType;
    $changed = true;
}

if ($conf->default_user !== $username) {
    $conf->default_user = $username;
    $changed = true;
}

if ($conf->attributeString('csp.frame-ancestors') !== "'self'") {
    $conf->_attribute('csp.frame-ancestors', "'self'");
    $changed = true;
}

if ($changed && !$conf->save()) {
    fail('FreshRSS could not write its configuration file!');
}

exit(0);
