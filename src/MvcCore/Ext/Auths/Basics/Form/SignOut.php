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

namespace MvcCore\Ext\Auths\Basics\Form;

/**
 * Trait for class `\MvcCore\Ext\Auths\Basics\SignOutForm`. Trait contains:
 * - Protected property `$user` to display user full name in sign out form.
 * - `Init()` method to initialize all necessary sign in form fields.
 * - `Submit()` method to handle sign-in form submit request (`POST` by default).
 * - `Render()` method to render sign out form without template by default.
 */
trait SignOut
{
	/**
	 * Initialize sign out button and user into
	 * template for any custom template rendering.
	 * @return \MvcCore\Ext\Auths\Basics\SignOutForm
	 */
	public function Init () {
		/** @var $this \MvcCore\Ext\Auths\Basics\SignOutForm */
		parent::Init();

		$this
			->initAuthFormPropsAndHiddenControls()
			->AddField(new \MvcCore\Ext\Forms\Fields\SubmitButton([
				'name'			=> 'send',
				'value'			=> 'Log Out',
				'cssClasses'	=> ['button'],
			]));

		$this->user = $this->auth->GetUser();

		return $this;
	}

	/**
	 * Sign out submit - if everything is OK, unset user unique name from session
	 * for next requests to have not authenticated user anymore.
	 * @param array $rawRequestParams
	 * @return array
	 */
	public function Submit (array & $rawRequestParams = []) {
		parent::Submit($rawRequestParams);
		$data = & $this->values;
		if ($this->result === \MvcCore\Ext\Form::RESULT_SUCCESS) {
			$userClass = $this->auth->GetUserClass();
			$userClass::LogOut();
		}
		$this
			->SetSuccessUrl(isset($data['successUrl']) ? $data['successUrl'] : '')
			->SetErrorUrl(isset($data['errorUrl']) ? $data['errorUrl'] : '');
		return [$this->result, $this->values, $this->errors];
	}

	/**
	 * Render form without custom template, without any errors, with
	 * user full name, sign out button and all necessary hidden fields.
	 * @return string
	 */
	public function Render ($controllerDashedName = '', $actionDashedName = '') {
		$this->PreDispatch();
		$result = $this->RenderBegin();
		if ($this->user)
			$result .= '<span>'.$this->user->GetFullName().'</span>';
		foreach ($this->fields as & $field)
			$result .= $field->Render();
		$result .= $this->RenderEnd();
		return $result;
	}
}
