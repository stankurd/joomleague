<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla formrule library
jimport('joomla.form.formrule');
 
/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleTime
{
	/**
	 * The regular expression.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $regex = '^[0-9]{1,2}:[0-9]{1,2}$';
	protected $modifiers;
	
	public function test($element, $value, $group = null, $input = null, $form = null)
	{
		if ($value == null or $value == '') {
			return true;
		}
		
		// Check for a valid regex.
		if (empty($this->regex))
		{
			throw new UnexpectedValueException(sprintf('%s has invalid regex.', get_class($this)));
		}

		// Add unicode property support if available.
		if (JCOMPAT_UNICODE_PROPERTIES)
		{
			$this->modifiers = (strpos($this->modifiers, 'u') !== false) ? $this->modifiers : $this->modifiers . 'u';
		}

		// Test the value against the regular expression.
		if (preg_match(chr(1) . $this->regex . chr(1) . $this->modifiers, $value))
		{
			return true;
		}

		return false;
	}
}