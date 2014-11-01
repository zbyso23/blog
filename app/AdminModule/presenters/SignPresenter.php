<?php

namespace App\AdminModule\Presenters;

use Nette,
	App\Model;


/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{

	public function actionLogin()
	{
		
	}

	public function actionLogout()
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect('login');
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new Nette\Application\UI\Form;
		$form->addText('email', 'Email:')
			->setRequired('Please enter your email.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addCheckbox('remember', 'Keep me signed in');

		$form->addSubmit('send', 'Sign in');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->signInFormSucceeded;
		return $form;
	}


	public function signInFormSucceeded($form, $values)
	{
		if ($values->remember) 
		{
			$this->getUser()->setExpiration('14 days', FALSE);
		} 
		else 
		{
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}

		try
		{
			$this->getUser()->login($values->email, $values->password);
			$this->redirect('Default:');
		} 
			catch (Nette\Security\AuthenticationException $e) 
		{
			$form->addError($e->getMessage());
		}
	}



}
