<?php
/* **************************************************************************** *
 *
 * Referral.php
 *
 * by: Snkh <dev@snkh.me>
 *
 * Created: 28/05/2019 19:25 by Snkh
 * Under private Copyright, all rights reserved to Snkh.
 *
 **************************************************************************** */

class Referral extends AppModel {

    public $useTable = "referral__datas";
    
    public $belongsTo = array('User');

}
