<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>
<!-- EXTENDED DATA-->
<?php
if(count($this->extended->getFieldsets()) > 0)
{
	// fieldset->name is set in the backend and is localized, so we need the backend language file here
	Factory::getLanguage()->load('com_joomleague', JPATH_ADMINISTRATOR);
	
	foreach ($this->extended->getFieldsets() as $fieldset)
	{
		$fields = $this->extended->getFieldset($fieldset->name);
		if (count($fields) > 0)
		{
			// Check if the extended data contains information 
			$hasData = false;
			foreach ($fields as $field)
			{
				// TODO: backendonly was a feature of JLGExtraParams, and is not yet available.
				//       (this functionality probably has to be added later)
				$value = $field->value;	// Remark: empty($field->value) does not work, using an extra local var does
				if (!empty($value)) // && !$field->backendonly
				{
					$hasData = true;
					break;
				}
			}
			// And if so, display this information
			if ($hasData)
			{
				?>
				<h2><?php echo '&nbsp;' . Text::_($fieldset->name); ?></h2>
				<table>
					<tbody>
				<?php
				foreach ($fields as $field)
				{
					$value = $field->value;
					if (!empty($value)) // && !$field->backendonly)
					{
						?>
						<tr>
							<td class="label"><?php echo $field->label; ?></td>
							<td class="data">
                        <?php
                        // weather
                        if ($field->value == "dry") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_DRY');
                        } elseif ($field->value == "rainy") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_RAINY');
                        } elseif ($field->value == "drizzle") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_DRIZZLE');
                        } elseif ($field->value == "shower") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_SHOWER');
                        } elseif ($field->value == "sunny") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_SUNNY');
                        } elseif ($field->value == "windy") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_WINDY');
                        } elseif ($field->value == "cloudy") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_CLOUDY');
                        } elseif ($field->value == "snowing") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_SNOWING');
                        } elseif ($field->value == "foggy") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_WEATHER_FOGGY');
                        }

                        // field condition
                        if ($field->value == "normal") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_NORMAL');
                        } elseif ($field->value == "fielddry") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_DRY');
                        } elseif ($field->value == "dull") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_DULL');
                        } elseif ($field->value == "wettish") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_WETTISH');
                        } elseif ($field->value == "wet") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_WET');
                        } elseif ($field->value == "snow") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_SNOW');
                        } elseif ($field->value == "frozen") {
                           echo Text::_('COM_JOOMLEAGUE_EXT_MATCH_FIELDCONDITION_FROZEN');
                        }
                        ?>
							</td>
						<tr>
						<?php
					}
				}
				?>
					</tbody>
				</table>
				<br/>
				<?php
			}
		}
	}
}
?>	
