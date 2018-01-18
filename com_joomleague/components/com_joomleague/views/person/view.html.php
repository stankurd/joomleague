<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Person view
 */
abstract class JoomleagueViewPerson extends JLGView
{
    var $overallconfig;
    var $config;
    var $project;
    var $division;
    var $person;
    var $projectPerson;
    var $info;
    var $extended;
    var $career;
    var $careerTitle;

    /**
     * data Title of the page
     * @var string
     */
    var $pageTitle;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this->overallconfig = $model->getOverallConfig();
        $this->config = $model->getTemplateConfig($this->getName());
        $this->project = $model->getProject();
        $this->division = $model->getDivision($model->divisionid);
        $this->person = $model->getPerson();
        $this->setViewSpecificParameters();

        parent::display($tpl);
    }

    abstract function setViewSpecificParameters();

    protected function setPageTitle($formatString)
    {
        $titleInfo = JoomleagueHelper::createTitleInfo(Text::sprintf($formatString,
            $this->person->firstname, $this->person->lastname));
        if (!empty($this->project))
        {
            $titleInfo->projectName = $this->project->name;
            $titleInfo->leagueName = $this->project->league_name;
            $titleInfo->seasonName = $this->project->season_name;
        }
        if (!empty($this->division))// && $this->division->id != 0)
        {
            $titleInfo->divisionName = $this->division->name;
        }
        $this->pageTitle = JoomleagueHelper::formatTitle($titleInfo, $this->config['page_title_format']);
        $document = Factory::getDocument();
        $document->setTitle($this->pageTitle);
    }

    function setCareerTitle($title)
    {
        $this->careerTitle = $title;
    }

    function getPicture()
    {
        $imgTitle = Text::sprintf(Text::_('COM_JOOMLEAGUE_PERSON_PICTURE'), JoomleagueHelper::formatName(null,
            $this->person->firstname, $this->person->nickname, $this->person->lastname, $this->config['name_format']));
        $picture = isset($this->projectPerson) ? $this->projectPerson->picture : null;
        if (empty($picture) || $picture == JoomleagueHelper::getDefaultPlaceholder('player'))
        {
            $picture = $this->person->picture;
        }
        if (!file_exists($picture))
        {
            $picture = JoomleagueHelper::getDefaultPlaceholder('player') ;
        }
        return JoomleagueHelper::getPictureThumb($picture, $imgTitle, $this->config['picture_width'], $this->config['picture_height']);
    }

    function getNationality()
    {
        return Countries::getCountryFlag($this->person->country) . ' ' . Text::_(Countries::getCountryName($this->person->country));
    }

    function formattedName()
    {
        $personName = JoomleagueHelper::formatName(null ,$this->person->firstname, $this->person->nickname,
            $this->person->lastname, $this->config['name_format']);
        switch ($this->config['show_user_profile'])
        {
            case 1: // Link to Joomla Contact Page
                $link = JoomleagueHelperRoute::getContactRoute($this->person->contact_id);
                $personName = HTMLHelper::link($link, $personName);
                break;

            case 2: // Link to CBE User Page with support for JoomLeague Tab
                $link = JoomleagueHelperRoute::getUserProfileRouteCBE($this->person->contact_id, $this->project->id, $this->person->id);
                $personName = HTMLHelper::link($link, $personName);
                break;

            default:
                break;
        }
        return $personName;
    }

    function getDescription()
    {
        return !empty($this->projectPerson->notes)
            ? $this->projectPerson->notes
            : !empty($this->person->notes)
                ? $this->person->notes
                : '';
    }

    function birthDayTitle()
    {
        switch ($this->config['show_birthday'])
        {
            case 1: // show Birthday and Age
                $title = 'COM_JOOMLEAGUE_PERSON_BIRTHDAY_AGE';
                break;
            case 2: // show Only Birthday
                $title = 'COM_JOOMLEAGUE_PERSON_BIRTHDAY';
                break;
            case 3: // show Only Age
                $title = 'COM_JOOMLEAGUE_PERSON_AGE';
                break;
            case 4: // show Only Year of birth
                $title = 'COM_JOOMLEAGUE_PERSON_YEAR_OF_BIRTH';
                break;
            default:
                $title = '';
                break;
        }
        return Text::_($title);
    }

    function formattedBirthDay()
    {
        if ($this->person->birthday == '0000-00-00')
        {
            $birthdayStr = '-';
        }
        else
        {
            switch ($this->config['show_birthday']) {
                case 1:     // show Birthday and Age
                    $birthdayStr = HTMLHelper::date($this->person->birthday . ' UTC', Text::_('COM_JOOMLEAGUE_GLOBAL_DAYDATE'),
                            JoomleagueHelper::getTimezone($this->project, $this->overallconfig)) .
                        '&nbsp;(' . JoomleagueHelper::getAge($this->person->birthday, $this->person->deathday) . ')';
                    break;

                case 2:     // show Only Birthday
                    $birthdayStr = HTMLHelper::date($this->person->birthday . ' UTC', Text::_('COM_JOOMLEAGUE_GLOBAL_DAYDATE'),
                        JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
                    break;

                case 3:     // show Only Age
                    $birthdayStr = JoomleagueHelper::getAge($this->person->birthday, $this->person->deathday);
                    break;

                case 4:     // show Only Year of birth
                    $birthdayStr = HTMLHelper::date($this->person->birthday . ' UTC', Text::_('%Y'),
                        JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
                    break;

                default:
                    $birthdayStr = '';
                    break;
            }
        }
        return $birthdayStr;
    }

    function formattedDeathDay()
    {
        return '&dagger; ' . HTMLHelper::date($this->person->deathday .' UTC', Text::_('COM_JOOMLEAGUE_GLOBAL_DEATHDATE'),
            JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
    }

    function formattedAddress()
    {
        return Countries::convertAddressString('', $this->person->address, $this->person->state, $this->person->zipcode,
            $this->person->location, $this->person->address_country, 'COM_JOOMLEAGUE_PERSON_ADDRESS_FORM');
    }

    function formattedEmail()
    {
        $user = Factory::getUser();
        if ($user->id || !$this->overallconfig['nospam_email'])
        {
            $email = '<a href="mailto: ' . $this->person->email . '"> ' . $this->person->email . ' </a>';
        }
        else
        {
            $email = HTMLHelper::_('email.cloak', $this->person->email);
        }
        return $email;
    }

    function formattedAbsenceDate($date, $from)
    {
        $absenceDate = HTMLHelper::date($date .' UTC', Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
            JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
        if (isset($from))
        {
            $absenceDate .= ' - ' . $from;
        }
        return $absenceDate;
    }

    function getEventIconHtml($statusTypeOrIconName, $text, $options = null)
    {
        switch ($statusTypeOrIconName)
        {
            case 'injury':
                $iconName = 'injured.gif';
                break;
            case 'suspension':
                $iconName = 'suspension.gif';
                break;
            case 'away':
                $iconName = 'away.gif';
                break;
            default:
                $iconName = $statusTypeOrIconName;
        }
        if (is_null($options))
        {
            $options = array(' title' => Text::_($text));
        }
        return HTMLHelper::image('images/com_joomleague/database/events/' . $this->project->fs_sport_type_name . '/' . $iconName,
            Text::_($text), $options);
    }

    function valueOrZeroRepresentation($value)
    {
        return $value > 0 ? $value : $this->overallconfig['zero_events_value'];
    }
}