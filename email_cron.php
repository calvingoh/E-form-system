<?php
// Configuration
require_once('config.php');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

require(DIR_SYSTEM . 'library/mail.php');
require(DIR_SYSTEM . 'library/mail/mail.php');
require(DIR_SYSTEM . 'library/mail/smtp.php');

$registry->set('mail', new Mail());

ini_set('memory_limit', -1);

$query = $db->query("SELECT COUNT(*) as total FROM email_cron");

if($query->row['total'] > 0){
	//if there is rows return in database then do the mailing
	$email_info = $db->query("SELECT * FROM email_cron ORDER BY email_cron_id");

	if($email_info->num_rows){
		foreach($email_info->rows as $email){
			
			$email_to = @unserialize($email['mail_to']);
			
			if($email_to == false){
				$email_to = $email['mail_to'];
			}
	
			$smtp_username = 'system-noreply@penanghill.gov.my';
			$smtp_password = 'gGBQ6MWYq6eMb3aB';

			$mail = new Mail('smtp');
			$mail->smtp_hostname = 'ssl://mail.penanghill.gov.my';
			$mail->smtp_username = $smtp_username;
			$mail->smtp_password = $smtp_password;
			$mail->smtp_port = '465';
			$mail->smtp_timeout = '5';
	
			//$mail->setTo(unserialize($email['mail_to']));
			$mail->setTo($email_to);
			$mail->setFrom($email['mail_from']);
			$mail->setSender(html_entity_decode("System Generated Email - " .$email['system'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($email['subject'], ENT_QUOTES, 'UTF-8'));
			$mail->setHtml(html_entity_decode($email['body'], ENT_QUOTES, 'UTF-8'));
			$mail->send();
			
			$db->query("INSERT INTO email_cron_history SET mail_to = '". $db->escape($email['mail_to'])."', mail_from = '". $db->escape($email['mail_from'])."', subject = '". $db->escape($email['subject'])."', body = '". $db->escape($email['body'])."', date_added = NOW(), status = '1', system = '". $db->escape($email['system'])."'");

			$db->query("DELETE FROM email_cron WHERE email_cron_id = '". (int)$email['email_cron_id']."'");
			
		}
	}
} else {
	//no rows
	exit();
}


?>