<?php
// No direct access to this file
use Joomla\CMS\Form\FormRule;

defined('_JEXEC') or die;
 
// import Joomla formrule library
jimport('joomla.form.formrule');
 
/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleSuperiorzero extends FormRule
{
	/**
	 * The regular expression.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $regex = '^[1-9][0-9]*$';
}