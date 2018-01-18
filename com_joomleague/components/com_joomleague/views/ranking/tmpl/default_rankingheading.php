<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$columns		= explode(',', $this->config['ordered_columns']);
$column_names	= explode(',', $this->config['ordered_columns_names']);
$colspan = $this->config['use_background_row_color'] == 0
	? $this->tableconfig['last_ranking'] == 1 ? 4 : 2
	: $this->tableconfig['last_ranking'] == 1 ? 3 : 1;
?>
<thead>
	<tr class='sectiontableheader'>
		<th class='rankheader' colspan='<?php echo $colspan; ?>'>
			<?php JoomleagueHelperHtml::printColumnHeadingSort(Text::_('COM_JOOMLEAGUE_RANKING_POSITION'), 'rank',
				$this->config, 'ASC'); ?>
		</th>

		<?php if ($this->config['show_picture'] != 'no_logo'): ?>
		<th style='text-align:center;'>&nbsp;</th>
		<?php endif; ?>

		<th class='teamheader'>
			<?php JoomleagueHelperHtml::printColumnHeadingSort(Text::_('COM_JOOMLEAGUE_RANKING_TEAM'), 'name',
				$this->config, 'ASC'); ?>
		</th>

	<?php foreach ($columns as $k => $column):
		if (empty($column_names[$k]))
		{
			$column_names[$k]='???';
		}
		$c = 'COM_JOOMLEAGUE_' . strtoupper(trim($column));
		$toolTipTitle = $column_names[$k];
		$toolTipText = Text::_($c);
		?>
		<th class='headers'>
			<span class='hasTip' title='<?php echo $toolTipTitle; ?>::<?php echo $toolTipText; ?>'>
		<?php
		switch (trim(strtoupper($column)))
		{
			case 'PLAYED':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'played', $this->config);
				break;
			case 'WINS':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'won', $this->config);
				break;
			case 'TIES':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'draw', $this->config);
				break;
			case 'LOSSES':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'loss', $this->config);
				break;
			case 'WOT':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'wot', $this->config);
				break;
			case 'WSO':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'wso', $this->config);
				break;
			case 'LOT':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'lot', $this->config);
				break;
			case 'LSO':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'lso', $this->config);
				break;
			case 'WINPCT':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'winpct', $this->config);
				break;
			case 'GB':
				echo $column_names[$k];
				break;
			case 'LEGS':
				echo $column_names[$k];
				break;
			case 'LEGS_DIFF':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'legsdiff', $this->config);
				break;
			case 'LEGS_RATIO':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'legsratio', $this->config);
				break;
			case 'SCOREFOR':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'goalsfor', $this->config);
				break;
			case 'SCOREAGAINST':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'goalsagainst', $this->config);
				break;
			case 'SCOREPCT':
				echo $column_names[$k];
				break;
			case 'RESULTS':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'goalsp', $this->config);
				break;
			case 'DIFF':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'diff', $this->config);
				break;
			case 'POINTS':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'points', $this->config);
				break;
			case 'NEGPOINTS':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'negpoints', $this->config);
				break;
			case 'OLDNEGPOINTS':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'negpoints', $this->config);
				break;
			case 'POINTS_RATIO':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'pointsratio', $this->config);
				break;
			case 'BONUS':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'bonus', $this->config);
				break;
			case 'START':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'start', $this->config);
				break;
			case 'QUOT':
				JoomleagueHelperHtml::printColumnHeadingSort($column_names[$k], 'quot', $this->config);
				break;
			case 'TADMIN':
				echo $column_names[$k];
				break;
			case 'GFA':
				echo $column_names[$k];
				break;
			case 'GAA':
				echo $column_names[$k];
				break;
			case 'PPG':
				echo $column_names[$k];
				break;
			case 'PPP':
				echo $column_names[$k];
				break;
			case 'LASTGAMES':
				echo $column_names[$k];
				break;
			default:
				echo Text::_($column);
				break;
		}
		?>
			</span>
		</th>
	<?php endforeach; ?>
	</tr>
</thead>