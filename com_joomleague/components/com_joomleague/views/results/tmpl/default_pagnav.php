<?php defined('_JEXEC') or die; ?>
<br />
<table width='96%' align='center' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td>
			<?php if (!empty($this->rounds)): ?>
			<div class='pagenav'>
				<?php echo JoomleaguePagination::pagenav($this->project); ?>
			</div>
			<?php endif; ?>
		</td>
	</tr>
</table>
