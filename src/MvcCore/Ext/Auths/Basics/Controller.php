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
 * Responsibility - handle configured sign-in form and sign-out form submit requests.
 */
class Controller
	extends		\MvcCore\Controller
	implements	\MvcCore\Ext\Auths\Basics\IController,
				\MvcCore\IController
{
	use			\MvcCore\Ext\Auths\Basics\Controller\Base;
}
