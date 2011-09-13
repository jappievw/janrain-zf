<?php
/**
 * Handler class for determining the language which fits best by a Zend_Locale.
 */
class Janrain_Handler_Language
{
	/**
	 * Find a default language by a locale, which is accepted by Janrain.
	 * 
	 * @param string|Zend_Locale $locale
	 * @param string $default_language
	 * @return string
	 */
	public static function getDefaultLanguageByLocale($locale, $default_language = 'en')
	{
		/**
		 * Definition of all languages allowed by Janrain.
		 * @link http://documentation.janrain.com/engage/widgets/localization 
		 */ 
		$allowed_languages = array('ar', 'bg', 'cs', 'da', 'de', 'el', 'en', 'es', 'fi', 'fr', 'he', 'hr', 'hu', 'id',
									'it', 'ja', 'lt', 'nb-NO', 'nl', 'nl-BE', 'nl-NL', 'no', 'pl', 'pt', 'pt-BR', 'pt-PT',
									'ro', 'ru', 'sk', 'sl', 'sv', 'sv-SE', 'th', 'zh');
		/**
		 * Step 1: determine the locale to use.
		 */
		if ($locale instanceof Zend_Locale)
		{
			$match_locale = $locale->__toString();
		}
		else
		{
			$match_locale = $locale;
		}
		
		
		/**
		 * Step 1: try to find a language based on the full locale.
		 */
		$check_language = str_replace('_', '-', $match_locale);
		if (in_array($check_language, $allowed_languages))
		{
			return $check_language;
		}
		
		/**
		 * Step 2: try to find a language based on the language part of the locale.
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