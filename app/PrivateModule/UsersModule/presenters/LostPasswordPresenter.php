<?php

namespace App\PrivateModule\UsersModule\Presenter;


use App\Entity\User;
use App\PrivateModule\BasePresenter;
use Nette,
	Nette\Application\UI\Form;

/**
 * @author Petr Besir Horacek <sirbesir@gmail.com>
 * Sign in/out presenters.
 */
class LostPasswordPresenter extends BasePresenter
{

	//const PASSWORD_PATTERN = '^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!"#$%^&*:\/;()><\?\-_=+,.]).*$'; // velke pismeno, male pismeno, cislo a specialni znak
	const PASSWORD_PATTERN = '^.*(?=.{8,})(?=.*[a-zA-Z0-9])(?=.*[!"#$%^&*:\/;()><\?\-_=+,.]).*$'; // jakykoli znak a specialni

	/**
	 * @inject
	 * @var \App\PrivateModule\UsersModule\Model\Service\Users
	 */
	public $model;

	/**
	 * Lost password form factory.
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentLostPasswordForm()
	{
		$form = new \Nette\Application\UI\Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Vyplňte uživatelské jméno.');

		$form->addSubmit('send', 'Odeslat');

		$form->onError[] = array($this, 'errorForm');
		$form->onSuccess[] = array($this, 'lostPasswordFormSubmitted');

		return $form;
	}


	/**
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @param \Nette\Application\UI\Form $form
	 * @return void
	 */
	public function lostPasswordFormSubmitted(\Nette\Application\UI\Form $form, $values)
	{
		$user = $this->model->findUser($values['username']);

		if (!$user) {
			$this->flashMessage('Uživatel s tímto jménem neexistuje');
			$this->redirect('this');
		}

		$this->model->updateChangePasswordHash($user->getId());

		$this->flashMessage('Odkaz pro změnu hesla byl odeslán na Váš e-mail', 'success');
		$this->redirect(':Private:Users:Sign:in');
	}

	/**
	 * Add form errors to flashes
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @param \Nette\Application\UI\Form $form
	 */
	public function errorForm(\Nette\Application\UI\Form $form)
	{
		foreach ($form->getErrors() as $error)
		{
			$this->getPresenter()->flashMessage($error, 'error');
		}
	}



	public function renderSetNewPassword($id, $secred)
	{
		if (!$id || !$secred) {
			$this->flashMessage('Odkaz na změnu hesla není správný', 'error');
			$this->redirect('LostPassword:');
		}
	}

	protected function createComponentSetNewPasswordForm()
	{
		$form = new \Nette\Application\UI\Form;

		$form->addHidden('hashId', $this->getParameter('id'));
		$form->addHidden('hashSecred', $this->getParameter('secred'));
		
		$form->addPassword('password', 'Nové heslo:')
			->setAttribute('placeholder', 'Nové heslo')
			->setAttribute('autocomplete', 'off')
			->addRule($form::FILLED, '%label musí být vyplněno')
			->addRule($form::PATTERN, 'Prosím zadejte silnější heslo. Heslo musí být minimálně 8 znaků dlouhé a obsahovat alespoň jeden ze znaků : ! " # $ % & ( ) * + , . - / : ; < = > ? _', self::PASSWORD_PATTERN );

		$form->addPassword('password2', 'Potvrzení hesla:')
			->setAttribute('placeholder', 'Potvrzení hesla')
			->setAttribute('autocomplete', 'off')
			->addRule($form::FILLED, '%label musí být vyplněno')
			->addRule($form::EQUAL, "Hesla se neshodují", $form["password"]);

		$form->addSubmit('send', 'Nastavit heslo');

		$form->onError[] = array($this, 'errorForm');
		$form->onSuccess[] = array($this, 'setNewPasswordFormSubmitted');

		return $form;
	}

	public function setNewPasswordFormSubmitted(Form $form, $values)
	{
		/** @var User $user */
		$user = $this->model->findUserByChangePasswordHash($values['hashId'], $values['hashSecred']);

		if (!$user) {
			$this->flashMessage('Odkaz na změnu hesla je neplatný, nebo již vypršel', 'error');
			$this->redirect('LostPassword:');
		}
		
		$this->model->updatePassword($user, $values['password']);
		
		$this->flashMessage('Nové heslo je nastaveno, přihlašte se prosím');
		$this->redirect('Sign:in');
	}

}