<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<!-- person data START -->
<?php if ($this->person): ?>
<h2><?php echo Text::_('COM_JOOMLEAGUE_PERSON_PERSONAL_DATA'); ?></h2>
<table class='plgeneralinfo table'>
	<tr>
		<?php if ($this->config['show_photo'] == 1): ?>
		<td class='picture'><?php echo $this->getPicture(); ?></td>
		<?php endif; ?>

		<td class='info'>
			<table class='plinfo'>
				<?php if (!empty($this->person->country) && $this->config['show_nationality'] == 1): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_NATIONALITY'); ?></td>
					<td class='data'><?php echo $this->getNationality(); ?></td>
				</tr>
				<?php endif;?>

				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_NAME'); ?></td>
					<td class='data'><?php echo $this->formattedName(); ?></td>
				</tr>

				<?php if (!empty($this->person->nickname)): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_NICKNAME'); ?></td>
					<td class='data'><?php echo $this->person->nickname; ?></td>
				</tr>
				<?php
				endif;

				if ($this->config[ 'show_birthday' ] > 0 && $this->config['show_birthday'] < 5 &&
					$this->person->birthday != '0000-00-00'):
//					$this->config['show_birthday'] = 4;
					?>
				<tr>
					<td class='label'><?php echo $this->birthDayTitle(); ?></td>
					<td class='data'><?php echo $this->formattedBirthDay(); ?></td>
				</tr>
					<?php
				endif;

				if ($this->person->address != '' && $this->config['show_person_address'] == 1): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_ADDRESS'); ?></td>
					<td class='data'><?php echo $this->formattedAddress(); ?></td>
				</tr>
				<?php
				endif;

				if ($this->person->phone != '' && $this->config['show_person_phone'] == 1): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_PHONE'); ?></td>
					<td class='data'><?php echo $this->person->phone; ?></td>
				</tr>
				<?php
				endif;

				if ($this->person->mobile != '' && $this->config['show_person_mobile'] == 1): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_MOBILE'); ?></td>
					<td class='data'><?php echo $this->person->mobile; ?></td>
				</tr>
				<?php
				endif;

				if ($this->config['show_person_email'] == 1 && $this->person->email != ''): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_EMAIL'); ?></td>
					<td class='data'><?php echo $this->formattedEmail(); ?></td>
				</tr>
				<?php
				endif;

				if ($this->person->website != '' && $this->config['show_person_website'] == 1): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_WEBSITE'); ?></td>
					<td class='data'><?php echo HTMLHelper::_('link', $this->person->website, $this->person->website,
						array('target' => '_blank')); ?>
					</td>
				</tr>
				<?php
				endif;

				if ($this->person->height > 0 && $this->config['show_person_height'] == 1): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_HEIGHT'); ?></td>
					<td class='data'>
						<?php echo str_replace('%HEIGHT%', $this->person->height, Text::_('COM_JOOMLEAGUE_PERSON_HEIGHT_FORM')); ?>
					</td>
				</tr>
				<?php
				endif;

				if ($this->person->weight > 0 && $this->config['show_person_weight'] == 1): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_WEIGHT'); ?></td>
					<td class='data'>
						<?php echo str_replace('%WEIGHT%', $this->person->weight, Text::_('COM_JOOMLEAGUE_PERSON_WEIGHT_FORM')); ?>
					</td>
				</tr>
				<?php
				endif;

				if ($this->projectPerson->position_name != ''): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_POSITION'); ?></td>
					<td class='data'><?php echo Text::_($this->projectPerson->position_name); ?></td>
				</tr>
				<?php
				endif;

				if (!empty($this->person->knvbnr) && $this->config['show_person_regnr'] == 1): ?>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_REGISTRATIONNR'); ?></td>
					<td class='data'><?php echo $this->person->knvbnr; ?></td>
				</tr>
				<?php endif;?>
			</table>
		</td>
	</tr>
</table>
<?php endif; ?>
