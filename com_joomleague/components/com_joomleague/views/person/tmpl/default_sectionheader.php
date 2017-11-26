<?php use Joomla\CMS\Component\ComponentHelper;

defined('_JEXEC') or die; ?>
<table width='100%' class='contentpaneopen'>
	<tr>
		<td class='contentheading'>
			<?php
			echo $this->pageTitle;

			if ($this->showEditIcon)
			{
				$editInfo = $this->getEditLinkAndImage();
				$modalheight = ComponentHelper::getParams('com_joomleague')->get('modal_popup_height', 600);
				$modalwidth = ComponentHelper::getParams('com_joomleague')->get('modal_popup_width', 900);
				?>
				<a rel="{handler: \'iframe\',size: {x:'<?php echo $modalwidth; ?>',y:'<?php echo $modalheight; ?>'}}"
				   href="'<?php echo $editInfo->link; ?>'" class='modal'>
					<?php echo $editInfo->image; ?>
				</a>
				<?php
			}

			if (isset($this->projectPerson->status))
			{
				$status = $this->projectPerson->status;
				if (isset($status['injury']->state) && $status['injury']->state)
				{
					echo "&nbsp;&nbsp;" . $this->getEventIconHtml('injury', 'COM_JOOMLEAGUE_PERSON_INJURY');
				}

				if (isset($status['suspension']->state) && $status['suspension']->state)
				{
					echo "&nbsp;&nbsp;" . $this->getEventIconHtml('suspension', 'COM_JOOMLEAGUE_PERSON_SUSPENSION');
				}

				if (isset($status['away']->state) && $status['away']->state)
				{
					echo "&nbsp;&nbsp;" . $this->getEventIconHtml('away', 'COM_JOOMLEAGUE_PERSON_AWAY');
				}
			}
			?>
		</td>
	</tr>
</table>
