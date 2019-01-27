<?php
/**
 * Progect: ergonomics
 * File: processing.php
 * Author: Andru
 * Date: 19.08.2017
 * Time: 16:12
 */
define('PROM', true);
// подключение конфигурационного файла
require_once '../config.php';
// подключаем классы для работы с БД
require_once '..' . SITE_URL . MODEL . '/Model.php';
require_once '..' . SITE_URL . MODEL . '/Model_Driver.php';

abstract class Processing
{
	private   $your_email = 'musik_info.net';
	protected $forms;

	public function __construct(Forms $forms){
		$this->forms = $forms;
	}

	public function treatment(){
		return $this->forms->treatment($this);
	}

	/**
	 * @return string
	 */
	public function getYourEmail(){
		return $this->your_email;
	}
}

class Answer extends Processing
{
	private $default_subject       = 'mail from my site';
	private $database_subject      = 'comment added';
	private $name_not_specified    = 'Please enter a name';
	private $message_not_specified = 'There is no comment!';
	private $email_was_sent        = 'Thank you! Mail has been sent.';
	private $server_not_configured = 'mail server is not configured';
	private $comment_was_add       = 'Thanks! Your comment is awaiting moderation.';
	private $database_server_error = 'database server error';
	private $email_not_specified   = 'Please enter a email';

	/**
	 * @return string
	 */
	public function getDefaultSubject(){
		return $this->default_subject;
	}

	/**
	 * @return string
	 */
	public function getNameNotSpecified(){
		return $this->name_not_specified;
	}

	/**
	 * @return string
	 */
	public function getMessageNotSpecified(){
		return $this->message_not_specified;
	}

	/**
	 * @return string
	 */
	public function getEmailNotSpecified(){
		return $this->email_not_specified;
	}

	/**
	 * @return string
	 */
	public function getEmailWasSent(){
		return $this->email_was_sent;
	}

	/**
	 * @return string
	 */
	public function getServerNotConfigured(){
		return $this->server_not_configured;
	}

	/**
	 * @return string
	 */
	public function getCommentWasAdd(){
		return $this->comment_was_add;
	}

	/**
	 * @return string
	 */
	public function getDatabaseServerError(){
		return $this->database_server_error;
	}

	/**
	 * @return string
	 */
	public function getDatabaseSubject(){
		return $this->database_subject;
	}
}

abstract class Forms
{
	protected $errors = array();

	abstract function treatment(Processing $processing);

}

class EmailForm extends Forms
{
	public function treatment(Processing $processing){
		if(isset($_POST['message']) and isset($_POST['name'])){
			if(!empty($_POST['name'])) $sender_name = stripslashes(strip_tags(trim($_POST['name'])));

			if(!empty($_POST['message'])) $message = stripslashes(strip_tags(trim($_POST['message'])));

			if(!empty($_POST['email'])) $sender_email = stripslashes(strip_tags(trim($_POST['email'])));

			//Message if no sender name was specified
			if(empty($sender_name)){
				$this->errors[] = $processing->getNameNotSpecified();
			}

			//Message if no message was specified
			if(empty($message)){
				$this->errors[] = $processing->getMessageNotSpecified();
			}
			$ip   = $_SERVER['REMOTE_ADDR'];
			$from = (!empty($sender_name)) ? 'From: ' . $sender_name . "\r\n" : '';
			$from .= (!empty($sender_email)) ? 'email: ' . $sender_email . "\r\n" : '';
			$from .= 'ip: ' . $ip;

			$subject = $processing->getDefaultSubject();

			$message = (!empty($message)) ? wordwrap($message, 70) : '';

			//sending message if no errors
			if(empty($this->errors)){
				if(mail($processing->getYourEmail(), $subject, $message, $from)){
					//echo $processing->getEmailWasSent();
					$k0 = $processing->getEmailWasSent();
					echo "<div class='alert alert-success fade in bordercolor-bottom-success'><p class='text-center'>$k0</p></div>";
					?>
					<script>$("form.emails").trigger('reset');</script>
					<?php
				}else{
					$this->errors[] = $processing->getServerNotConfigured();
					$k1             = implode('<br>', $this->errors);
					echo "<div class='alert alert-danger fade in bordercolor-bottom-danger'><p class='text-center'>$k1</p></div>";
				}
			}else{
				$k = implode('<br>', $this->errors);
				echo "<div class='alert alert-warning fade in bordercolor-bottom-warning'><p class='text-center'>$k</p></div>";
			}
		}
	}
}

