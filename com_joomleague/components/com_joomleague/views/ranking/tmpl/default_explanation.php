<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$config   = &$this->tableconfig;
$columns = explode( ',', $config['ordered_columns'] );
$column_names	= explode( ',', $config['ordered_columns_names'] );
if (!empty($columns)): ?>
<br />
<table width='96%' border='0' cellpadding='0' cellspacing='0'>
	<tr class='explanation'>
	<?php
		$d = 0;
		foreach ($columns as $k => $column):
			if (empty($column_names[$k]))
			{
				$column_names[$k] = '???';
			}
			$c = 'COM_JOOMLEAGUE_' . strtoupper(trim($column));
			?>
		<td class='col<?php echo $d; ?>'>
			<b><?php echo $column_names[$k]; ?></b> = <?php echo Text::_($c); ?>
		</td>
			<?php
			$d = 1 - $d;
		endforeach; ?>
	</tr>
</table>
<?php endif; ?>