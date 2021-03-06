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

namespace MvcCore\Ext\Auths\Basics;

/**
 * Responsibility - base user model class.
 * This class is necessary to extend and implement method
 * `GetByUserName()` at least to be able to login into your app.
 */
class User
	extends		\MvcCore\Model
	implements	\MvcCore\Ext\Auths\Basics\IUser
{
	use \MvcCore\Ext\Auths\Basics\User\Base;
	use \MvcCore\Ext\Auths\Basics\UserAndRole\Base;
	use \MvcCore\Ext\Auths\Basics\UserAndRole\Permissions;
	use \MvcCore\Ext\Auths\Basics\User\Auth;
	use \MvcCore\Ext\Auths\Basics\User\Roles;

	/**
	 * Do not automatically initialize protected properties
	 * `$user->db`, `$user->config` and `$user->resource`.
	 * @var bool
	 */
	protected $autoInit = FALSE;

	/**
	 * Get user model instance from database or any other users list
	 * resource by submitted and cleaned `$userName` field value.
	 * @param string $userName Submitted and cleaned username. Characters `' " ` < > \ = ^ | & ~` are automatically encoded to html entities by default `\MvcCore\Ext\Auths\Basic` sign in form.
	 * @throws \RuntimeException
	 * @return \MvcCore\Ext\Auths\Basics\User|\MvcCore\Ext\Auths\Basics\IUser
	 */
	public static function & GetByUserName ($userName) {
		$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
		throw new \RuntimeException(
			'['.$selfClass.'] Method is not implemented. '
			.'Use class `\MvcCore\Ext\Auths\Basics\Users\Database` or '
			.'`\MvcCore\Ext\Auths\Basics\Users\SystemConfig` instead or extend '
			.'class `'.$selfClass.'` and implement method `GetByUserName($userName)` by your own.'
		);
	}
}
