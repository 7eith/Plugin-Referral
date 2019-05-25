<?php
/* **************************************************************************** *
 *
 * schema.php
 *
 * by: Snkh <dev@snkh.me>
 *
 * Created: 24/03/2019 18:48 by Snkh
 * Under private Copyright, all rights reserved to Snkh.
 *
 **************************************************************************** */

class ReferralAppSchema extends CakeSchema {

    public $file = 'schema.php';

    public function before($event = array()) {
        return true;
    }

    public function after($event = array()) {}

    public $referral__tables = array(

    );
}
