<?php

namespace Denizen\Model;

class User extends \Peyote\Model
{
	public $idProperty = 'user_id';

	public function id()
	{
		return $this->offsetGet($this->idProperty);
	}

	/**
	 * @return array  A list of validation errors
	 */
	public function validate()
	{
		$errors = array();

		if ( ! $this->offsetExists('first_name'))
		{
			$errors[] = 'first_name|required';
		}

		if ( ! $this->offsetExists('last_name'))
		{
			$errors[] = 'last_name|required';
		}

		if ( ! $this->offsetExists('email'))
		{
			$errors[] = 'email|required';
		}
		else
		{
			if (\filter_var($this->offsetGet('email'), \FILTER_VALIDATE_EMAIL) === false)
			{
				$errors[] = 'email|email';
			}
		}

		return $errors;
	}
}
