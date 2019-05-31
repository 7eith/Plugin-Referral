<?php
/* **************************************************************************** *
 *
 * register.ctp
 *
 * by: Snkh <dev@snkh.me>
 *
 * Created: 31/05/2019 01:08 by Snkh
 * Under private Copyright, all rights reserved to Snkh.
 *
 **************************************************************************** */
?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h1><?= $Lang->get('REFERRAL__REGISTER_TITLE') ?></h1>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <form method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => 'referral', 'admin' => false, 'controller' => 'referral', 'action' => 'ajax_register')) ?>" data-redirect-url="?">
                <div class="ajax-msg"></div>
                
                <div class="form-group">
                    <h5><?= $Lang->get('USER__USERNAME') ?></h5>
                    <input type="text" class="form-control" name="pseudo" placeholder="<?= $Lang->get('USER__USERNAME_LABEL') ?>">
                </div>
                
                <div class="form-group">
                    <h5><?= $Lang->get('USER__PASSWORD') ?></h5>
                    <input type="password" class="form-control" name="password" placeholder="<?= $Lang->get('USER__PASSWORD_LABEL') ?>">
                </div>
                
                <div class="form-group">
                    <h5><?= $Lang->get('USER__PASSWORD_CONFIRM') ?></h5>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="<?= $Lang->get('USER__PASSWORD_CONFIRM_LABEL') ?>">
                </div>
                
                <div class="form-group">
                    <h5><?= $Lang->get('USER__EMAIL') ?> </h5>
                    <input type="email" class="form-control" name="email" placeholder="<?= $Lang->get('USER__EMAIL_LABEL') ?>">
                </div>
                
                <div class="form-group">
                    <h5><?= $Lang->get('REFERRAL__REGISTER_REFERRAL') ?> </h5>
                    <input type="text" class="form-control" name="referral" value="<?= $referral ?>">
                </div>
                
                <?php if($reCaptcha['type'] == "google") { ?>
                    <script src='https://www.google.com/recaptcha/api.js'></script>

                    <div class="form-group">
                        <h5><?= $Lang->get('FORM__CAPTCHA') ?></h5>
                        <div class="g-recaptcha" data-sitekey="<?= $reCaptcha['siteKey'] ?>"></div>
                    </div>
                    
                <?php } else { ?>
                        
                    <div class="form-group">
                        <h5><?= $Lang->get('FORM__CAPTCHA') ?></h5>
                        <?php
                        echo $this->Html->image(array('controller' => 'user', 'action' => 'get_captcha', 'plugin' => false, 'admin' => false), array('plugin' => false, 'admin' => false, 'id' => 'captcha_image'));
                        echo $this->Html->link($Lang->get('FORM__RELOAD_CAPTCHA'), 'javascript:void(0);',array('id' => 'reload'));
                        ?>
                    </div>
                        
                    <div class="form-group">
                        <input type="text" class="form-control" name="captcha" id="inputPassword3" placeholder="<?= $Lang->get('FORM__CAPTCHA_LABEL') ?>">
                        </div>
                <?php } ?>
                        
                <?php if (!empty($condition)) { ?>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="condition">
                                <?=$Lang->get('USER__CONDITION_1')?> <a href="<?= $condition ?>"> <?= $Lang->get('USER__CONDITION_2')?></a>
                            </label>
                        </div>
                    </div>
                <?php } ?>
                <button type="submit" class="btn btn-primary btn-block"><?= $Lang->get('USER__REGISTER') ?></button>
            </form>
        </div>
    </div>
</div>