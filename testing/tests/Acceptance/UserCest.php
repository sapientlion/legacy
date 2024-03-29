<?php


namespace Tests\Acceptance;

use Codeception\Step\Argument\PasswordArgument;
use Tests\Support\AcceptanceTester;

class UserCest
{
	private function signUp(AcceptanceTester $I) : void
	{
		$I->amOnPage('/index.php');
		$I->see('Sign up');
		$I->click('Sign up');
		$I->amOnPage('/user_signup.php');

		$I->fillField('user-name', 'SapientLion');
		$I->fillField('user-email', 'hello@world.org');
		$I->fillField(
			'user-password', new PasswordArgument('1234567890')
		);
		$I->fillField(
			'user-conf-password', new PasswordArgument('1234567890')
		);

		$I->click('Sign up', 'form');
		$I->amOnPage('/index.php');
	}

	private function signIn(AcceptanceTester $I) : void
	{
		$I->see('Sign in');
		$I->click('Sign in');

		$I->fillField('user-name', 'SapientLion');
		$I->fillField(
			'user-password', new PasswordArgument('1234567890')
		);

		$I->click('Sign in', 'form');
		$I->amOnPage('/index.php');
	}

    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToSignUp(AcceptanceTester $I)
    {
		$I->amOnPage('/index.php');
		$I->see('Sign up');
		$I->click('Sign up');
		$I->amOnPage('/user_signup.php');

		$I->fillField('user-name', 'SapientLion');
		$I->fillField('user-email', 'hello@world.org');

		$password = '1234567890';

		$I->fillField(
			'user-password', new PasswordArgument($password)
		);
		$I->fillField(
			'user-conf-password', new PasswordArgument($password)
		);

		$I->click('Sign up', 'form');
		$I->makeScreenshot('UserCestSignupResult');
		$I->amOnPage('/index.php');

		$this->signIn($I);
    }

	public function tryToUpdateByChangingUserName(AcceptanceTester $I)
	{
		$this->signUp($I);
		$this->signIn($I);

		$I->amOnPage('/index.php');
		$I->see('SapientLion');
		$I->click('SapientLion');
		$I->amOnPage('/user_updater.php');
		
		$I->fillField('user-name', 'LionTheSapient');

		$I->click('Update');
		$I->amOnPage('/user_updater.php');
		$I->see('LionTheSapient');
	}

	public function tryToUpdateByChangingEmailAddress(AcceptanceTester $I)
	{
		$this->signUp($I);
		$this->signIn($I);

		$I->amOnPage('/index.php');
		$I->see('SapientLion');
		$I->click('SapientLion');
		$I->amOnPage('/user_updater.php');
		
		$I->fillField('user-email', 'goodbye@world.org');

		$I->click('Update');
		$I->amOnPage('/user_updater.php');
		$I->see('goodbye@world.org');
	}

	public function tryToUpdateByChangingPassword(AcceptanceTester $I)
	{
		$this->signUp($I);
		$this->signIn($I);

		$I->amOnPage('/index.php');
		$I->see('SapientLion');
		$I->click('SapientLion');
		$I->amOnPage('/user_updater.php');
		
		$I->fillField(
			'user-password', 
			new PasswordArgument('8916858')
		);
		$I->fillField(
			'user-conf-password', 
			new PasswordArgument('8916858')
		);

		$I->click('Update');
		$I->amOnPage('/user_updater.php');
		$I->see('goodbye@world.org');
	}

	public function tryToSignIn(AcceptanceTester $I)
	{
		$this->signUp($I);

		$I->see('Sign in');
		$I->click('Sign in');
		$I->amOnPage('/user_signin.php');

		$I->fillField('user-name', 'SapientLion');
		$I->fillField(
			'user-password', new PasswordArgument('1234567890')
		);

		$I->click('Sign in', 'form');
		$I->makeScreenshot('UserCestSigninResult');
		$I->amOnPage('/index.php');

		$I->see('SapientLion');
		$I->see('Sign out');
	}
}
