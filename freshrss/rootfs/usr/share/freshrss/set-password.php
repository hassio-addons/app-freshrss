<?php

/*
 * Sets the password of a FreshRSS user.
 *
 * FreshRSS' own ./cli/update-user.php takes the password as a command line
 * argument, which leaves it in the process list for as long as it runs. This
 * calls the very same controller that script calls, with both the user and the
 * password read from the environment instead.
 */

require_once '/var/www/freshrss/cli/_cli.php';

$username = cliInitUser(getenv('FRESHRSS_USER') ?: '');
$password = getenv('FRESHRSS_PASSWORD');

if (!is_string($password) || $password === '') {
    fail('No password provided');
}

// The null leaves the address on file alone; only the password is touched.
if (!FreshRSS_user_Controller::updateUser($username, null, $password)) {
    fail('FreshRSS could not set the password of ' . $username . '!');
}

exit(0);
