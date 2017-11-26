<?php
use Joomla\CMS\Language\Language;

class JLLanguage extends Language
{
	public function getProperties($public = true)
	{
		$vars = get_object_vars($this);
		if ($public)
		{
			foreach ($vars as $key => $value)
			{
				if ('_' == substr($key, 0, 1))
				{
					unset($vars[$key]);
				}
			}
		}

		return $vars;
	}

	public function setLanguage($lang) {
		$self = $lang;
	}
}