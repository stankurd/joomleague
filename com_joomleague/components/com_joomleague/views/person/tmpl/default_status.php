<?php

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

$status = $this->projectPerson->status;
$absenceTypesToShow = array();
foreach (array('injury', 'suspension', 'away') as $absenceType)
{
	if (isset($status[$absenceType]->state) && $status[$absenceType]->state > 0)
	{
		$absenceTypesToShow[] = $absenceType;
	}
}

if (count($absenceTypesToShow) > 0): ?>
<h2><?php echo JText::_('COM_JOOMLEAGUE_PERSON_STATUS'); ?></h2>
<table class='status'>
<?php
$today = HTMLHelper::date('now' .' UTC', JText::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
	JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
foreach ($absenceTypesToShow as $absenceType):
	$absenceStatus = $status[$absenceType];
	$prefix = 'COM_JOOMLEAGUE_PERSON_' . strtoupper($absenceType);
	if ($absenceStatus->state):
		$startDate = $this->formattedAbsenceDate($absenceStatus->date, $absenceStatus->from);
		$endDate   = $this->formattedAbsenceDate($absenceStatus->end, $absenceStatus->to);
		if ($absenceStatus->date == $absenceStatus->end): ?>
		<tr>
			<td class='label'>
				<?php echo '&nbsp;&nbsp;' . $this->getEventIconHtml($absenceType, $prefix, array('title' => JText::_($prefix),
							'style' => 'padding-right: 10px; vertical-align: middle;')) .
					JText::_($prefix); ?>
			</td>
			<td  class='data'><?php echo $endDate != $today ? $endDate : ''; ?></td>
		</tr>
		<?php else: ?>
		<tr>
			<td class='label' colspan='2'>
				<?php echo '&nbsp;&nbsp;' . $this->getEventIconHtml($absenceType, $prefix); ?>
			</td>
		</tr>
		<tr>
			<td class='label'><?php echo JText::_($prefix . '_DATE'); ?></td>
			<td class='data'><?php echo $startDate; ?></td>
		</tr>
			<?php if ($endDate != $today): ?>
		<tr>
			<td class='label'><?php echo JText::_($prefix . '_END'); ?></td>
			<td class='data'><?php echo $endDate; ?></td>
		</tr>
			<?php
			endif;
		endif;

		if (!empty($absenceStatus->detail)): ?>
		<tr>
			<td class='label'><?php echo JText::_($prefix . '_TYPE'); ?></td>
			<td class='data'><?php printf('%s', htmlspecialchars($absenceStatus->detail)); ?></td>
		</tr>
		<?php
		endif;
	endif;
endforeach; ?>
</table>
<?php endif; ?>