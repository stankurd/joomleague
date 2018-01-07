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

HTMLHelper::_('behavior.framework');

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
				$('".$control_name.$updates."').onclick = function () { return false;};
				var combo = $('".$control_name.$this->name."');
				var value = combo.options[combo.selectedIndex].value;
				var postStr  = '';
				var url = '".Uri::base()."' + 'index.php?option=com_joomleague&view=".$view."&format=raw&".$this->name."='+value;
				var theAjax = new Ajax(url, {
					method: 'post',
					postBody : postStr
					});
				theAjax.addEvent('onSuccess', function(html) {
					var comboToUpdate = $('".$control_name.$updates."');
					var previousValue = comboToUpdate.selectedIndex>0 ? comboToUpdate.options[comboToUpdate.selectedIndex].value : -1;
					var msie = navigator.userAgent.toLowerCase().match(/msie\s+(\d)/);
					if(msie) {
						comboToUpdate.empty();
						comboToUpdate.outerHTML='<SELECT id=\"".$control_name.$updates."\" name=\"".$control_name."[".$updates."]\">'+html+'</SELECT>';
					}
					else {
						comboToUpdate.empty().set('html',html);
					}
					if(previousValue!=-1){
						for (var i=0; i<comboToUpdate.options.length;i++) {
			 				if (comboToUpdate.options[i].value==previousValue) {
								comboToUpdate.selectedIndex = i;
								break;
			 				}
		  				}
	  				}
				});
				theAjax.request();
			}");
		}
		$html = HTMLHelper::_('select.genericlist',  $db->loadObjectList(), $this->name, 'class="inputbox"'.($updates ? ' onchange="javascript:update_'.$updates.'()"' : '').($depends ? ' onclick="javascript:update_'.$this->name.'()"' : ''), $key, $val, $this->value, $this->name);
		return $html;
	}
}
