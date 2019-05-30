<?php
/* **************************************************************************** *
 *
 * routes.php
 *
 * by: Snkh <dev@snkh.me>
 *
 * Created: 26/05/2019 00:05 by Snkh
 * Under private Copyright, all rights reserved to Snkh.
 *
 **************************************************************************** */

Router::connect('/admin/referral',            array('controller' => 'referral', 'action' => 'index',  'plugin' => 'referral', 'admin' => true));