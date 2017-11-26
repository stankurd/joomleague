<?php


use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

abstract class JHtmlIcon {

/**
 * Display an edit icon
 */
public static function edit($project_id,$item,$item_id,$task,$view=false,$params=false)
{
	$user = Factory::getUser();
	$uri  = Uri::getInstance();

	JHtml::_('bootstrap.tooltip');

	// Show checked_out icon if the article is checked out by a different user
	if (property_exists($item, 'checked_out')
			&& property_exists($item, 'checked_out_time')
			&& $item->checked_out > 0
			&& $item->checked_out != $user->get('id'))
	{
		$checkoutUser = Factory::getUser($item->checked_out);
		$date         = JHtml::_('date', $item->checked_out_time);
		$tooltip      = JText::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . JText::_('JLIB_HTML_CHECKED_OUT');
		
		$button = JHtml::_('image', 'system/checked_out.png', null, null, true);
		$text   = '<span class="hasTooltip" title="' . JHtml::tooltipText($tooltip . '', 0) . '">'
					. $button . '</span> ';
		
		// @todo: decide if checked_out should be visible
		$output = $text;

		return $output;
	}
	if ($view == 'teaminfo') {
		$url 	= 'index.php?option=com_joomleague&task='.$task.'&a_id=' . $item_id . '&pid='.$project_id.'&ptid='.$item_id.'&return=' . base64_encode($uri);
	} else {
		$url 	= 'index.php?option=com_joomleague&task='.$task.'&a_id=' . $item_id . '&return=' . base64_encode($uri);
	}

	$text 	= JHtml::_('image', 'com_joomleague/edit.png', JText::_('JGLOBAL_EDIT'), null, true);
	$output = JHtml::_('link', JRoute::_($url), $text);

	return $output;
}

}