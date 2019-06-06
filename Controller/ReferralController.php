<?php
/* **************************************************************************** *
*
* ReferralController.php
*
* by: Snkh <dev@snkh.me>
*
* Created: 28/05/2019 19:20 by Snkh
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
  * AJAX set Referral to Model :: User
  */
  
  public function setReferral() { 
    if(!$this->isConnected) 
      throw new ForbiddenException();
    
    if(!$this->request->is('ajax')) 
      return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
    
    $this->loadModel('Referral.Referral');
    $this->response->type('json');
    $this->autoRender = false;

    // IP ?? => better to hide reason? 
    if($this->Referral->find('count', ['conditions' => ['ip' => $this->request->clientIp()]]) != 0) 
      return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__IP_ALREADY_USED'))));
    
    // has referral anyone 
    if($this->Referral->find('count', ['conditions' => ['user_id' => $this->User->getKey('id')]]) != 0) 
      return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__HAS_REFERRAL'))));
    
    if (!empty($this->request->data['referral'])) {
      $user = $this->User->find('first', array('conditions' => array('pseudo' => $this->request->data['referral'])));
      
      if($user == null)
        return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__USER_NOT_EXIST'))));
      
      $this->Referral->read(null, null);
      $this->Referral->set($this->request->data);
      $this->Referral->set(array('ip' => $this->request->clientIp()));
      $this->Referral->set(array('user_id' => $this->User->getKey('id')));
      $this->Referral->set(array('referral_id' => $user['User']['id']));
      
      $this->Referral->save();

      // adding 10 of money
      $user['User']['money'] = $user['User']['money'] + 10; 
      $this->User->save($user);
      
      $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('REFERRAL__OK') . $user['User']['pseudo'])));
    
    } else { 
      $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
    }
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
                  
                  $user['User']['money'] = $user['User']['money'] + 10; 
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
                )->sendMail();
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