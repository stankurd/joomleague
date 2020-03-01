<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Display the About view
 *
 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
 *
 * @return  void
 */
class JoomleagueViewAbout extends JLGView
{
	public function display($tpl = null)
	{
		// Assign data to the view
		$this->about = $this->get('About');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			Log::add(implode('<br />', $errors), Log::WARNING, 'error');

			return false;
		}

		$this->pageTitle = Text::_('COM_JOOMLEAGUE_ABOUT_PAGE_TITLE');
		$document = Factory::getDocument();
		$document->setTitle($this->pageTitle);

		// Display the view
		parent::display($tpl);
	}

}
