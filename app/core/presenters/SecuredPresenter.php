<?php

namespace App\Presenters;

use Tracy\Debugger;
use Tracy\Dumper;


/**
 * Base presenter for all secured application presenters.
 */
class SecuredPresenter extends \App\Presenters\BasePresenter
{

	/**
	 * @inject
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	public $em;

	/**
	 * @inject
	 * @var \Nette\Http\Request
	 */
	public $request;

	/**
	 * Startup
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @see Nette\Application\Presenter#startup()
	 */
	public function startup()
	{
		parent::startup();

		$this->getTemplate()->addFilter('addHttp', function($url){
			if (!preg_match("~^(?:ftp|http)s?://~i", $url)) {
				$url = "http://" . $url;
			}
			return $url;
		});

		/**
		 * Kontrola validnosti přihlášení
		 */
		$user = $this->getUser();
		$this->getTemplate()->identity = $user->getIdentity();

		if (in_array($this->getName(), ['Private:Users:Sign', 'Private:Users:LostPassword'])) {

		}
		elseif ( FALSE === $user->isLoggedIn() )
		{
			if ( $user->getLogoutReason() === \Nette\Security\User::INACTIVITY )
			{
				$this->flashMessage('Byl jste dlouho nečinný, systém Vás kvůli bezpečnosti odhlásil.');
			}

			$backlink = $this->getPresenter()->storeRequest();
			$this->redirect(':Private:Users:Sign:in', array('backlink' => $backlink));
		}
		else
		{
			if ( $user->isAllowed($this->name, $this->action) === FALSE )
			{
				$this->flashMessage('Nemáte dostatečná práva pro tuto akci!', 'danger');
				$this->redirect(':Private:Dashboard:Dashboard:');
			}
		}
	}
}
