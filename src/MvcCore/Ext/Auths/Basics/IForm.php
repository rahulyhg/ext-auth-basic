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
 * Responsibility - base authentication signin/signout form class specification.
 */
interface IForm
{
	/**
	 * Default html `<form>` element id for authentication sign in form.
	 */
	const HTML_ID_SIGNIN = 'authentication_signin';

	/**
	 * Default html `<form>` element id for authentication sign out form.
	 */
	const HTML_ID_SIGNOUT = 'authentication_signout';

	/**
	 * Set form id, required to configure.
	 * Form id is used to identify session data, error messages,
	 * csrf tokens, html form attribute id value and much more.
	 * @requires
	 * @param string $id
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetId ($id);

	/**
	 * Set form submitting url value.
	 * It could be relative or absolute, anything
	 * to complete classic html form attribute `action`.
	 * @requires
	 * @param string $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetAction ($url = NULL);

	/**
	 * Set form http submitting method.
	 * `POST` by default.
	 * @param string $method
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetMethod ($method = \MvcCore\Ext\Forms\IForm::METHOD_POST);

	/**
	 * Set form HTML element css classes strings.
	 * All previously defined css classes will be removed.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * You can define css classes as single string, more classes separated 
	 * by space or you can define css classes as array with strings.
	 * @param string|\string[] $cssClasses
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetCssClasses ($cssClasses);

	/**
	 * Set success url string, relative or absolute, to specify, where
	 * to redirect user after form submitted successfully.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @param string $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetSuccessUrl ($url = NULL);

	/**
	 * Set error url string, relative or absolute, to specify, where
	 * to redirect user after form not submitted successfully.
	 * It's not required to use `\MvcCore\Ext\Form` like this, but if you want to use method
	 * `$form->SubmittedRedirect();` at the end of custom `Submit()` method implementation,
	 * you need to specify at least success and error url strings.
	 * @param string $url
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetErrorUrl ($url = NULL);

	/**
	 * Set translator callable to translate everything visible in form.
	 * Handler is necessary to design with first param to be a translation key,
	 * second param to be a language code and hadler has to return translated string result.
	 * This property is optional to configure, but if it is configured to any callable,
	 * everything in form will be translated, except fields strictly defined to not translate.
	 * Default value is `NULL`, it means no translation will be processed.
	 * @param callable $handler
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & SetTranslator (callable $translator = NULL);

	/**
	 * Initialize the form, check if form is initialized or not and do it only once.
	 * Check if any form id exists and initialize translation boolean for better field initializations.
	 * This is template method. To define any fields in custom `\MvcCore\Ext\Form` class extension,
	 * do it in `Init()` method and call `parent::Init();` as first line inside your custom `Init()` method.
	 * @throws \RuntimeException No form id property defined.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function Init ();

	/**
	 * Add configured form field instance.
	 * @param \MvcCore\Ext\Forms\IField $field
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & AddField (\MvcCore\Ext\Forms\IField $field);

	/**
	 * Add multiple configured form field instances,
	 * function have infinite params with new field instances.
	 * @param \MvcCore\Ext\Forms\IField[] $fields,... Any `\MvcCore\Ext\Forms\IField` instance to add into form.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & AddFields ();

	/**
	 * Process standard low level submit process.
	 * If no params passed as first argument, all params from object
	 * `\MvcCore\Application::GetInstance()->GetRequest()` are used.
	 * - If fields are not initialized - initialize them by calling `$form->Init();`.
	 * - Check max. post size by php configuration if form is posted.
	 * - Check cross site request forgery tokens with session tokens.
	 * - Process all field values and their validators and call `$form->AddError()` where necessary.
	 *	 `AddError()` method automaticly switch `$form->Result` property to zero - `0`, it means error submit result.
	 * Return array with form result, safe values from validators and errors array.
	 * @param array $rawRequestParams optional
	 * @return array Array to list: `array($form->Result, $form->Data, $form->Errors);`
	 */
	public function Submit (array & $rawRequestParams = []);

	/**
	 * Clear all session records for this form by form id.
	 * Data sended from last submit, any csrf tokens and any errors.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	public function & ClearSession ();

	/**
	 * Call this function in custom `\MvcCore\Ext\Form::Submit();` method implementation
	 * at the end of custom `Submit()` method to redirect user by configured success/error/next
	 * step url address into final place and store everything into session.
	 * @return void
	 */
	public function SubmittedRedirect ();

	/**
	 * Alias for method `\MvcCore\Ext\Form::Render();`.
	 * @see `\MvcCore\Ext\Form::Render();`
	 * @return string
	 */
	public function __toString ();

	/**
	 * Render form into string to display it.
	 * - If form is not initialized, there is automaticly
	 *   called `$form->Init();` method.
	 * - If form is not prepared for rendering, there is
	 *   automaticly called `$form->prepareForRendering();` method.
	 * - Create new form view instance and set up the view with local
	 *   context variables.
	 * - Render form naturaly or by custom template.
	 * - Clean session errors, because errors shoud be rendered
	 *   only once, only when it's used and it's now - in this rendering process.
	 * @return string
	 */
	public function Render ($controllerDashedName = '', $actionDashedName = '');
}
