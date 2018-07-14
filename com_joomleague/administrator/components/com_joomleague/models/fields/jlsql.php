<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

//HTMLHelper::_('behavior.framework');

/**
 * Renders a Dynamic SQL element
 */

class JFormFieldJLSQL extends FormField
{
	/**
	 * Element name
	 *
	 * @accessprotected
	 * @varstring
	 */
	protected $type = 'JLSQL';

	function getInput() {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$db->setQuery($this->elements['query']);
		$key = ($this->elements['key_field'] ? $this->elements['key_field'] : 'value');
		$val = ($this->elements['value_field'] ? $this->elements['value_field'] : $this->name);
		$doc = Factory::getDocument();
		$updates = $this->elements['updates'];
		$depends = $this->elements['depends'];
		if($updates){
			$view = $this->elements['rawview'];
			$doc->addScriptDeclaration("function update_".$updates."()
			{
				document.getElementById('".$control_name.$updates."').onclick = function () { return false;};
				var combo = document.getElementById('".$control_name.$this->name."');
				var value = jQuery(combo).options[jQuery(combo).selectedIndex].val();
				var postStr  = '';
				var url = '".Uri::base()."' + 'index.php?option=com_joomleague&view=".$view."&format=raw&".$this->name."='+value;
				var jqXhr = jQuery.ajax({
					url : url,
					method: 'post',
					postBody : postStr
					success:  function((html) {
					var comboToUpdate = document.getElementById ('".$control_name.$updates."');
					var previousValue = jQuery(comboToUpdate).selectedIndex>0 ? jQuery(comboToUpdate).options[jQuery(comboToUpdate).selectedIndex].val() : -1;
					var msie = navigator.userAgent.toLowerCase().match(/msie\s+(\d)/);
					if(msie) {
						jQuery(comboToUpdate).empty();
						jQuery(comboToUpdate).outerHTML='<SELECT id=\"".$control_name.$updates."\" name=\"".$control_name."[".$updates."]\">'+html+'</SELECT>';
					}
					else {
						jQuery(comboToUpdate).empty().set('html',html);
					}
					if(previousValue!=-1){
						for (var i=0; i<jQuery(comboToUpdate).options.length;i++) {
			 				if (jQuery(comboToUpdate).options[i].val()==previousValue) {
								jQuery(comboToUpdate).selectedIndex = i;
								break;
			 				}
		  				}
	  				}
				});
				//theAjax.request();
					console.log(jqXhr); 
			}");
		}
		$html = HTMLHelper::_('select.genericlist',  $db->loadObjectList(), $this->name, 'class="inputbox"'.($updates ? ' onchange="javascript:update_'.$updates.'()"' : '').($depends ? ' onclick="javascript:update_'.$this->name.'()"' : ''), $key, $val, $this->value, $this->name);
		return $html;
	}
}
