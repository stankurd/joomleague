<?php defined('_JEXEC') or die;

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class='joomleague'>
	<?php
	if ($this->config['show_sectionheader'])
	{
		echo $this->loadTemplate('sectionheader');
	}

	echo $this->loadTemplate('projectheading');

	if ($this->config['show_rankingnav'] == 1)
	{
		echo $this->loadTemplate('rankingnav');
	}

	if ($this->config['show_ranking'] == 1)
	{
		if ($this->config['use_tabbed_view'] == 1)
		{
			$i = 1;
			$selector = 'ranking';
			echo JHtml::_('bootstrap.startTabSet', $selector, array('active' => 'panel' . $i));
				
			echo JHtml::_('bootstrap.addTab', $selector, 'panel' . $i++, JText::_('COM_JOOMLEAGUE_RANKING_FULL_RANKING'));
			echo $this->loadTemplate('ranking');
			echo JHtml::_('bootstrap.endTab');
			
			echo JHtml::_('bootstrap.addTab', $selector, 'panel' . $i++, JText::_('COM_JOOMLEAGUE_RANKING_HOME_RANKING'));
			$this->currentRanking = $this->homeRanking;
			echo $this->loadTemplate('ranking');
			echo JHtml::_('bootstrap.endTab');
			
			echo JHtml::_('bootstrap.addTab', $selector, 'panel' . $i++, JText::_('COM_JOOMLEAGUE_RANKING_AWAY_RANKING'));
			$this->currentRanking = $this->awayRanking;
			echo $this->loadTemplate('ranking');
			echo JHtml::_('bootstrap.endTab');
			
			echo JHtml::_('bootstrap.endTabSet');
		}
		else
		{
			echo $this->loadTemplate('ranking');
		}
	}

	if ($this->config['show_colorlegend'] > 0)
	{
		echo $this->loadTemplate('colorlegend');
	}
	
	if ($this->config['show_explanation'] == 1)
	{
		echo $this->loadTemplate('explanation');
	}
	
	if ($this->config['show_pagnav'] == 1)
	{
		echo $this->loadTemplate('pagnav');
	}
	
	if ($this->config['show_help'] == 1)
	{
		echo $this->loadTemplate('hint');
	}
	?>
	<div>
		<?php
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
		?>
	</div>
</div>
