<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<form name='adminForm' id='adminForm' method='post' action='<?php echo $this->action;?>'>
	<table>
		<tr>
		<?php
		//echo ' [' . $this->startdate. ' / ' . $this->enddate. ']';

		//echo $this->pagenav.'<br>';
		//echo $this->pagenav2.'<br>';

		//echo HTMLHelper::calendar( $this->startdate, 'startdate', 'startdate', $dateformat );
		//echo ' - ' . HTMLHelper::calendar( $this->enddate, 'enddate', 'enddate', $dateformat );
			if ($this->config['use_tabbed_view'] == 0): ?>
			<td>
				<?php echo HTMLHelper::_('select.genericlist', $this->lists['type'], 'type' , 'class="inputbox" size="1"',
						'value', 'text', $this->type ); ?>
			</td>
			<?php endif; ?>

			<td>
				<?php echo HTMLHelper::_('select.genericlist', $this->lists['frommatchday'], 'from' , 'class="inputbox" size="1"',
					'value' ,'text' , $this->from ); ?>
			</td>
			<td>
				<?php echo HTMLHelper::_('select.genericlist', $this->lists['tomatchday'], 'to' , 'class="inputbox" size="1"',
					'value', 'text', $this->to ); ?>
			</td>

			<td>
				<input type='submit' class='button' name='reload View'
					   value="<?php echo Text::_('COM_JOOMLEAGUE_RANKING_FILTER'); ?>">
			</td>
		</tr>
	</table>
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>
<br />

