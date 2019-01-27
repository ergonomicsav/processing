<?php

/**
 * Progect: ergonomics
 * File: processing.php
 * Author: Andru
 * Date: 19.08.2017
 * Time: 16:12
 */
abstract class Processing
{
	private   $your_email = 'vashepravo-info@ukr.net';
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

class Ukraine extends Processing
{
	private $default_subject       = 'З українськомовної контактной форми';
	private $name_not_specified    = 'Будь ласка, введіть дійсне ім\'я';
	private $message_not_specified = 'Введіть дійсне повідомлення';
	private $tel_not_specified     = 'Введіть дійсний номер';
	private $email_was_sent        = 'Дякую, Ваше повідомлення успішно відправлено';
	private $server_not_configured = 'На жаль, поштовий сервер не налаштовано';

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
	public function getTelNotSpecified(){
		return $this->tel_not_specified;
	}
}

class Russia extends Processing
{
	private $default_subject       = 'С русскоязычной контактной формы';
	private $name_not_specified    = 'Пожалуйста введите настоящее имя';
	private $message_not_specified = 'Введите сообщение';
	private $tel_not_specified     = 'Введите номер телефона';
	private $email_was_sent        = 'Спасибо, Ваше уведомление успешно отправленно';
	private $server_not_configured = 'К сожалению почтовый сервер не конфигурирован';

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
	public function getTelNotSpecified(){
		return $this->tel_not_specified;
	}
}

abstract class Forms
{
	protected $errors = array();

	abstract function treatment(Processing $processing);

}

class StaticForm extends Forms
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

			$from = (!empty($sender_name)) ? 'From: ' . $sender_name : '';
			$from .= (!empty($sender_email)) ? ' email: ' . $sender_email : '';

			$subject = $processing->getDefaultSubject();

			$message = (!empty($message)) ? wordwrap($message, 70) : '';

			//sending message if no errors
			if(empty($this->errors)){
				if(mail($processing->getYourEmail(), $subject, $message, $from)){
					echo $processing->getEmailWasSent();
				}else{
					$this->errors[] = $processing->getServerNotConfigured();
					echo implode('<br>', $this->errors);
				}
			}else{
				echo implode('<br>', $this->errors);
			}
		}
	}
}

class ModalForm extends Forms
{
	public function treatment(Processing $processing){
		if(isset($_POST['tel']) and isset($_POST['name'])){
			if(!empty($_POST['name'])) $sender_name = stripslashes(strip_tags(trim($_POST['name'])));

			if(!empty($_POST['tel'])) $tel = stripslashes(strip_tags(trim($_POST['tel'])));

			//Message if no sender name was specified
			if(empty($sender_name)){
				$this->errors[] = $processing->getNameNotSpecified();
			}

			//Message if no message was specified
			if(empty($tel)){
				$this->errors[] = $processing->getTelNotSpecified();
			}

			$from = (!empty($sender_name)) ? 'From: ' . $sender_name : '';

			$subject = $processing->getDefaultSubject();

			$message = (!empty($tel)) ? $tel : '';

			//sending message if no errors
			if(empty($this->errors)){
				if(mail($processing->getYourEmail(), $subject, $message, $from)){
					echo $processing->getEmailWasSent();
				}else{
					$this->errors[] = $processing->getServerNotConfigured();
					echo implode('<br>', $this->errors);
				}
			}else{
				echo implode('<br>', $this->errors);
			}
		}
	}
}

function choice(){
	if(isset($_POST['form'])){
		switch($_POST['form']){
			case 'modal_ukr' :
				return new Ukraine(new ModalForm());
			break;
			case 'modal_rus' :
				return new Russia(new ModalForm());
			break;
			case 'staticum_ukr' :
				return new Ukraine(new StaticForm());
			break;
			case 'staticum_rus' :
				return new Russia(new StaticForm());
			break;
			default:
				return new Ukraine(new StaticForm());
		}
	}
}

$obj = choice();
$obj->treatment();