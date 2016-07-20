<?php

namespace App\PrivateModule\UsersModule\Presenter;

use App\PrivateModule\PrivatePresenter;
use App\PrivateModule\UsersModule\Components\UsersForm\IUsersFormFactory;
use App\PrivateModule\UsersModule\Components\UsersListGrid\IUsersListGridControlFactory;


class ListPresenter extends PrivatePresenter
{
	/**
	 * @var  IUsersListGridControlFactory
	 * @inject
	 */
	public $usersListGridControlFactory;

	/**
	 * @var  IUsersFormFactory
	 * @inject
	 */
	public $usersFormFactory;

	
	public function createComponentUsersListGrid()
	{
		return $this->usersListGridControlFactory->create();
	}
	
	public function createComponentUsersForm()
	{
		return $this->usersFormFactory->create();
	}
	
	
	public function renderDefault()
	{
		
	}

	public function renderAdd()
	{
		
	}

	public function renderEdit($id)
	{
		
	}

}