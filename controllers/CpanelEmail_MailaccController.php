<?php
namespace Craft;

class CpanelEmail_MailaccController extends BaseController
{

  private $xmlapi = null;
  private $settings = null;
  private $account = null;

  public function __construct() {

      $this->settings = craft()->plugins->getPlugin('cpanelemail')->getSettings();
      $ip = $this->settings['cpanel_ip'];
      $username = $this->settings['cpanel_username'];
      $password = $this->settings['cpanel_password'];
	    $port = $this->settings['cpanel_port'];
      $this->account = $this->settings['cpanel_account'];
      if (!$this->xmlapi) {
          $xmlapi = new \xmlapi($ip);
          $xmlapi->set_port($port);
          $xmlapi->password_auth($username, $password);
          $xmlapi->set_output("array");
          $this->xmlapi = $xmlapi;
      }
  }

  public function actionAdd() {
      $currentUser = craft()->userSession->getUser();
      if ($currentUser->can('permEmailAcc')) {
          $email = trim($_POST['email']);
          $domain = trim($_POST['domain']);
          $pwd1 = trim($_POST['pwd1']);
          $pwd2 = trim($_POST['pwd2']);
          $quota = trim($_POST['quota']);
          $quotavalue = $_POST['quotavalue'];

          $e = null;
          $ok = true;

          if (($ok) && ($pwd1 != $pwd2)) {
              $ok = false;
              $e = craft::t('Password mismatch');
          }
          if (($ok) && ($quota == 'fixed') && ((!is_numeric($quotavalue)) || ($quotavalue < 1))) {
              $ok = false;
              $e = craft::t('Quota must be a positive integer');
          }

          if ($ok) {

              if ($quota == 'unlimited') {
                  $quotavalue = 0;
              }

              $options = array(
                  'domain' => $domain,
                  'email' => $email,
                  'password' => $pwd1,
                  'quota' => $quotavalue,
              );

              $addpop = $this->xmlapi->api2_query($this->account, 'Email', 'addpop', $options);

              if (!$addpop)
              {
                craft()->userSession->setError(craft::t('An error occured') . ' : XMLAPI');
              }
              elseif (isset($addpop['event']['result']) && ($addpop['event']['result'] == 0)) {
                  craft()->userSession->setError($addpop['event']['reason']);
              } else {
                  craft()->userSession->setNotice(craft::t('Email account added successfully'));
              }
          } else {
              craft()->userSession->setError(craft::t('An error occured') . ' : ' . $e);
          }
      } else {
          // No permissions!
          craft()->userSession->setError(craft::t('No permission!'));
          throw new Exception(Craft::t('No permission!'));
      }
      $this->redirect($_POST['redirect']);
  }

  public function actionQuota() {
      $currentUser = craft()->userSession->getUser();
      if ($currentUser->can('permEmailAcc')) {
          $pieces = explode('@', trim($_POST['email']));

          $email = $pieces[0];
          $domain = $pieces[1];

          $quota = trim($_POST['quota']);
          $quotavalue = $_POST['quotavalue'];

          $ok = true;

          if (($ok) && ($quota == 'fixed') && ($quotavalue < 1)) {
              $ok = false;
              $e = craft::t('Quota must be a positive integer');
          }

          if ($ok) {

              if ($quota == 'unlimited') {
                  $quotavalue = 0;
              }

              $options = array(
                  'domain' => $domain,
                  'email' => $email,
                  'quota' => $quotavalue,
              );

              $editquota = $this->xmlapi->api2_query($this->account, 'Email', 'editquota', $options); // Call the function.

              if (isset($editquota['event']['result']) && ($editquota['event']['result'] == 0)) {
                  $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $editquota['data']['reason']));
                  craft()->userSession->setError($error);
              } else {
                  craft()->userSession->setNotice(craft::t('Quota modified added successfully'));
              }
          } else {
              craft()->userSession->setError(craft::t('An error occured') . ' : ' . $e);
          }
      } else {
          // No permissions!
          craft()->userSession->setError(craft::t('No permission!'));
          throw new Exception(Craft::t('No permission!'));
      }
      $this->redirect($_POST['redirect']);
  }

  public function actionPassword() {
      $currentUser = craft()->userSession->getUser();
      if ($currentUser->can('permEmailAcc')) {

          $pieces = explode('@', trim($_POST['email']));

          $email = $pieces[0];
          $domain = $pieces[1];

          $pwd1 = trim($_POST['pwd1']);
          $pwd2 = trim($_POST['pwd2']);


          $e = null;
          $ok = true;

          if (($ok) && ($pwd1 != $pwd2)) {
              $ok = false;
              $e = craft::t('Password mismatch');
          }

          if ($ok) {

              $options = array(
                  'domain' => $domain,
                  'email' => $email,
                  'password' => $pwd1,
              );

              $passwdpop = $this->xmlapi->api2_query($this->account, 'Email', 'passwdpop', $options); // Call the function.

              if (isset($passwdpop['data']['result']) && ($passwdpop['data']['result'] == 0)) {
                  $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $passwdpop['data']['reason']));
                  craft()->userSession->setError($error);
              } else {
                  craft()->userSession->setNotice(craft::t('Password modified successfully'));
              }
          } else {
              craft()->userSession->setError(craft::t('An error occured') . ' : ' . $e);
          }
      } else {
          // No permissions!
          craft()->userSession->setError(craft::t('No permission!'));
          throw new Exception(Craft::t('No permission!'));
      }
      $this->redirect($_POST['redirect']);
  }

  public function actionDelete() {
      $currentUser = craft()->userSession->getUser();
      if ($currentUser->can('permEmailAcc')) {
          $pieces = explode('@', trim($_POST['email']));

          $email = $pieces[0];
          $domain = $pieces[1];

          $options = array(
              'domain' => $domain,
              'email' => $email,
          );

          $delpop = $this->xmlapi->api2_query($this->account, 'Email', 'delpop', $options); // Call the function.

          if (isset($delpop['event']['result']) && ($delpop['event']['result'] == 0)) {
              $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $delpop['data']['reason']));
              craft()->userSession->setError($error);
          } else {
              craft()->userSession->setNotice(craft::t('Email account deleted successfully'));
          }
      } else {
          // No permissions!
          craft()->userSession->setError(craft::t('No permission!'));
          throw new Exception(Craft::t('No permission!'));
      }
      $this->redirect($_POST['redirect']);
  }
}
