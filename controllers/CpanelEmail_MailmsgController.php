<?php

namespace Craft;

class CpanelEmail_MailmsgController extends BaseController {

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
            $from = trim($_POST['from']);
            $subject = trim($_POST['subject']);
            $body = trim($_POST['body']);
            $domain = trim($_POST['domain']);
            $html = trim($_POST['html']);
            $charset = trim($_POST['charset']);
            $interval = trim($_POST['interval']);
            $start = null;
            $stop = null;
            if (($_POST['msgstart'] == 1) && ($_POST['date_start']['date'])) {
                $date = dateTime::createFromString($_POST['date_start']);
                $start = strtotime($date->format('Y-m-d H:i:s'));
            }
            if (($_POST['msgend'] == 1) && ($_POST['date_end']['date'])) {
                $date = dateTime::createFromString($_POST['date_end']);
                $stop = strtotime($date->format('Y-m-d H:i:s'));
            }
            $add_autoresponder = $this->xmlapi->api1_query($this->account, 'Email', 'addautoresponder', array($email, $from, $subject, $body, $domain, $html, $charset, $interval, $start, $stop)); // Call the function.

            if (isset($add_autoresponder['error'])) {
                $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $add_autoresponder['error']));
                craft()->userSession->setError($error);
            } else {
                craft()->userSession->setNotice(craft::t('Auto responder added successfully'));
            }
        } else {
            // No permissions!
            craft()->userSession->setError(craft::t('No permission!'));
            throw new Exception(Craft::t('No permission!'));
        }
        $this->redirect($_POST['redirect']);
    }

    public function actionEdit() {

        $currentUser = craft()->userSession->getUser();
        if ($currentUser->can('permEmailAcc')) {

            $email = trim($_POST['email']);
            $from = trim($_POST['from']);
            $subject = trim($_POST['subject']);
            $body = trim($_POST['body']);
            $domain = trim($_POST['domain']);
            $html = trim($_POST['html']);
            $charset = trim($_POST['charset']);
            $interval = trim($_POST['interval']);
            $start = '';
            $stop = '';
            if (($_POST['msgstart'] == 1) && ($_POST['date_start']['date'])) {
                $date = dateTime::createFromString($_POST['date_start']);
                $start = strtotime($date->format('Y-m-d H:i:s'));
            }
            if (($_POST['msgend'] == 1) && ($_POST['date_end']['date'])) {
                $date2 = dateTime::createFromString($_POST['date_end']);
                $stop = strtotime($date2->format('Y-m-d H:i:s'));
            }


            $del_autoresponder = $this->xmlapi->api1_query($this->account, 'Email', 'delautoresponder', array($email . '@' . $domain)); // Call the function.
            $add_autoresponder = $this->xmlapi->api1_query($this->account, 'Email', 'addautoresponder', array($email, $from, $subject, $body, $domain, $html, $charset, $interval, $start, $stop)); // Call the function.


            if (isset($add_autoresponder['error'])) {
                $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $add_autoresponder['error']));
                craft()->userSession->setError($error);
            } else {
                craft()->userSession->setNotice(craft::t('Auto responder edited successfully'));
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

            $email = trim($_POST['email']);
            $del_autoresponder = $this->xmlapi->api1_query($this->account, 'Email', 'delautoresponder', array($email)); // Call the function.

            if (isset($del_autoresponder['error'])) {
                $error = trim(str_replace('Ignore any messages of success. This can only result in failure!', '', $del_autoresponder['error']));
                craft()->userSession->setError($error);
            } else {
                craft()->userSession->setNotice(craft::t('Auto responder deleted successfully'));
            }
        } else {
            // No permissions!
            craft()->userSession->setError(craft::t('No permission!'));
            throw new Exception(Craft::t('No permission!'));
        }
        $this->redirect($_POST['redirect']);
    }

}
