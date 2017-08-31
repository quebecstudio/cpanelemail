<?php

namespace Craft;

class CpanelEmailVariable
{
  private $xmlapi=null;
  private $settings=null;
  private $account=null;



  public function getSettings()
  {
      return $this->settings;
  }

  public function __construct()
  {

      $this->settings = craft()->plugins->getPlugin('cpanelemail')->getSettings();
      $ip = $this->settings['cpanel_ip'];
      $username = $this->settings['cpanel_username'];
      $password = $this->settings['cpanel_password'];
	    $port = $this->settings['cpanel_port'];
      $this->account = $this->settings['cpanel_account'];


      if (!$this->xmlapi)
      {

          $xmlapi = new \xmlapi($ip);
          $xmlapi->set_port($port);
          $xmlapi->password_auth($username,$password);
          $xmlapi->set_output("array");

          $this->xmlapi = $xmlapi;
      }
  }

  public function getEmailAcc()
  {
      $res =  $this->xmlapi->api2_query($this->account, "Email", "listpopswithdisk", array('api2_sort'=>true,'api2_sort_column'=>'email'));
      //echo '<pre>';print_r($res);die();
      if (isset($res['error']))
      {
        return array('ERROR' => $res['error']);
      }
      elseif (isset($res['data'][1]))
      {
          $out=$res['data'];
          return $out;
      }
      elseif (isset($res['data']['user']))
      {
          $out[0]=$res['data'];
          return $out;
      }
      else
      {
          return false;
      }
  }

  public function getEmailFwd()
  {
      $res =  $this->xmlapi->api2_query($this->account, "Email", "listforwards", array('api2_sort'=>true,'api2_sort_column'=>'forward') );
      if (isset($res['error']))
      {
        return array('ERROR' => $res['error']);
      }
      elseif (isset($res['data'][1]))
      {
          $out=$res['data'];
          return $out;
      }
      elseif (isset($res['data']['dest']))
      {
          $out[0]=$res['data'];
          return $out;
      }
      else
      {
          return false;
      }
  }

  public function getEmailDmnFwd()
  {
      $res =  $this->xmlapi->api2_query($this->account, "Email", "listdomainforwards", array('api2_sort'=>true,'api2_sort_column'=>'forward') );
      if (isset($res['error']))
      {
        return array('ERROR' => $res['error']);
      }
      elseif (isset($res['data'][1]))
      {
          $out=$res['data'];
          return $out;
      }
      elseif (isset($res['data']['dest']))
      {
          $out[0]=$res['data'];
          return $out;
      }
      else
      {
          return false;
      }
  }


  public function getEmailAutoResponders()
  {
      $res =  $this->xmlapi->api2_query($this->account, "Email", "listautoresponders", array('api2_sort'=>true,'api2_sort_column'=>'email') );
      if (isset($res['error']))
      {
        return array('ERROR' => $res['error']);
      }
      elseif (isset($res['data'][1]))
      {
          $out=$res['data'];
          return $out;
      }
      elseif (isset($res['data']['email']))
      {
          $out[0]=$res['data'];
          return $out;
      }
      else
      {
          return false;
      }
  }

  public function getEmailDomains()
  {
      $res =  $this->xmlapi->api2_query($this->account, "Email", "listmaildomains", array('api2_sort'=>true,'api2_sort_column'=>'domain') );
      if (isset($res['data'][1]))
      {
          $out=$res['data'];
          return $out;
      }
      elseif (isset($res['data']['domain']))
      {
          $out[0]=$res['data'];
          return $out;
      }
      else
      {
          return false;
      }
  }

  public function defaultQuota()
  {
      return '250';
  }

  function getStats()
  {

      $options=array(
          'display' => 'emailforwarders|emailaccounts|autoresponders'
              );

      $res =  $this->xmlapi->api2_query($this->account, "StatsBar", "stat", $options );

      $stats=array();
      foreach ($res['data'] as $item)
      {
          if (isset($item['name']))
          {
              $stats[$item['name']]=$item;
          }
      }
      return $stats;
  }

  public function fetchAutoResponder($email)
  {
      $res =  $this->xmlapi->api2_query($this->account, "Email", "fetchautoresponder", array('email' => $email) );


      if (isset($res['data']))
      {
          $pieces = explode('@',$email);
          $res['data']['email']=$pieces[0];
          $res['data']['domain']=$pieces[1];
          if (is_string($res['data']['start']))
          {
              $res['data']['date_start']=DateTime::createFromString($res['data']['start']);
          }
          else
          {
              $res['data']['date_start']=null;
              $res['data']['start']='0';
          }

          if (is_string($res['data']['stop']))
          {
              $res['data']['date_end']=DateTime::createFromString($res['data']['stop']);
          }
          else
          {
              $res['data']['date_end']=null;
              $res['data']['stop']='0';
          }
          return $res['data'];
      }
      else
      {
          return false;
      }
  }


}
