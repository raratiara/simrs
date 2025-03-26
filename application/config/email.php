<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// email system config
$config['mailtype'] = 'html'; //  text or html
$config['charset'] = 'utf-8';
$config['crlf'] = '\r\n';
$config['newline'] = '\r\n';
$config['protocol'] = 'smtp'; // smtp or sendmail
//$config['mailpath'] = '/usr/sbin/sendmail -t -i',  // if use sendmail protocol
$config['smtp_crypto'] = 'ssl';
$config['smtp_host'] = 'srv28.niagahoster.com';
$config['smtp_port'] = 465;
$config['smtp_user'] = 'noreply@nathabuana.com';
$config['smtp_pass'] = 'Nathabuana#@2020';
//$config['wordwrap'] = TRUE;
//$config['wrapchars'] = 76;
//$config['validate'] = FALSE;
//$config['priority'] = 3;