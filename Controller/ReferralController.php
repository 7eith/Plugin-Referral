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
      return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__HAS_REFERRAL'))));
    
    // has referral anyone 
    if($this->Referral->find('count', ['conditions' => ['user_id' => $this->User->getKey('id')]]) != 0) 
      return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('REFERRAL__IP_ALREADY_USED'))));
    
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
      
      $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('REFERRAL__OK') . $user['User']['pseudo'])));
    
    } else { 
      $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
    }
  }
}