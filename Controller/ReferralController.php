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

   public function admin_index(){
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
 }