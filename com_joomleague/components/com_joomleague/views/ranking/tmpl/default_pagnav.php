<?php defined('_JEXEC') or die; ?>
<!-- matchdays pageNav -->
<br />
<table width='96%' border='0' cellpadding='0' cellspacing='0'>
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
<!-- matchdays pageNav END -->