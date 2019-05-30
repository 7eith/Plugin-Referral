<?php
/* **************************************************************************** *
 *
 * admin_index.ctp
 *
 * by: Snkh <dev@snkh.me>
 *
 * Created: 28/05/2019 21:08 by Snkh
 * Under private Copyright, all rights reserved to Snkh.
 *
 **************************************************************************** */
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $Lang->get('REFERRAL__ADMIN_TITLE') ?></h3>
                </div>

                <div class="box-body table-responsive">
                    <table class="table table-bordered dataTable">
                        <thead>
                            <tr>
                                <th><?= $Lang->get('REFERRAL__ADMIN_USER') ?></th>
                                <th><?= $Lang->get('REFERRAL__ADMIN_IP') ?></th>
                                <th><?= $Lang->get('REFERRAL__ADMIN_REFERRAL') ?></th>
                                <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($referrals)) { ?>
                                <?php foreach ($referrals as $key => $value) { ?>
                                    <?php if($value['Referral']['user_id'] != "0") { ?>
                                        <tr>
                                            <td><?= $usersByID[$value['Referral']['user_id']] ?></td>
                                            <td><?= $value['Referral']['ip'] ?></td>
                                            <td><?= $usersByID[$value['Referral']['referral_id']] ?></td>
                                            <td><?= $Lang->date($value['Referral']['created']) ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>