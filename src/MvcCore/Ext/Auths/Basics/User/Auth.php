<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Auths\Basics\User;

/**
 * Trait for `\MvcCore\Ext\Auths\Basics\User` class. Trait contains:
 * - Static property `$userSessionNamespace` with their public setter and getter with expiration settings.
 * - Static methods `LogIn()` and `LogOut()` to authenticate or remove user from session namespace.
 * - Static method `EncodePasswordToHash()` to hash password with custom or configured salt and other options.
 */
trait Auth
{
	/**
	 * MvcCore session namespace instance
	 * to get/clear username record from session
	 * to load user for authentication.
	 * @var \MvcCore\Session|\MvcCore\ISession
	 */
	protected static $userSessionNamespace = NULL;

	/**
	 * Try to get user model instance from application users list
	 * (it could be database table or system config) by user session namespace
	 * `userName` record if `authenticated` boolean in user session namespace is `TRUE`.
	 * Or return `NULL` for no user by user session namespace records.
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\IUser|NULL
	 */
	public static function SetUpUserBySession () {
		$userSessionNamespace = static::GetUserSessionNamespace();
		$userNameStr = \MvcCore\Ext\Auths\Basics\IUser::SESSION_USERNAME_KEY;
		$authenticatedStr = \MvcCore\Ext\Auths\Basics\IUser::SESSION_AUTHENTICATED_KEY;
		if (
			isset($userSessionNamespace->{$userNameStr}) &&
			isset($userSessionNamespace->{$authenticatedStr}) &&
			$userSessionNamespace->{$authenticatedStr}
		) {
			return static::GetByUserName($userSessionNamespace->{$userNameStr});
		}
		return NULL;
	}

	/**
	 * Try to get user model instance from application users list
	 * (it could be database table or system config) by submitted
	 * and cleaned `$userName`, hash submitted and cleaned `$password` and try to compare
	 * hashed submitted password and user password hash from application users
	 * list. If password hashes are the same, set username and authenticated boolean
	 * into user session namespace. Then user is logged in.
	 * @param string $userName Submitted and cleaned username. Characters `' " ` < > \ = ^ | & ~` are automatically encoded to html entities by default `\MvcCore\Ext\Auths\Basic` sign in form.
	 * @param string $password Submitted and cleaned password. Characters `' " ` < > \ = ^ | & ~` are automatically encoded to html entities by default `\MvcCore\Ext\Auths\Basic` sign in form.
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\IUser|NULL
	 */
	public static function LogIn ($userName = '', $password = '') {
		$user = static::GetByUserName($userName);
		if ($user) {
			$hashedPassword = static::EncodePasswordToHash($password);
			if (hash_equals($user->passwordHash, $hashedPassword)) {
				$userSessionNamespace = & static::GetUserSessionNamespace();
				$userNameStr = \MvcCore\Ext\Auths\Basics\IUser::SESSION_USERNAME_KEY;
				$authenticatedStr = \MvcCore\Ext\Auths\Basics\IUser::SESSION_AUTHENTICATED_KEY;
				$userSessionNamespace->$userNameStr = $user->userName;
				$userSessionNamespace->$authenticatedStr = TRUE;
				return $user;
			}
		}
		return NULL;
	}

	/**
	 * Log out user. Set `authenticated` record in user session namespace to `FALSE`
	 * by default. User name should still remain in user session namespace.
	 * If First argument `$destroyWholeSession` is `TRUE`, destroy whole
	 * user session namespace with `authenticated` bool and with `userName` string record.
	 * @param bool $destroyWholeSession
	 * @return void
	 */
	public static function LogOut ($destroyWholeSession = FALSE) {
		$userSessionNamespace = & static::GetUserSessionNamespace();
		if ($destroyWholeSession) {
			static::GetUserSessionNamespace()->Destroy();
		} else {
			$authenticatedStr = \MvcCore\Ext\Auths\Basics\IUser::SESSION_AUTHENTICATED_KEY;
			$userSessionNamespace->$authenticatedStr = FALSE;
		}
	}

	/**
	 * Get password hash by `password_hash()` with salt
	 * by `\MvcCore\Ext\Auths\Basic` extension configuration or
	 * by custom salt in second agument `$options['salt'] = 'abcdefg';`.
	 * @see http://php.net/manual/en/function.password-hash.php
	 * @param string $password
	 * @param array $options An options for `password_hash()`.
	 * @return string
	 */
	public static function EncodePasswordToHash ($password = '', $options = []) {
		if (!isset($options['salt'])) {
			$configuredSalt = \MvcCore\Ext\Auths\Basic::GetInstance()->GetPasswordHashSalt();
			if ($configuredSalt !== NULL) {
				$options['salt'] = $configuredSalt;
			} else {
				$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
				throw new \InvalidArgumentException(
					'['.$selfClass.'] No option `salt` given by second argument `$options`'
					." or no salt configured by `\MvcCore\Ext\Auth::GetInstance()->SetPasswordHashSalt('...')`."
				);
			}
		}
		if (isset($options['cost']) && ($options['cost'] < 4 || $options['cost'] > 31)) {
			$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
			throw new \InvalidArgumentException(
				'['.$selfClass.'] `cost` option has to be from `4` to `31`, `' . $options['cost'] . '` given.'
			);
		}
		$result = @password_hash($password, PASSWORD_BCRYPT, $options);
		if (!$result || strlen($result) < 60) {
			$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
			throw new \RuntimeException(
				'['.$selfClass.'] Hash computed by `password_hash()` is invalid. Try a little bit longer salt.'
			);
		}
		return $result;
	}

	/**
	 * MvcCore session namespace instance
	 * to get/clear username record from session
	 * to load user for authentication.
	 * Session is automatically started if necessary
	 * by `\MvcCore\Session::GetNamespace();`.
	 * @return \MvcCore\Session|\MvcCore\ISession
	 */
	public static function & GetUserSessionNamespace () {
		if (static::$userSessionNamespace === NULL) {
			$sessionClass = \MvcCore\Application::GetInstance()->GetSessionClass();
			static::$userSessionNamespace = $sessionClass::GetNamespace('MvcCore\Ext\Auths\Basic');
			static::$userSessionNamespace->SetExpirationSeconds(
				\MvcCore\Ext\Auths\Basic::GetInstance()->GetExpirationSeconds()
			);
		}
		return static::$userSessionNamespace;
	}

	/**
	 * Summary of SetUserSessionNamespace
	 * @param \MvcCore\Session|\MvcCore\ISession $userSessionNamespace
	 * @return \MvcCore\Session|\MvcCore\ISession
	 */
	public static function & SetUserSessionNamespace (\MvcCore\ISession & $userSessionNamespace) {
		static::$userSessionNamespace = $userSessionNamespace;
		return $userSessionNamespace;
	}
}
