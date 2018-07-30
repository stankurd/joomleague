<?php
/**
 * @package     Joomleague
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * override for default layout
 */

defined('JPATH_BASE') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

if (is_array($data['options']))
{
	$data['options'] = new Registry($data['options']);
}

// Options
$searchButton = $data['options']->get('searchButton', true);

$filters = $data['view']->filterForm->getGroup('filter');
?>
<?php if (!empty($filters['filter_search'])) : ?>
		<label for="filter_search" class="element-invisible">
			<?php echo Text::_('JSEARCH_FILTER'); ?>
		</label>
		<div class="btn-wrapper input-append pull-left">
			<?php echo $filters['filter_search']->input; ?>
			<?php if ($filters['filter_search']->description) : ?>
				<?php HTMLHelper::tooltip('#filter_search', array('title' => Text::_($filters['filter_search']->description))); ?>
			<?php endif; ?>
			<button type="submit" class="btn hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
				<span class="icon-search"></span>
			</button>
			<button type="button" class="btn hasTooltip js-stools-btn-clear" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
				<span class="icon-remove"></span>
			</button>
		</div>
<?php endif;
