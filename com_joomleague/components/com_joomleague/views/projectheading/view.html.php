<?php 
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * View-Projectheading
 */
class JoomleagueViewProjectHeading extends JLGView
{
    public function display($tpl = null)
    {
        $model = $this->getModel();
        $overallconfig = $model->getOverallConfig();
        $project = $model->getProject();
        $this->project=$project;
        $division = $model->getDivision(Factory::getApplication()->input->getInt('division', 0));
		$this->division=$division;
        $this->overallconfig=$overallconfig;
        parent::display($tpl);
    }
}
