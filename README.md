janrain-zf: Zend Framework library for the Janrain Engage API
=============================================================

Links
-----
 - [Janrain Documentation](http://documentation.janrain.com/)
 - [Zend Framework](http://framework.zend.com/)

Example
--------

	<?php
	class AuthController extends Zend_Controller_Action
	{
		/**
		 * Login page for this user.
		 */
		public function loginAction()
		{
			/**
			 * The login action doesn't need any specific logic. In the viewscript
			 * only the Janrain modal overlay or embed code with iframe needs to be
			 * embedded. To get the html snippet, go to:
			 *  - http://rpxnow.com
			 *  - Sign in with your account.
			 *  - Click Deployment -> Sign-In for Web
			 *  - Fill in the url to the callbackAction below.
			 *  - Generate the code and place it in your viewscripts.
			 */
		}
		
		/**
		 * Callback action for authentication through Janrain.
		 * The token parameter is in the _POST var array.
		 */
		public function callbackAction()
		{
			/**
			 * Grab the token from the request.
			 */
			$token = null;
			if (isset($_POST['token']))
			{
				$token = $_POST['token'];
			}
			
			/**
			 * When the token is not posted with this request, the user
			 * cannot be logged in.
			 */
			if (!isset($token))
			{
				// log and handle this error.
				throw new Exception('No token parameter specified.');
			}
			
			/**
			 * Verify the token with Janrain by requesting the auth_info.
			 * Internally the Zend_Http_Client is used. You can override
			 * any default setting.
			 */
			$zend_http_client_options = array();
			$janrain_api = new Janrain_Api('<your-api-key>', $zend_http_client_options);
			try
			{
				$auth_info = $janrain_api->getAuthInfo($token, true);
			}
			catch (Janrain_Exception $e)
			{
				/**
				 * Three types of exceptions can be thrown here:
				 *  1. Janrain_Exception_Request
				 *     When an error occurred during the request.
				 *  2. Janrain_Exception_Response
				 *     When the response is not conform the Janrain documentation.
				 *  3. Janrain_Exception_Api
				 *     When the api returned an error (functional error).
				 */
				// log and handle this error.
			}
			
			/**
			 * OPTIONAL: user mapping
			 * Janrain offers a feature to map their users to users within your platform.
			 * This can speed up the lookup of the user drastically. Connect the Janrain
			 * primaryKey to your user_id. The mapping of users is bound to one specific 
			 * account of Janrain and one environment of your platform! For example only
			 * production, but not staging and testing environments.
			 * 
			 * The mapping process works as follows. When a user is logging in:
			 *  1. When the mapping feature can be used and a primaryKey is supplied
			 *      -> lookup the user based on the primaryKey / user_id.
			 *  2. When the mapping feature can NOT be used
			 *      -> lookup the user based on the identifier.
			 *  3. When still no user was found
			 *      -> create a new user.
			 *      -> when mapping feature available: map the user.
			 */
			$use_map_feature = ('production' === APPLICATION_ENV);
			$primary_key     = (isset($auth_info->profile->primaryKey)) ? $auth_info->profile->primaryKey : null;
			$primary_key     = (isset($auth_info->profile->identifier)) ? $auth_info->profile->identifier : null;
			$is_new_user     = false;
			$user_object     = null;
			
			/**
			 * Option 1: find user by primary key.
			 */
			if ($use_map_feature && $primary_key)
			{
				// Fetch $user_object from database by user_id.
			}
			
			/**
			 * Option 2: find the user by identifier.
			 * When the user doesn't exist, it is false instead of null...
			 */
			if (!is_null($snuser_obj))
			{
				// Fetch $user_object from database by identifier.
			}
			
			/**
			 * Option 3: new user.
			 */
			if (!is_null($snuser_obj))
			{
				// Create a new $user_object in the database.
				$is_new_user = true;
			}
			
			/**
			 * Time to update the SnUser with all possibly updated information.
			 */
			if (!$is_new_user)
			{
				// Update the user with probably new info.
			}
			
			/**
			 * Map the local user to the Janrain identifier.
			 */
			if ($use_map_feature && !$primary_key)
			{
				try
				{
					$map_result = $janrain_api->setMap($user_object->identifier, $user_object->user_id);
				}
				catch (Janrain_Exception $e)
				{
					// log and handle this error.
				}
			}
			
			/**
			 * Log the user in into the current session.
			 * This is a kindof dirty way, but it works.
			 */
			Zend_Auth::getInstance()->getStorage()->write($user_object);
			
			/**
			 * User is logged in, handle redirection to a url which required
			 * authentication or to the frontpage.
			 */
		}
		
		/**
		 * Logs the current user out of the platform.
		 */
		public function logoutAction()
		{
			$auth = Zend_Auth::getInstance()->clearIdentity();
			
			/**
			 * User is logged out, handle redirection to the frontpage or so.
			 */
		}
		
		/**
		 * Find a default language by a locale, which is accepted by Janrain.
		 * 
		 * @param Zend_Locale $locale
		 * @return string
		 */
		private function _getDefaultLanguageByLocale(Zend_Locale $locale)
		{
			/**
			 * Definition of all languages allowed by Janrain.
			 * @link http://documentation.janrain.com/engage/widgets/localization 
			 */ 
			$allowed_languages = array(
				'ar', 'bg', 'cs', 'da', 'de', 'el', 'en', 'es', 'fi', 'fr', 'he',
				'hr', 'hu', 'id', 'it', 'ja', 'lt', 'nb-NO', 'nl', 'nl-BE', 'nl-NL',
				'no', 'pl', 'pt', 'pt-BR', 'pt-PT', 'ro', 'ru', 'sk', 'sl', 'sv',
				'sv-SE', 'th', 'zh'
			);
			$default_language = 'en';
			
			/**
			 * Step 1: try to find a language based on the full locale.
			 */
			$check_language = str_replace('_', '-', $locale->__toString());
			if (in_array($check_language, $allowed_languages))
			{
				return $check_language;
			}
			
			/**
			 * Step 2: try to find a language based on the language part 
			 * of the locale.
			 */
			$check_language = substr($check_language, 0, 2);
			if (in_array($check_language, $allowed_languages))
			{
				return $check_language;
			}
			
			/**
			 * Step 3: no language found. return the default language.
			 */
			return $default_language;
		}
	}

Compatibility
-------------
The library is currently in use in a modular ZF project. The environment consists of:

 - Zend Framework 1.11.4
 - PHP 5.3.6, should work with any 5.3.*