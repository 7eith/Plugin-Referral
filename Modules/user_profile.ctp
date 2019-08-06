<?php
/* **************************************************************************** *
 *
 * user_profile.ctp
 *
 * by: Snkh <dev@snkh.me>
 *
 * Created: 30/05/2019 20:27 by Snkh
 * Updated: 06/08/2019 03:03 by Snkh
 * 
 * Under private Copyright, all rights reserved to Snkh.
 *
 **************************************************************************** */
?>

<div class="panel panel-default">
    <div class="panel-heading" id="panel-head">
        <span class="white"><?= $Lang->get("REFERRAL__PROFILE_TITLE") ?></span>
    </div>
    
    <div class="panel-body" style="padding: 30px 20px;">
        <form>
            <div class="row" style="padding-left: 20px;">
            <?= $Lang->get("REFERRAL__LINK") ?> <br><br>
                <div class="alert alert-info"><a class="close" data-dismiss="alert">×</a>
                    <a style="color:white;" href="https://qataria.fr/r/<?= $user['pseudo'] ?>">
                        <center>https://qataria.fr/r/<?= $user['pseudo'] ?></center>
                    </a> 
                </div>
            </div>
        </form>
        <form method="post" data-ajax="true" action="<?= $this->Html->url(array('controller' => 'referral', 'action' => 'confirm')) ?>">
            <div class="text-right">
                <button type="submit" class="btn btn-primary btn-large"><?= $Lang->get("REFERRAL__PROFILE_CONFIRM") ?></button>
            </div>
        </form>
    </div>
</div>
