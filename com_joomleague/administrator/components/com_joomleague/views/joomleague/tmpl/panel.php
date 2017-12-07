<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

$path = 'administrator/components/com_joomleague/assets/images/';
$user = Factory::getUser();
JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_CONTROL_PANEL_TITLE'));
// load navigation menu
$this->addTemplatePath(JPATH_COMPONENT . '/views/joomleague');
?>
<div id="element-box">
	<div class="m">
		<div class="adminform">
			<div><h3><?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTS_CONTROL_PANEL_LEGEND','<i>'.$this->project->name.'</i>'); ?></h3><hr></div>
			<div class="cpanel">
							<?php
							$link = Route::_('index.php?option=com_joomleague&task=project.edit&id=' . $this->project->id.'&return=cpanel');
							$text = JText::_('COM_JOOMLEAGUE_P_PANEL_PSETTINGS');
							$imageFile = 'icon-48-ProjectSettings.png';
							$linkParams = "<span>$text</span>&nbsp;";
							$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
							?>
							<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
							<?php
							$link = Route::_('index.php?option=com_joomleague&view=templates');
							$text = JText::_('COM_JOOMLEAGUE_P_PANEL_FES');
							$imageFile = 'icon-48-FrontendSettings.png';
							$linkParams = "<span>$text</span>&nbsp;";
							$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
							?>
							<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
							<?php
							if((isset($this->project->project_type)) &&
									 (($this->project->project_type == PROJECT_DIVISIONS) || ($this->project->project_type == 'DIVISIONS_LEAGUE')))
							{
								$link = Route::_('index.php?option=com_joomleague&view=divisions');
								$text = JText::plural('COM_JOOMLEAGUE_P_PANEL_DIVISIONS',$this->count_projectdivisions);
								$imageFile = 'icon-48-Divisions.png';
								$linkParams = "<span>$text</span>&nbsp;";
								$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
								?>
								<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
								<?php
							}
							if((isset($this->project->project_type)) &&
									 (($this->project->project_type == 'TOURNAMENT_MODE') || ($this->project->project_type == 'DIVISIONS_LEAGUE')))
							{
								$link = Route::_('index.php?option=com_joomleague&view=treetos');
								$text = JText::_('COM_JOOMLEAGUE_P_PANEL_TREE');
								$imageFile = 'icon-48-Tree.png';
								$linkParams = "<span>$text</span>&nbsp;";
								$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
								?>
								<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
							<?php
							}
							$link = Route::_('index.php?option=com_joomleague&view=projectposition');
							$text = JText::plural('COM_JOOMLEAGUE_P_PANEL_POSITIONS',$this->count_projectpositions);
							$imageFile = 'icon-48-Positions.png';
							$linkParams = "<span>$text</span>&nbsp;";
							$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
							?>
							<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
							<?php
							$link = Route::_('index.php?option=com_joomleague&view=projectreferees');
							$text = JText::plural('COM_JOOMLEAGUE_P_PANEL_REFEREES',$this->count_projectreferees);
							$imageFile = 'icon-48-Referees.png';
							$linkParams = "<span>$text</span>&nbsp;";
							$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
							?>
							<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
							<?php
							$link = Route::_('index.php?option=com_joomleague&view=projectteams');
							$text = JText::plural('COM_JOOMLEAGUE_P_PANEL_TEAMS',$this->count_projectteams);
							$imageFile = 'icon-48-Teams.png';
							$linkParams = "<span>$text</span>&nbsp;";
							$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
							?>
							<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
							<?php
							$link = Route::_('index.php?option=com_joomleague&view=rounds');
							$text = JText::plural('COM_JOOMLEAGUE_P_PANEL_ROUNDS',$this->count_rounds);
							$imageFile = 'icon-48-Rounds.png';
							$linkParams = "<span>$text</span>&nbsp;";
							$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
							?>
							<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
							<?php
							$link = Route::_('index.php?option=com_joomleague&task=jlxmlexport.export');
							$text = JText::_('COM_JOOMLEAGUE_P_PANEL_XML_EXPORT');
							$imageFile = 'icon-48-XMLExportData.png';
							$linkParams = "<span>$text</span>&nbsp;";
							$image = HTMLHelper::_('image',$path . $imageFile,$text) . '<span>' . $text . '</span>';
							?>
							<div class="iconwrapper">
					<div class="cpicon"><?php echo HTMLHelper::link($link,$image); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div id="element-box">
	<div class="m">
		<div class="adminform">
			<div class="cpanel"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_CONTROL_PANEL_HINT'); ?></div>
		</div>
	</div>
</div>
<!-- bottom close main table opened in default_admin -->
</td>
</tr>
</table>
