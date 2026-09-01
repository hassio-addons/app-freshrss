<?php

/*
 * The configuration every newly created FreshRSS account starts from.
 *
 * FreshRSS includes this file while creating an account and merges what it
 * returns underneath the settings it was asked for. The account being created
 * does not exist yet at that point, so listUsers() still describes the state
 * before it.
 *
 * The first person through the door administers FreshRSS. With Ingress signing
 * people in automatically, accounts appear on their own as Home Assistant users
 * open the app, and somebody has to be able to reach the administration pages.
 * That first arrival is the only one this app can name without being told,
 * since Home Assistant says who is asking but not whether they administer Home
 * Assistant itself.
 *
 * The account the app sets up for itself does not count towards being first.
 * It exists so that FreshRSS' default_user names something real, and when
 * Ingress signs people in, nobody ever logs into it.
 *
 * This file is copied into the data directory on every start, so editing it
 * there does not survive a restart.
 */

$existing = FreshRSS_user_Controller::listUsers();

if (FreshRSS_Context::hasSystemConf()) {
    $existing = array_diff($existing, [FreshRSS_Context::systemConf()->default_user]);
}

return [
    'is_admin' => $existing === [],
];
