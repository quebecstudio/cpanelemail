<?php

namespace Craft;

class CpanelEmail_MailfwdController extends BaseController {

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
        if ($currentUser->can('permEmailFwd')) {


            $email = trim($_POST['email']) . '@' . trim($_POST['domain']);
            $domain = trim($_POST['domain']);
            $fwdopt = trim($_POST['fwdopt']);
            $fwdemail = trim($_POST['fwdemail']);
            $failmsgs = trim($_POST['failmsgs']);

            if ($fwdopt == 'fwd') {
                $options = array(
                    'domain' => $domain,
                    'email' => $email,
                    'fwdopt' => 'fwd',
                    'fwdemail' => $fwdemail,
                );
            } elseif ($fwdopt == 'fail') {
                $options = array(
                    'domain' => $domain,
                    'email' => $email,
                    'fwdopt' => 'fail',
                    'failmsgs' => $failmsgs,
                );
            }

            if (isset($options)) {
                $addforward = $this->xmlapi->api2_query($this->account, 'Email', 'addforward', $options); // Call the function.
                if (isset($addforward['data']['result']) && ($addforward['data']['result'] == 0)) {
                    $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $addforward['data']['reason']));
                    craft()->userSession->setError($error);
                } else {
                    craft()->userSession->setNotice(craft::t('Forwarder added successfully'));
                }
            } else {
                craft()->userSession->setError(craft::t('An error occured'));
            }
        } else {
            // No permissions!
            craft()->userSession->setError(craft::t('No permission!'));
            throw new Exception(Craft::t('No permission!'));
        }
        $this->redirect($_POST['redirect']);
    }

    public function actionAdddmn() {

        $currentUser = craft()->userSession->getUser();
        if ($currentUser->can('permEmailDmnFwd')) {

            $domain = trim($_POST['domain']);
            $destdomain = trim($_POST['destdomain']);

            $options = array(
                'domain' => $domain,
                'destdomain' => $destdomain,
            );

            $adddomainforward = $this->xmlapi->api2_query($this->account, 'Email', 'adddomainforward', $options); // Call the function.
            if (isset($adddomainforward['data']['result']) && ($adddomainforward['data']['result'] == 0)) {
                $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $adddomainforward['data']['reason']));
                craft()->userSession->setError($error);
            } else {
                craft()->userSession->setNotice(craft::t('Domain forwarder added successfully'));
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
        if ($currentUser->can('permEmailFwd')) {

            $email = trim($_POST['email']);
            $emaildest = trim($_POST['emaildest']);

            $delforward = $this->xmlapi->api1_query($this->account, 'Email', 'delforward', array($email, $emaildest)); // Call the function.

            if (isset($delforward['error'])) {
                $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $delforward['error']));
                craft()->userSession->setError($error);
            } else {
                craft()->userSession->setNotice(craft::t('Forwarder deleted successfully'));
            }
        } else {
            // No permissions!
            craft()->userSession->setError(craft::t('No permission!'));
            throw new Exception(Craft::t('No permission!'));
        }
        $this->redirect($_POST['redirect']);
    }

    public function actionDeleteDmn() {
        $currentUser = craft()->userSession->getUser();
        if ($currentUser->can('permEmailDmnFwd')) {


            $domain = trim($_POST['domain']);

            $delforward = $this->xmlapi->api1_query($this->account, 'Email', 'deldforward', array($domain)); // Call the function.

            if (isset($delforward['error'])) {
                $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $delforward['error']));
                craft()->userSession->setError($error);
            } else {
                craft()->userSession->setNotice(craft::t('Domain forwarder deleted successfully'));
            }
        } else {
            // No permissions!
            craft()->userSession->setError(craft::t('No permission!'));
            throw new Exception(Craft::t('No permission!'));
        }
        $this->redirect($_POST['redirect']);
    }

}
