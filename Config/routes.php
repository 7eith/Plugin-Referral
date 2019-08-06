<?php
/* **************************************************************************** *
 *
 * routes.php
 *
 * by: Snkh <dev@snkh.me>
 *
 * Created: 26/05/2019 00:05 by Snkh
 * Updated: 06/08/2019 03:03 by Snkh
 * 
 * Under private Copyright, all rights reserved to Snkh.
 *
 **************************************************************************** */

Router::connect('/r/:user_referral',          array('controller' => 'referral', 'action' => 'register',  'plugin' => 'referral'));
Router::connect('/referral/register',         array('controller' => 'referral', 'action' => 'ajax_register',  'plugin' => 'referral'));
Router::connect('/referral/confirm',          array('controller' => 'referral', 'action' => 'confirm',  'plugin' => 'referral'));

/**
 * Admin 
 */

Router::connect('/admin/referral',            array('controller' => 'referral', 'action' => 'index',  'plugin' => 'referral', 'admin' => true));

