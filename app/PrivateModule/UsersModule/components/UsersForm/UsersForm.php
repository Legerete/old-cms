<?php

namespace App\PrivateModule\UsersModule\Components\UsersForm;

use App\Entity\User;
use App\PrivateModule\UsersModule\Model\Service\Users;
use Nette;
use Nette\Caching;
use Ublaboo;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Tracy\Debugger;


class UsersForm extends \Nette\Application\UI\Control
{
	/** @var EntityManager $entityManager */
	private $entityManager;

	/**
	 * @var \App\PrivateModule\UsersModule\Model\Service\Users
	 */
	public $userModel;

	public function __construct(EntityManager $entityManager, Users $userModel)
	{
		$this->entityManager = $entityManager;
		$this->userModel = $userModel;
	}



	public function render()
	{
		if ($this->presenter->getAction() === 'edit') {
			$user = $this->entityManager->getRepository(User::class)->find($this->presenter->getParameter('id'));
			$this->template->user = $user;
		}


		$this->template->render(__DIR__ . '/UsersForm.latte');
	}



	public function createComponentUsersForm()
	{
		$form = new Form;

		$form->addText('username', 'Uživatelské jméno')
			->addRule($form::FILLED, '%label musí být vyplněno')
			->addRule($form::MAX_LENGTH, '%label může být maximálně %d znaků', 100);

		$form->addText('email', 'E-mail')
			->addRule($form::FILLED, '%label musí být vyplněn')
			->addRule($form::EMAIL, '%label nemá správný tvar')
			->addRule($form::MAX_LENGTH, '%label může být maximálně %d znaků', 100);

		$form->addText('name', 'Jméno')
			->addRule($form::FILLED, '%label musí být vyplněno')
			->addRule($form::MAX_LENGTH, '%label může být maximálně %d znaků', 65);

		$form->addText('surname', 'Příjmení')
			->addRule($form::FILLED, '%label musí být vyplněno')
			->addRule($form::MAX_LENGTH, '%label může být maximálně %d znaků', 65);

		$form->addSelect('role', 'Role', [
			'admin' => 'Admin'
		])->setPrompt('Vyberte')
			->addRule($form::FILLED, '%label musí být vyplněny');

		if ($this->presenter->getAction() === 'add') {
			$form->addSubmit('submit', 'Vložit');
		}
		else {
			$form->addSubmit('submit', 'Uložit');
		}

		$form->addSubmit('cancel', 'Zrušit')
			->setValidationScope(FALSE)
			->onClick[] = [$this, 'cancelForm'];

		$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'processForm'];
		$form->onError[] = [$this, 'errorForm'];


		if ($this->presenter->getAction() === 'edit') {
			$form->addHidden('id');

			$userId = $this->presenter->getParameter('id');
			$userRepository = $this->entityManager->getRepository(User::class);
			$userDb = $userRepository->find($userId);

			$form->setDefaults([
				'id' => $userDb->id,
				'username' => $userDb->username,
				'email' => $userDb->email,
				'name' => $userDb->name,
				'surname' => $userDb->surname,
				'role' => $userDb->role,
			]);
		}


		Debugger::barDump( $this->presenter->getAction() );
		Debugger::barDump( $this->presenter->getParameter('id') );

		return $form;
	}



	public function validateForm(Form $form, $value)
	{
		if (isset($value->id)) {
			$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $value->email, 'id !=' => $value->id]);
			if ($user) {
				$form['email']->addError('Tento e-mail již existuje');
			}

			$user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $value->username, 'id !=' => $value->id]);
			if ($user) {
				$form['username']->addError('Toto uživatelské jméno již existuje');
			}
		}


		//dump($value);exit;
	}


	public function errorForm(Form $form)
	{
		//dump($form->getErrors());
	}



	/*
	 * Zpracovani formulare
	 */
	public function processForm(Form $form, $value)
	{
		if (!isset($value->id)) {
			$user = new User();
		}
		else {
			$user = $this->entityManager->getRepository(User::class)->find($value->id);
		}

		$user->setUsername($value->username);
		$user->setEmail($value->email);
		$user->setName($value->name);
		$user->setSurname($value->surname);
		$user->setRole($value->role);

		// add
		if (!isset($value->id)) {
			$user->setPassword(Nette\Utils\Random::generate(12));
			$this->entityManager->persist($user);
			$this->entityManager->flush();

			$this->userModel->updateChangePasswordHash($user->getId());

			$this->presenter->flashMessage('Uživatel vložen');
			$this->presenter->redirect('List:');
		}
		// edit
		else {
			$this->entityManager->merge($user);
			$this->entityManager->flush();
			$this->presenter->flashMessage('Uživatel uložen');
			$this->presenter->redirect('this');
		}
	}


	public function cancelForm()
	{
		$this->presenter->redirect('List:');
	}


	public function handleDel($id)
	{
		/** @var User $user */
		$user = $this->entityManager->getRepository(User::class)->find($id);
		$this->entityManager->persist($user);
		$user->destroy();
		$this->entityManager->flush();

		$this->presenter->flashMessage('Uživatel byl smazán');
		$this->redirect('this');
	}
}
