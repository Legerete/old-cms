<?php

namespace App\PrivateModule\UsersModule\Components\UsersForm;

interface IUsersFormFactory
{

	/**
	 * @return UsersForm
	 */
	function create();

}