<?php
/* **************************************************************************** *
 *
 * user_profile.ctp
 *
 * by: Snkh <dev@snkh.me>
 *
 * Created: 30/05/2019 20:27 by Snkh
 * Under private Copyright, all rights reserved to Snkh.
 *
 **************************************************************************** */
?>

<div class="panel panel-default">
    <div class="panel-heading" id="panel-head">
        <span class="white"><?= $Lang->get("REFERRAL__PROFILE_TITLE") ?></span>
    </div>
    
    <div class="panel-body" style="padding: 30px 20px;">
        <form method="post" data-ajax="true" action="<?= $this->Html->url(array('controller' => 'referral', 'action' => 'setReferral')) ?>">
            <div class="row" style="padding-left: 20px;">

                <div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><b><?= $Lang->get('REFERRAL__WARNING_SET') ?></b> </div>
                <div class="form-group">
                    <label><?= $Lang->get("REFERRAL__PROFILE_FORM") ?></label>
                    <input type="text" class="form-control" name="referral">
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary btn-large"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
        </form>
    </div>
</div>