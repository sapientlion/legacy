class UserController
{
	#ErrorCode = {
		SUCCESS: 0,
		USERNAMELENGTH: 1,
		USERNAMECHARS: 2,
		EMAILLENGTH: 3,
		PASSWORDLENGTH: 4,
		PASSWORDMISMATCH: 5,
	};

	/**
	 * Check confirmation password for any errors. This method is more or less the same as the other methods included
	 * within the scope of the class. Most of the comments with various explanations will be put here.
	 * @returns {SUCCESS} success.
	 * @returns {PASSWORDLENGTH} something's wrong with the string length
	 */
	#checkConfirmationPassword()
	{
		//
		// Get all known registration form elements from HTML.
		//
		const passInput = document.getElementById('password');
		const confPassInput = document.getElementById('confirmation-password');

		let pass = passInput.value;
		let conPass = confPassInput.value;
		let messagebox = document.getElementById('messagebox-confirmation-password');

		//
		// String mustn't be empty.
		//
		if(pass.length <= 0)
		{
			messagebox.innerHTML = 'Required';

			return this.#ErrorCode.PASSWORDLENGTH;;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		//
		// Passwords from both of the password input fields must be equal.
		//
		if(pass != conPass)
		{
			messagebox.innerHTML = 'The passwords are mismatched';

			return this.#ErrorCode.PASSWORDMISMATCH;
		}
		else
		{
			messagebox.innerHTML = 'The password are equal';
		}

		return this.#ErrorCode.SUCCESS;
	}

	usernameLength;
	emailLength;
	passwordMinLength;
	passwordMaxLength;

	/**
	 * Define string lengths for all entities.
	 * @constructor
	 * @param {int} usernameLength user name string length.
	 * @param {int} emailLength email string length.
	 * @param {int} passwordMinLength minimal password length.
	 * @param {int} passwordMaxLength maximal password length.
	 */
	constructor(usernameLength = 24, emailLength = 255, passwordMinLength = 8, passwordMaxLength = 16)
	{
		this.usernameLength = usernameLength;
		this.emailLength = emailLength;
		this.passwordMinLength = passwordMinLength;
		this.passwordMaxLength = passwordMaxLength;
	}

	/**
	 * Check user name for any errors.
	 * @returns {SUCCESS} success.
	 * @returns {USERNAMELENGTH} something's wrong with the length, 
	 * @returns {USERNAMECHARS} one of the special characters is used in the string,
	 */
	checkUsername()
	{
		const usernameinput = document.getElementById('username');

		let username = usernameinput.value;
		let messagebox = document.getElementById('messagebox-username');

		if(username.length <= 0)
		{
			messagebox.innerHTML = 'Required';

			return this.#ErrorCode.USERNAMELENGTH;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		//
		// User name length can't be equal or longer than the given maximum.
		//
		if(username.length >= this.usernameLength)
		{
			username = username.replace(/.$/, '');
			usernameinput.value = username;
			messagebox.innerHTML = 'The username length mustn`t exceed ' + this.usernameLength + ' characters';

			return this.#ErrorCode.USERNAMELENGTH;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		//
		// Disallow the use of special characters in user names.
		//
		if(username.match(/\W/))
		{
			messagebox.innerHTML = 'The username mustn`t contain the following characters: /$-_.+!*\'(),{}|\\^~[]`<>#%";/?:@&=/';

			return this.#ErrorCode.USERNAMECHARS;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		return this.#ErrorCode.SUCCESS;
	}

	/**
	 * Check given email address for any errors.
	 * @returns {SUCCESS} success.
	 * @returns {EMAILLENGTH} something's wrong with the string length.
	 */
	checkEmail()
	{
		const emailinput = document.getElementById('email');

		let email = emailinput.value;
		let messagebox = document.getElementById('messagebox-email');

		if(email.length <= 0)
		{
			messagebox.innerHTML = 'Required';

			return this.#ErrorCode.EMAILLENGTH;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		if(email.length >= this.emailLength)
		{
			email = email.replace(/.$/, '');
			emailinput.value = email;
			messagebox.innerHTML = 'The email length mustn`t excceed ' + this.emailLength + ' characters';

			return this.#ErrorCode.EMAILLENGTH;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		return this.#ErrorCode.SUCCESS;
	}

	/**
	 * Check given password for any errors.
	 * @returns {SUCCESS} success. 
	 * @returns {PASSWORDLENGTH} something's wrong with the string length.
	 */
	checkPassword()
	{
		const passInput = document.getElementById('password');

		let pass = passInput.value;
		let messagebox = document.getElementById('messagebox-password');
		
		if(pass.length <= 0)
		{
			messagebox.innerHTML = 'Required';

			return this.#ErrorCode.PASSWORDLENGTH;;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		//
		// Check whether password length is less than the lower limit or not.
		//
		if(pass.length < this.passwordMinLength)
		{
			messagebox.innerHTML = 'Given password must be longer than ' + this.passwordMinLength + ' characters';

			return this.#ErrorCode.PASSWORDLENGTH;;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		//
		// Check whether password length is more than the set upper limit or not.
		//
		if(pass.length >= this.passwordMaxLength)
		{
			pass = pass.replace(/.$/, '');
			passInput.value = pass;
			messagebox.innerHTML = 'The password length mustn`t excceed ' + this.passwordMaxLength + ' characters';

			return this.#ErrorCode.PASSWORDLENGTH;;
		}
		else
		{
			messagebox.innerHTML = '';
		}

		this.#checkConfirmationPassword();

		return this.#ErrorCode.SUCCESS;
	}

	/**
	 * A meta method which executes all of the error checking methods of this class.
	 * @returns total number of unsuccessful error checks.
	 */
	check()
	{
		let errnum = 0;

		if(this.checkUsername() != this.#ErrorCode.SUCCESS)
		{
			errnum++;
		}

		if(this.checkEmail() != this.#ErrorCode.SUCCESS)
		{
			errnum++;
		}
		
		if(this.checkPassword() != this.#ErrorCode.SUCCESS)
		{
			errnum++;
		}

		if(this.#checkConfirmationPassword() != this.#ErrorCode.SUCCESS)
		{
			errnum++;
		}

		const subBtn = document.getElementById('submission-button');

		if(errnum > 0)
		{
			subBtn.disabled = true;
		}
		else
		{
			subBtn.disabled = false;
		}

		return errnum;
	}
}

let userController = new UserController();
