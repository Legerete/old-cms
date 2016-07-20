<?php

namespace App\PrivateModule\UsersModule\Components\UsersListGrid;

interface IUsersListGridControlFactory
{

	/**
	 * @return UsersListGridControl
	 */
	function create();

}