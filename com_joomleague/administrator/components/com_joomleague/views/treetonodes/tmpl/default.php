<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;
HTMLHelper::_('behavior.tooltip');

$istree = $this->treeto->tree_i;
$isleafed = $this->treeto->leafed;

?>
<div id="editcell">
	<fieldset class="adminform">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TREETONODES_LEGEND_','<i>'.$this->project->name.'</i>'); ?></legend>
		<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
		<table>
<?php
$style = $this->style;
$path = $this->path;

$dl = HTMLHelper::_('image',$path . 'treedl.gif','width="16px"','height="18px"');
$ul = HTMLHelper::_('image',$path . 'treeul.gif','width="16px"','height="18px"');
$cl = HTMLHelper::_('image',$path . 'treecl.gif','width="16px"','height="18px"');
$dr = HTMLHelper::_('image',$path . 'treedr.gif','width="16px"','height="18px"');
$ur = HTMLHelper::_('image',$path . 'treeur.gif','width="16px"','height="18px"');
$cr = HTMLHelper::_('image',$path . 'treecr.gif','width="16px"','height="18px"');
$p = HTMLHelper::_('image',$path . 'treep.gif','width="16px"','height="18px"');
$h = HTMLHelper::_('image',$path . 'treeh.gif','width="16px"','height="18px"');

$i = $this->treeto->tree_i; // depth
$r = 2 * (pow(2,$i)); // rows
$c = 2 * $i + 1; // columns
$col_hide = $c - 2 * ($this->treeto->hide); // tournament with multiple
                                            // winners
echo '<table>';

if ($this->items) {
	for($j = 1;$j < $r;$j ++)
	{
		if($this->items[$j - 1]->published == 0) // hide rows
		{
			;
		}
		else
		{
			echo '<tr>';
			echo '<td height=18px></td>';
			for($k = 1;$k <= $c;$k ++)
			{
				if($k > $col_hide) // hide columns
				{
					;
				}
				else
				{
					echo '<td ';
					for($w = 0;$w <= $i;$w ++)
					{
						if(($k == (1 + ($w * 2))) && ($j % (2 * (pow(2,$w))) == (pow(2,$w))))
						{
							echo "$style";
						}
					}
					echo ' >';

					for($w = 0;$w <= $i;$w ++)
					{
						if(($k == (1 + ($w * 2))) && ($j % (2 * (pow(2,$w))) == (pow(2,$w))))
						{
							// node
							// __________________________________________________________________________________________________
							$checked = HTMLHelper::_('grid.checkedout',$this->items[$j - 1],$j - 1);
							if($isleafed == 1)
							{
								echo $this->items[$j - 1]->node;
								if($this->items[$j - 1]->team_id)
								{
									$link = Route::_('index.php?option=com_joomleague&task=treetonode.edit&id=' . $this->items[$j - 1]->id);
									$ednode = '<a href=' . $link . '>';
									$ednode .= HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png','edit');
									$ednode .= '</a>';
									echo $ednode;
									echo $this->items[$j - 1]->team_name;
									$link3 = Route::_(
											'index.php?option=com_joomleague&view=treetomatches&nid[]=' . $this->items[$j - 1]->id . '&tid[]=' .
											$this->treeto->id);
									$match = '<a href=' . $link3 . '>';
									$match .= HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/matches.png','edit');
									$match .= '</a>';
									echo $match;
									$link4 = Route::_('index.php?option=com_joomleague&task=treetomatches.editlist&nid[]=' . $this->items[$j - 1]->id);
									$matchas = '<a href=' . $link4 . '>';
									$matchas .= HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/import.png','assign');
									$matchas .= '</a>';
									echo $matchas;
								}
								else
								{
									echo $checked;
									$append = '';
									if($this->items[$j - 1]->team_id == 0)
									{
										$append = ' style="background-color:#bbffff"';
									}
									echo HTMLHelper::_('select.genericlist',$this->lists['team'],'team_id' . $this->items[$j - 1]->id,
											'class="inputbox select-hometeam" size="1"' . $append,'value','text',$this->items[$j - 1]->team_id);
								}
							}
							else
							{
								if($this->items[$j - 1]->is_leaf == 1)
								{
									echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/settings.png','leaf');
									;
								}
								else
								{
									echo $checked;
								}
							}
							// node
							// end_________________________________________________________________________________________________
						}
						elseif(($k == (2 + ($w * 2))) && ($j % (4 * (pow(2,$w))) == (pow(2,$w))))
						{
							echo "$dl";
						}
						elseif(($k == (2 + ($w * 2))) && ($j % (4 * (pow(2,$w))) == (2 * (pow(2,$w)))))
						{
							if($this->items[$j - 1]->is_leaf == 1)
							{
								;
							}
							else
							{
								echo "$cl";
							}
						}
						elseif(($k == (2 + ($w * 2))) && ($j % (4 * (pow(2,$w))) == (3 * (pow(2,$w)))))
						{
							echo "$ul";
						}
						elseif(($k == (2 + ($w * 2))) && (($j % (4 * (pow(2,$w))) > (pow(2,$w))) && ($j % (4 * (pow(2,$w))) < (3 * (pow(2,$w))))))
						{
							echo "$p";
						}
						else
						{
							;
						}
					}
					// }
					echo '</td>';
				}
			}
			echo '</tr>';
		}
	}
}

?>
	</table>
	</fieldset>
</div>
	<input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="tree_i" value="<?php echo $this->treeto->tree_i; ?>" />
	<input type="hidden" name="treeto_id" value="<?php echo $this->treeto->id; ?>" />
	<input type="hidden" name="global_fake" value="<?php echo $this->treeto->global_fake; ?>" />
	<input type="hidden" name="global_known" value="<?php echo $this->treeto->global_known; ?>" />
	<input type="hidden" name="global_matchday" value="<?php echo $this->treeto->global_matchday; ?>" />
	<input type="hidden" name="global_bestof" value="<?php echo $this->treeto->global_bestof; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
