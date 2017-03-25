<?php
/**
 * Email plugin for Craft CMS
 *
 * Email management using cPanel API
 *
 *
 * @author    Québec Studio
 * @copyright Copyright (c) 2017 Québec Studio
 * @link      http://quebecstudio.com
 * @package   cpanelemail
 * @since     1.1
 */

namespace Craft;

class CpanelEmailPlugin extends BasePlugin
{

    public function init()
    {
      require(CRAFT_PLUGINS_PATH . '/cpanelemail/include/xmlapi.php');
    }

    public function getName()
    {
         return Craft::t('Emails');
    }

    public function getDescription()
    {
        return Craft::t('Email management using cPanel API');
    }


    public function getDocumentationUrl()
    {
        return $this->getPluginUrl().'/plugins/cpanelemail/'.$this->getVersion().'/README.md';
    }


    public function getReleaseFeedUrl()
    {
        return null;
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    public function getSchemaVersion()
    {
        return '1.0';
    }

    public function getDeveloper()
    {
        return 'Québec Studio';
    }

    public function getDeveloperUrl()
    {
        return 'http://quebecstudio.com';
    }

  	public function getPluginUrl()
  	{
  		return '';
  	}

    public function hasCpSection()
    {
        return true;
    }

    protected function defineSettings()
    {
      return array(
          'cpanel_ip' => array(AttributeType::String, 'required' => true, 'default' => 'localhost'),
		      'cpanel_port' => array(AttributeType::Number, 'required' => true, 'default' => 2082),
          'cpanel_username' => array(AttributeType::String, 'required' => true, 'default' => 'root'),
          'cpanel_password' => array(AttributeType::String, 'required' => true, 'default' => '1234'),
          'cpanel_account' => array(AttributeType::String, 'required' => true, 'default' => 'myaccount'),          
      );
    }

    public function getSettingsHtml()
    {
       return craft()->templates->render('cpanelemail/settings', array(
           'settings' => $this->getSettings()
       ));
    }

    public function prepSettings($settings)
    {
        return $settings;
    }

    public function registerUserPermissions() {
        return array(
            'permEmailAcc' => array('label' => Craft::t('Manage email accounts/users')),
            'permEmailFwd' => array('label' => Craft::t('Manage email redirections')),
            'permEmailDmnFwd' => array('label' => Craft::t('Manage domain redirections')),
            'permEmailMsg' => array('label' => Craft::t('Manage auto responders for email accounts')),
            'permEmailStats' => array('label' => Craft::t('View usage statistics')),
            'permEmailAdvStats' => array('label' => Craft::t('View website and server statistics (advanced)')),
        );
    }

    public function registerCpRoutes() {
        return array(
            'cpanelemail\/mailacc' => 'cpanelemail/mail/mailacc',
            'cpanelemail\/mailfwd' => 'cpanelemail/mail/mailfwd',
            'cpanelemail\/mailmsg' => 'cpanelemail/mail/mailmsg',
            'cpanelemail\/mailacc\/add' => 'cpanelemail/mail/_accadd',
            'cpanelemail\/mailacc\/quota\/(?P<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,8}+)' => 'cpanelemail/mail/_accquota',
            'cpanelemail\/mailacc\/password\/(?P<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,8}+)' => 'cpanelemail/mail/_accpassword',
            'cpanelemail\/mailacc\/delete\/(?P<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,8}+)' => 'cpanelemail/mail/_accdelete',
            'cpanelemail\/mailacc\/config\/(?P<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,8}+)' => 'cpanelemail/mail/_accconfig',
            'cpanelemail\/mailfwd\/add' => 'cpanelemail/mail/_fwdadd',
            'cpanelemail\/mailfwd\/adddmn' => 'cpanelemail/mail/_fwdadddmn',
            'cpanelemail\/mailfwd\/delete\/(?P<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,8}+)' => 'cpanelemail/mail/_fwddelete',
            'cpanelemail\/mailfwd\/deletedmn\/(?P<domain>[a-zA-Z0-9.-]+\.[a-zA-Z]{2,8}+)' => 'cpanelemail/mail/_fwddeletedmn',
            'cpanelemail\/mailmsg\/add' => 'cpanelemail/mail/_msgadd',
            'cpanelemail\/mailmsg\/edit\/(?P<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,8}+)' => 'cpanelemail/mail/_msgedit',
            'cpanelemail\/mailmsg\/delete\/(?P<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,8}+)' => 'cpanelemail/mail/_msgdelete',
        );
    }

}