class ContactForm extends Forms
{
	public function treatment(Processing $processing){
		if(isset($_POST['message']) and isset($_POST['name']) and isset($_POST['email'])){
			if(!empty($_POST['name'])) $sender_name = stripslashes(strip_tags(trim($_POST['name'])));

			if(!empty($_POST['message'])) $message = stripslashes(strip_tags(trim($_POST['message'])));

			if(!empty($_POST['email'])) $sender_email = stripslashes(strip_tags(trim($_POST['email'])));

			if(!empty($_POST['subject'])) $sender_subject = stripslashes(strip_tags(trim($_POST['subject'])));

			//Message if no sender name was specified
			if(empty($sender_name)){
				$this->errors[] = $processing->getNameNotSpecified();
			}

			//Message if no email was specified
			if(empty($sender_email)){
				$this->errors[] = $processing->getEmailNotSpecified();
			}
			//Message if no message was specified
			if(empty($message)){
				$this->errors[] = $processing->getMessageNotSpecified();
			}


			$ip   = $_SERVER['REMOTE_ADDR'];
			$from = (!empty($sender_name)) ? 'From: ' . $sender_name . "\r\n" : '';
			$from .= (!empty($sender_email)) ? 'email: ' . $sender_email . "\r\n" : '';
			$from .= (!empty($sender_subject)) ? 'subject: ' . $sender_subject . "\r\n" : '';
			$from .= 'ip: ' . $ip;

			$subject = $processing->getDefaultSubject();

			$message = (!empty($message)) ? wordwrap($message, 70) : '';

			//sending message if no errors
			if(empty($this->errors)){
				if(mail($processing->getYourEmail(), $subject, $message, $from)){
					//echo $processing->getEmailWasSent();
					$k0 = $processing->getEmailWasSent();
					echo "<div class='alert alert-success fade in bordercolor-bottom-success'><p class='text-center'>$k0</p></div>";
					?>
					<script>$("form.contacts").trigger('reset');</script>
					<?php
				}else{
					$this->errors[] = $processing->getServerNotConfigured();
					$k1             = implode('<br>', $this->errors);
					echo "<div class='alert alert-danger fade in bordercolor-bottom-danger'><p class='text-center'>$k1</p></div>";
				}
			}else{
				$k = implode('<br>', $this->errors);
				echo "<div class='alert alert-warning fade in bordercolor-bottom-warning'><p class='text-center'>$k</p></div>";
			}
		}
	}
}

class CommentForm extends Forms
{
	public function treatment(Processing $processing){
		if(isset($_POST['message']) and isset($_POST['name'])){
			if(!empty($_POST['name'])) $sender_name = stripslashes(strip_tags(trim($_POST['name'])));

			if(!empty($_POST['message'])) $message = stripslashes(strip_tags(trim($_POST['message'])));

			if(!empty($_POST['email'])) $sender_email = stripslashes(strip_tags(trim($_POST['email'])));

			if(!empty($_POST['su'])) $sender_su = trim($_POST['su']);

			if(!empty($_POST['id'])) $id_parent_video = trim($_POST['id']);

			if(!empty($_POST['gu'])) $get_url = trim($_POST['gu']);

			//Message if no sender name was specified
			if(empty($sender_name)){
				$this->errors[] = $processing->getNameNotSpecified();
			}

			//Message if no message was specified
			if(empty($message)){
				$this->errors[] = $processing->getMessageNotSpecified();
			}
			$ip   = $_SERVER['REMOTE_ADDR'];
			$from = (!empty($sender_name)) ? 'From: ' . $sender_name . "\r\n" : '';
			$from .= (!empty($sender_email)) ? 'email: ' . $sender_email . "\r\n" : '';
			$from .= 'ip: ' . $ip;

			$subject = $processing->getDatabaseSubject();

			$message = (!empty($message)) ? wordwrap($message, 70) : '';

			//sending message if no errors
			if(empty($this->errors)){
				if(mail($processing->getYourEmail(), $subject, $message, $from)){
					//echo $processing->getEmailWasSent();
				}else{
					$this->errors[] = $processing->getServerNotConfigured();
					$k1             = implode('<br>', $this->errors);
					echo "<div class='alert alert-danger fade in bordercolor-bottom-danger'><p class='text-center'>$k1</p></div>";
				}
				// insert comment to db
				$db     = Model::get_instance();
				$result = $db->add_comment($sender_name, $message, $sender_email, $ip, $sender_su, $id_parent_video,
					$get_url);
				if($result === true){
					$k0 = $processing->getCommentWasAdd();
					echo "<div class='alert alert-success fade in bordercolor-bottom-success'><p class='text-center'>$k0</p></div>";
					?>
					<script>$("form.comments").trigger('reset');</script>
					<?php
				}else{
					$this->errors[] = $processing->getDatabaseServerError();
					$k2             = implode('<br>', $this->errors);
					echo "<div class='alert alert-danger fade in bordercolor-bottom-danger'><p class='text-center'>$k2</p></div>";
				}
			}else{
				$k = implode('<br>', $this->errors);
				echo "<div class='alert alert-warning fade in bordercolor-bottom-warning'><p class='text-center'>$k</p></div>";
			}
		}
	}
}

function choice(){
	if(isset($_POST['form'])){
		switch($_POST['form']){
			case 'comments' :
				return new Answer(new CommentForm());
			break;
			case 'emails' :
				return new Answer(new EmailForm());
			break;
			case 'contacts' :
				return new Answer(new ContactForm());
			default:
				return new Answer(new CommentForm());
		}
	}
}

$obj = choice();
$obj->treatment();