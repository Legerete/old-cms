<?php

namespace App\PrivateModule\UsersModule\Components\UsersListGrid;

use App\Entity\User;
use Nette;
use Nette\Caching;
use Ublaboo;
use Ublaboo\DataGrid\DataGrid;
use Kdyby\Doctrine\EntityManager;


class UsersListGridControl extends \Nette\Application\UI\Control
{
	/** @var EntityManager $entityManager */
	private $entityManager;


	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}



	public function render()
	{
		$this->template->render(__DIR__ . '/UsersListGrid.latte');
	}




	public function createComponentUsersListGrid($name)
	{
		/**
		 * @var Ublaboo\DataGrid\DataGrid
		 */
		$grid = new DataGrid($this, $name);

		$userRepository = $this->entityManager->getRepository(User::class);
		$grid->setDataSource($userRepository->createQueryBuilder('u'));

		/**
		 * Columns
		 */
		$grid->addColumnNumber('id', 'Id');
		$grid->addColumnNumber('username', 'Username');
		$grid->addColumnNumber('name', 'Jméno');
		$grid->addColumnNumber('surname', 'Příjmení');
		$grid->addColumnNumber('email', 'E-mail');

		$grid->addColumnText('status', 'Status')
			->setTemplate(__DIR__ . '/Datagrid/grid.status.latte')
			->setAlign('center');

		/**
		 * Actions
		 */
		$grid->addAction('id', 'Edit', 'edit!')
			->setClass('btn btn-xs btn-primary');
	}

	public function handleEdit($id)
	{
		$this->presenter->redirect('List:edit', $id);
	}
	
}