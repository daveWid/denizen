<?php

namespace Denizen\Model;

class NewUser extends User
{
	/**
	 * @return array  A list of validation errors
	 */
	public function validate()
	{
		$errors = parent::validate();

		$passwords = 2;
		if ( ! $this->offsetExists('password'))
		{
			$errors[] = 'password|required';
			$passwords--;
		}

		if ( ! $this->offsetExists('confirm_password'))
		{
			$errors[] = 'confirm_password|required';
			$passwords--;
		}

		if ($passwords === 2)
		{
			if ($this->offsetGet('password') !== $this->offsetGet('confirm_password'))
			{
				$errors[] = 'password|matches';
			}
		}

		return $errors;
	}

}
