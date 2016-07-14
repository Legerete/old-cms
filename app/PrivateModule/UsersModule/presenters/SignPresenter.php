<?php

namespace App\PrivateModule\UsersModule\Presenter;
use App\PrivateModule\BasePresenter;
use Nette\Application\ForbiddenRequestException;
use Doctrine\DBAL\Exception\ConnectionException;
use Nette\Application\UI\Form;

/**
 * @author Petr Besir Horacek <sirbesir@gmail.com>
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
	public function renderIn()
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(':Private:Dashboard:Dashboard:');
		}
	}


	/**
	 * Sign-in form factory.
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @return Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new \Nette\Application\UI\Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setAttribute('placeholder', 'Uživatelské jméno')
			->setDefaultValue(!$this->getHttpRequest()->getQuery('username')?:$this->getHttpRequest()->getQuery('username'))
			->setRequired('Vyplňte uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setAttribute('placeholder', 'Heslo')
			->setRequired('Vyplňte heslo.');

		$form->addCheckbox('remember');
		$form->addHidden('backlink', $this->getHttpRequest()->getQuery('backlink', FALSE));

		$form->addSubmit('send', 'Přihlásit');

		$form->onError[] = array($this, 'errorForm');
		$form->onSuccess[] = array($this, 'signInFormSubmitted');

		return $form;
	}


	/**
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @param \Nette\Application\UI\Form $form
	 * @return void
	 */
	public function signInFormSubmitted(\Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();

		if ($values->remember) {
			$this->getUser()->setExpiration('+ 2 days', FALSE);
		} else {
			$this->getUser()->setExpiration('+ 40 minutes', TRUE, TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
		} catch (\Nette\Security\AuthenticationException $e) {
			$this->flashMessage($e->getMessage(), 'danger');
			$this->redirect('this');
		}

		if ($values['backlink'])
		{
			$this->restoreRequest($values['backlink']);
		}
		else
		{
			$this->redirect(':Private:Dashboard:Dashboard:');
		}
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

	/**
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 */
	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste odhlášen.');
		$this->redirect('in');
	}

}
