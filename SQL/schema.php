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

    public $referral__datas = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
        'referral_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
        'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
        'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
    );
}
