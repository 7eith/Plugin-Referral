<?php
/* **************************************************************************** *
*
* ReferralController.php
*
* by: Snkh <dev@snkh.me>
*
* Created: 28/05/2019 19:20 by Snkh
* Updated: 06/08/2019 03:43 by Snkh
* 
* Under private Copyright, all rights reserved to Snkh.
*
**************************************************************************** */

class ReferralController extends AppController { 
  
  /** 
   * VIEW :: Admin Index
   */

  public function admin_index() {
    $this->layout = 'admin';
    $this->set('title_for_layout', $this->Lang->get('REFERRAL__LAYOUT_INDEX'));
    $this->loadModel('Referral.Referral');
    
    $usersToFind = array();
    $referrals = $this->Referral->find('all');
    
    foreach ($referrals as $key => $value) {
      $usersToFind[] = $value['Referral']['user_id'];
    }
    
    $usersByID = array();
    $findUsers = $this->User->find('all', array('conditions' => array('id' => $usersToFind)));
    foreach ($findUsers as $key => $value) {
      $usersByID[$value['User']['id']] = $value['User']['pseudo'];
    }
    
    $this->set(compact('referrals', 'usersByID'));   
  }

  /**
   * GET :: Redirecting to /r/:user
   */

  public function register() { 
    if($this->isConnected) 
      $this->redirect('/');

    $this->set('title_for_layout', $this->Lang->get('REFERRAL__REGISTER_TITLE'));
    $this->set('referral', $this->request->params['user_referral']);
  }

  /**
   * AJAX :: Confirm referral
   */

  public function confirm() { 
    $this->autoRender = false;
    $this->response->type('json');
    $confirmEmailIsNeeded = ($this->Configuration->getKey('confirm_mail_signup') && $this->Configuration->getKey('confirm_mail_signup_block'));
      
    // bad request
    if (!$this->request->is('Post') || !$this->isConnected) 
      return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));

    // double check, sur to not use curl
    if ($confirmEmailIsNeeded && !empty($this->User->getKey('confirmed')))
      return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__ERROR_EMAIL'))));

    $this->loadModel('User');
    $this->loadModel('Referral.Referral');

    // referral table
    $referral = $this->Referral->find('first', array('conditions' => array('user_id' => $this->User->getKey('id'))));

    if ($referral['Referral']['pay'] == true)
      return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__ERROR_ALREADY_CONFIRMED'))));

    // get user referred
    $referred = $this->User->find('first', array('conditions' => array('id' => $referral['Referral']['referral_id'])));

    // adding 10 to referred
    $this->User->read(null, $referred['User']['id']);
    $this->User->set(array('money' => $referred['User']['money'] + 10));
    $this->User->save();

    // set has already pay
    $this->Referral->read(null, $referral['Referral']['id']);
    $this->Referral->set(array('pay' => true));
    $this->Referral->save();

    return $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('REFERRAL__CONFIRMED') . $this->User->getKey('pseudo'))));
  }
  
  /** 
   * AJAX :: Registering user with Referral 
   */

  function ajax_register() {
    $this->autoRender = false;
    $this->response->type('json');
    
    if ($this->request->is('Post')) {
      $conditionsChecked = !empty($this->request->data['condition']) || !$this->Configuration->getKey('condition');
      
      if (!empty($this->request->data['pseudo']) && !empty($this->request->data['password']) && $conditionsChecked && !empty($this->request->data['password_confirmation']) && !empty($this->request->data['email'])) {
        if ($this->Configuration->getKey('captcha_type') == "2") { 
          $validCaptcha = $this->Util->isValidReCaptcha($this->request->data['recaptcha'], $this->Util->getIP(), $this->Configuration->getKey('captcha_google_secret'));
        } else {
          $captcha = $this->Session->read('captcha_code');
          $validCaptcha = (!empty($captcha) && $captcha == $this->request->data['captcha']);
        }
        
        if ($validCaptcha) { 
          $this->loadModel('User');
          $isValid = $this->User->validRegister($this->request->data, $this->Util);
          if ($isValid === true) { 
            $eventData = $this->request->data;
            $eventData['password'] = $this->Util->password($eventData['password'], $eventData['pseudo']);
            $event = new CakeEvent('beforeRegister', $this, array('data' => $eventData));
            $this->getEventManager()->dispatch($event);
            if ($event->isStopped()) {
              return $event->result;
            }
            
            /**
            * Check
            */
            
            if(!empty($this->request->data['referral'])) {
              $this->loadModel('Referral.Referral');
              
              // check IP 
              if($this->Referral->find('count', ['conditions' => ['ip' => $this->request->clientIp()]]) == 0) {
                $user = $this->User->find('first', array('conditions' => array('pseudo' => $this->request->data['referral'])));

                if($this->request->data['pseudo'] == $this->request->data['referral'])
                  return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__ERROR_YOURSELF'))));
                
                if($user != null) {
                  $userSession = $this->User->register($this->request->data, $this->Util);
                  
                  $this->Referral->read(null, null);
                  $this->Referral->set(array('ip' => $this->request->clientIp()));
                  $this->Referral->set(array('user_id' => $userSession));
                  $this->Referral->set(array('referral_id' => $user['User']['id']));
                  
                  $this->Referral->save();
                  
                  $this->User->save($user);
                  
                } else { 
                  return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__USER_NOT_EXIST'))));
                }
              } else {
                return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__IP_ALREADY_USED'))));
              }
            } else { // no referral 
              $userSession = $this->User->register($this->request->data, $this->Util);
            }
            
            if ($this->Configuration->getKey('confirm_mail_signup')) {
              $confirmCode = substr(md5(uniqid()), 0, 12);
              $emailMsg = $this->Lang->get('EMAIL__CONTENT_CONFIRM_MAIL', array(
                '{LINK}' => Router::url('/user/confirm/', true) . $confirmCode,
                '{IP}' => $this->Util->getIP(),
                '{USERNAME}' => $this->request->data['pseudo'],
                '{DATE}' => $this->Lang->date(date('Y-m-d H:i:s'))
              ));
              $email = $this->Util->prepareMail(
                $this->request->data['email'],
                $this->Lang->get('EMAIL__TITLE_CONFIRM_MAIL'),
                $emailMsg
                )->sendMail(); // internal error if no mail send.. but user registered
                if ($email) {
                  $this->User->read(null, $this->User->getLastInsertID());
                  $this->User->set(array('confirmed' => $confirmCode));
                  $this->User->save();
                }
              }
              
              if (!$this->Configuration->getKey('confirm_mail_signup_block')) { 
                $this->Session->write('user', $userSession);
                $event = new CakeEvent('onLogin', $this, array('user' => $this->User->getAllFromCurrentUser(), 'register' => true));
                $this->getEventManager()->dispatch($event);
                
                if ($event->isStopped()) {
                  return $event->result;
                }
              }
              
              $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('USER__REGISTER_SUCCESS'))));
            } else { 
              $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get($isValid))));
            }
          } else {
            $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('FORM__INVALID_CAPTCHA'))));
          }
        } else {
          $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
        }
      } else {
        $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
      }
    }
}