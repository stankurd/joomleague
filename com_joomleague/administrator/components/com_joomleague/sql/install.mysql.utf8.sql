-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_club`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_club` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(100) NOT NULL DEFAULT '',
  `zipcode` varchar(10) NOT NULL DEFAULT '',
  `location` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `country` varchar(3) DEFAULT NULL,
  `founded` DATE NOT NULL DEFAULT '0000-00-00',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `fax` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `website` varchar(250) NOT NULL DEFAULT '',
  `president` varchar(50) NOT NULL DEFAULT '',
  `manager` varchar(50) NOT NULL DEFAULT '',
  `logo_big` varchar(255) NOT NULL DEFAULT '',
  `logo_middle` varchar(255) NOT NULL DEFAULT '',
  `logo_small` varchar(255) NOT NULL DEFAULT '',
  `standard_playground` int(11) DEFAULT NULL,
  `notes` text  DEFAULT NULL,
  `extended` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `dissolved` varchar(50) DEFAULT NULL,
  `asset_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_division`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_division` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(75) NOT NULL DEFAULT '',
  `alias` varchar(75) NOT NULL DEFAULT '',
  `shortname` varchar(10) DEFAULT NULL,
  `notes` text  NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `picture` varchar(128) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_eventtype`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_eventtype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL DEFAULT '',
  `alias` varchar(75) NOT NULL DEFAULT '',
  `icon` varchar(128) NOT NULL DEFAULT '',
  `parent` int(11) NOT NULL DEFAULT '0',
  `splitt` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `direction` char(4) NOT NULL DEFAULT 'DESC',
  `double` tinyint(3) NOT NULL DEFAULT '0',
  `suspension` tinyint(3) NOT NULL DEFAULT '0',
  `sports_type_id` tinyint(1) NOT NULL DEFAULT '1',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`parent`,`sports_type_id`),
  KEY `sports_type_id` (`sports_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_league`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_league` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL DEFAULT '',
  `short_name` varchar(15) NOT NULL DEFAULT '',
  `middle_name` varchar(25) NOT NULL DEFAULT '',
  `alias` varchar(75) NOT NULL DEFAULT '',
  `country` varchar(3) DEFAULT NULL,
  `extended` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_match`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_match` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL DEFAULT '0',
  `match_number` varchar(10) DEFAULT NULL,
  `projectteam1_id` int(11) NOT NULL DEFAULT '0',
  `projectteam2_id` int(11) NOT NULL DEFAULT '0',
  `playground_id` int(11) DEFAULT NULL,
  `match_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_present` time DEFAULT NULL,
  `team1_result` float DEFAULT NULL,
  `team2_result` float DEFAULT NULL,
  `team1_bonus` int(11) NOT NULL DEFAULT '0',
  `team2_bonus` int(11) NOT NULL DEFAULT '0',
  `team1_legs` float DEFAULT NULL,
  `team2_legs` float DEFAULT NULL,
  `team1_result_split` varchar(64) DEFAULT NULL,
  `team2_result_split` varchar(64) DEFAULT NULL,
  `match_result_type` tinyint(4) NOT NULL DEFAULT '0',
  `team_won` tinyint(4) NOT NULL DEFAULT '0',
  `team1_result_ot` float DEFAULT NULL,
  `team2_result_ot` float DEFAULT NULL,
  `team1_result_so` float DEFAULT NULL,
  `team2_result_so` float DEFAULT NULL,
  `alt_decision` tinyint(4) NOT NULL DEFAULT '0',
  `team1_result_decision` float DEFAULT NULL,
  `team2_result_decision` float DEFAULT NULL,
  `decision_info` varchar(128) NOT NULL DEFAULT '',
  `cancel` tinyint(4) NOT NULL DEFAULT '0',
  `cancel_reason` varchar(32) NOT NULL DEFAULT '',
  `count_result` tinyint(4) NOT NULL DEFAULT '1',
  `crowd` int(11) NOT NULL DEFAULT '0',
  `summary` text DEFAULT NULL,
  `show_report` tinyint(4) NOT NULL DEFAULT '0',
  `preview` text DEFAULT NULL,
  `match_result_detail` varchar(64) NOT NULL DEFAULT '',
  `new_match_id` int(11) NOT NULL DEFAULT '0',
  `old_match_id` int(11) NOT NULL DEFAULT '0',
  `extended` text DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `asset_id` int(10) NOT NULL DEFAULT '0',
  `typeAlias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `round_id` (`round_id`),
  KEY `projectteam1_id` (`projectteam1_id`),
  KEY `projectteam2_id` (`projectteam2_id`),
  KEY `playground_id` (`playground_id`),
  KEY `new_match_id` (`new_match_id`),
  KEY `old_match_id` (`old_match_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_match_event`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_match_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL DEFAULT '0',
  `projectteam_id` int(11) NOT NULL DEFAULT '0',
  `teamplayer_id` int(11) NOT NULL DEFAULT '0',
  `teamplayer_id2` int(11) NOT NULL DEFAULT '0',
  `event_time` varchar(20) NOT NULL DEFAULT '',
  `event_type_id` int(11) NOT NULL DEFAULT '0',
  `event_sum` double DEFAULT NULL,
  `notice` varchar(64) NOT NULL DEFAULT '',
  `notes` text  DEFAULT NULL,
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `projectteam_id` (`projectteam_id`),
  KEY `teamplayer_id` (`teamplayer_id`),
  KEY `teamplayer_id2` (`teamplayer_id2`),
  KEY `event_type_id` (`event_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_match_player`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_match_player` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `match_id` int(11) unsigned NOT NULL DEFAULT '0',
  `teamplayer_id` int(11) unsigned NOT NULL DEFAULT '0',
  `project_position_id` int(11) unsigned NOT NULL DEFAULT '0',
  `came_in` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `in_for` int(11) unsigned DEFAULT NULL,
  `out` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `in_out_time` varchar(15) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `teamplayer_id` (`teamplayer_id`),
  KEY `project_position_id` (`project_position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_match_referee`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_match_referee` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL DEFAULT '0',
  `project_referee_id` int(11) NOT NULL DEFAULT '0',
  `project_position_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `project_referee_id` (`project_referee_id`),
  KEY `project_position_id` (`project_position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_match_staff`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_match_staff` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL DEFAULT '0',
  `team_staff_id` int(11) NOT NULL DEFAULT '0',
  `project_position_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `team_staff_id` (`team_staff_id`),
  KEY `project_position_id` (`project_position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_match_staff_statistic`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_match_staff_statistic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL DEFAULT '0',
  `projectteam_id` int(11) NOT NULL,
  `team_staff_id` int(11) NOT NULL DEFAULT '0',
  `statistic_id` int(11) NOT NULL DEFAULT '0',
  `value` double NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `projectteam_id` (`projectteam_id`),
  KEY `team_staff_id` (`team_staff_id`),
  KEY `statistic_id` (`statistic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_match_statistic`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_match_statistic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL DEFAULT '0',
  `projectteam_id` int(11) NOT NULL,
  `teamplayer_id` int(11) NOT NULL DEFAULT '0',
  `statistic_id` int(11) NOT NULL DEFAULT '0',
  `value` double NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `projectteam_id` (`projectteam_id`),
  KEY `teamplayer_id` (`teamplayer_id`),
  KEY `statistic_id` (`statistic_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_person`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) DEFAULT NULL,
  `firstname` varchar(45) NOT NULL DEFAULT '',
  `lastname` varchar(45) NOT NULL DEFAULT '',
  `nickname` varchar(45) NOT NULL DEFAULT '',
  `alias` varchar(90) NOT NULL DEFAULT '',
  `country` varchar(3) DEFAULT NULL,
  `knvbnr` varchar(10) NOT NULL DEFAULT '',
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `deathday` date NOT NULL DEFAULT '0000-00-00',
  `height` int(3) NOT NULL DEFAULT '0',
  `weight` int(3) NOT NULL DEFAULT '0',
  `picture` varchar(128) NOT NULL DEFAULT '',
  `show_pic` tinyint(1) NOT NULL DEFAULT '1',
  `show_persdata` tinyint(1) NOT NULL DEFAULT '1',
  `show_teamdata` tinyint(1) NOT NULL DEFAULT '1',
  `show_on_frontend` tinyint(1) NOT NULL DEFAULT '1',
  `info` varchar(255) NOT NULL DEFAULT '',
  `notes` text  DEFAULT NULL,
  `phone` varchar(20) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(50)  NOT NULL DEFAULT '',
  `website` varchar(250) NOT NULL DEFAULT '',
  `address` varchar(100) NOT NULL DEFAULT '',
  `zipcode` varchar(10) NOT NULL DEFAULT '',
  `location` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `address_country` varchar(3) DEFAULT NULL,
  `extended` text,
  `position_id` int(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `position_id` (`position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_playground`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_playground` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL DEFAULT '',
  `short_name` varchar(15) NOT NULL DEFAULT '',
  `alias` varchar(75) NOT NULL DEFAULT '',
  `address` varchar(100) NOT NULL DEFAULT '',
  `zipcode` varchar(10) NOT NULL DEFAULT '',
  `city` varchar(64) NOT NULL DEFAULT '',
  `country` varchar(3) DEFAULT NULL,
  `max_visitors` int(11) DEFAULT NULL,
  `website` varchar(250) NOT NULL DEFAULT '',
  `picture` varchar(128) NOT NULL DEFAULT '',
  `notes` text  DEFAULT NULL,
  `club_id` int(11) NOT NULL DEFAULT '0',
  `extended` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `club_id` (`club_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_position`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL DEFAULT '',
  `alias` varchar(75) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `persontype` tinyint(4) NOT NULL DEFAULT '1',
  `sports_type_id` tinyint(1) NOT NULL DEFAULT '1',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` smallint(6) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`parent_id`,`persontype`,`sports_type_id`),
  KEY `parent_id` (`parent_id`),
  KEY `sports_type_id` (`sports_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_position_eventtype`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_position_eventtype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL DEFAULT '0',
  `eventtype_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pos_et` (`position_id`,`eventtype_id`),
  KEY `position_id` (`position_id`),
  KEY `eventtype_id` (`eventtype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_position_statistic`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_position_statistic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position_id` int(11) NOT NULL DEFAULT '0',
  `statistic_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pos_et` (`position_id`,`statistic_id`),
  KEY `position_id` (`position_id`),
  KEY `statistic_id` (`statistic_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_project`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `league_id` int(11) NOT NULL DEFAULT '0',
  `season_id` int(11) NOT NULL DEFAULT '0',
  `master_template` int(11) NOT NULL DEFAULT '0',
  `sub_template_id` int(11) NOT NULL DEFAULT '0',
  `extension` varchar(80) DEFAULT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'Europe/Warsaw',
  `project_type` enum('SIMPLE_LEAGUE','DIVISIONS_LEAGUE','TOURNAMENT_MODE','FRIENDLY_MATCHES') NOT NULL DEFAULT 'SIMPLE_LEAGUE',
  `teams_as_referees` tinyint(1) NOT NULL DEFAULT '0',
  `sports_type_id` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `start_time` varchar(5) NOT NULL DEFAULT '15:30',
  `current_round_auto` tinyint(4) NOT NULL DEFAULT '0',
  `current_round` varchar(32) NOT NULL DEFAULT '0',
  `auto_time` int(11) DEFAULT NULL,
  `game_regular_time` smallint(6) NOT NULL DEFAULT '90',
  `game_parts` smallint(6) NOT NULL DEFAULT '2',
  `halftime` smallint(6) NOT NULL DEFAULT '15',
  `points_after_regular_time` varchar(10) NOT NULL DEFAULT '3,1,0',
  `use_legs` tinyint(1) DEFAULT NULL,
  `allow_add_time` tinyint(1) NOT NULL DEFAULT '0',
  `add_time` smallint(6) NOT NULL DEFAULT '30',
  `points_after_add_time` varchar(10) NOT NULL DEFAULT '3,1,0',
  `points_after_penalty` varchar(10) NOT NULL DEFAULT '3,1,0',
  `fav_team` varchar(64) NOT NULL DEFAULT '',
  `fav_team_highlight_type` varchar(7) NOT NULL DEFAULT '',
  `fav_team_color` varchar(7) NOT NULL DEFAULT '',
  `fav_team_text_color` varchar(7) NOT NULL DEFAULT '',
  `fav_team_text_bold` varchar(7) NOT NULL DEFAULT '',
  `template` varchar(32) NOT NULL DEFAULT 'default',
  `enable_sb` tinyint(4) NOT NULL DEFAULT '0',
  `sb_catid` int(11) NOT NULL DEFAULT '0',
  `extended` text,
  `picture` varchar(128) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `asset_id` int(10) NOT NULL DEFAULT '0',
  `is_utc_converted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name, league, season` (`name`,`league_id`,`season_id`),
  KEY `league_id` (`league_id`),
  KEY `season_id` (`season_id`),
  KEY `sub_template_id` (`sub_template_id`),
  KEY `sports_type_id` (`sports_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_project_position`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_project_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pos_proj` (`position_id`,`project_id`),
  KEY `project_id` (`project_id`),
  KEY `position_id` (`position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_project_referee`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_project_referee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `person_id` int(11) NOT NULL DEFAULT '0',
  `project_position_id` int(11) DEFAULT NULL,
  `notes` text  DEFAULT NULL,
  `picture` varchar(128) NOT NULL DEFAULT '',
  `published` int(1) unsigned NOT NULL DEFAULT '1',
  `extended` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `asset_id` int(10) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `person_id` (`person_id`),
  KEY `project_position_id` (`project_position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_project_team`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_project_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `team_id` int(11) NOT NULL DEFAULT '0',
  `division_id` int(11) DEFAULT NULL,
  `start_points` smallint(6) NOT NULL DEFAULT '0',
  `points_finally` smallint(6) NOT NULL DEFAULT '0',
  `neg_points_finally` smallint(6) NOT NULL DEFAULT '0',
  `matches_finally` smallint(6) NOT NULL DEFAULT '0',
  `won_finally` smallint(6) NOT NULL DEFAULT '0',
  `draws_finally` smallint(6) NOT NULL DEFAULT '0',
  `lost_finally` smallint(6) NOT NULL DEFAULT '0',
  `homegoals_finally` smallint(6) NOT NULL DEFAULT '0',
  `guestgoals_finally` smallint(6) NOT NULL DEFAULT '0',
  `diffgoals_finally` smallint(6) NOT NULL DEFAULT '0',
  `is_in_score` tinyint(1) NOT NULL DEFAULT '1',
  `use_finally` tinyint(1) NOT NULL DEFAULT '0',
  `info` varchar(255) NOT NULL DEFAULT '',
  `picture` varchar(128) DEFAULT NULL,
  `notes` text  DEFAULT NULL,
  `standard_playground` int(11) DEFAULT NULL,
  `reason` varchar(150) DEFAULT NULL,
  `extended` text,
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(10) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `combi` (`project_id`,`team_id`),
  KEY `project_id` (`project_id`),
  KEY `team_id` (`team_id`),
  KEY `division_id` (`division_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_round`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_round` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `roundcode` int(11) NOT NULL DEFAULT '0',
  `name` varchar(75) NOT NULL DEFAULT '',
  `alias` varchar(75) NOT NULL DEFAULT '',
  `round_date_first` date NOT NULL DEFAULT '0000-00-00',
  `round_date_last` date NOT NULL DEFAULT '0000-00-00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_season`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_season` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL DEFAULT '',
  `alias` varchar(75) NOT NULL DEFAULT '',
  `extended` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_sports_type`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_sports_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(128) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_statistic`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_statistic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL DEFAULT '',
  `alias` varchar(75) NOT NULL DEFAULT '',
  `short` varchar(10) NOT NULL DEFAULT '',
  `icon` varchar(128) NOT NULL DEFAULT '',
  `class` varchar(50) NOT NULL COMMENT 'must be the name of the class handling it',
  `calculated` tinyint(4) DEFAULT NULL,
  `params` text DEFAULT NULL,
  `baseparams` text DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `sports_type_id` tinyint(1) NOT NULL DEFAULT '1',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sports_type_id` (`sports_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_team`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) DEFAULT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `short_name` varchar(15) NOT NULL DEFAULT '',
  `middle_name` varchar(25) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `website` varchar(250) NOT NULL DEFAULT '',
  `info` varchar(255) NOT NULL DEFAULT '',
  `notes` text  DEFAULT NULL,  
  `picture` varchar(128) NOT NULL DEFAULT '',
  `extended` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `asset_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  KEY `fk_club` (`club_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_team_player`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_team_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectteam_id` int(11) DEFAULT '0',
  `person_id` int(11) DEFAULT '0',
  `project_position_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `jerseynumber` int(11) DEFAULT NULL,
  `notes` text  DEFAULT NULL,  
  `picture` varchar(128) NOT NULL DEFAULT '',
  `extended` text,
  `injury` tinyint(4) NOT NULL DEFAULT '0',
  `injury_date` int(11) NOT NULL,
  `injury_end` int(11) NOT NULL,
  `injury_detail` varchar(255) NOT NULL,
  `injury_date_start` date NOT NULL DEFAULT '0000-00-00',
  `injury_date_end` date NOT NULL DEFAULT '0000-00-00',
  `suspension` tinyint(4) NOT NULL DEFAULT '0',
  `suspension_date` int(11) NOT NULL,
  `suspension_end` int(11) NOT NULL,
  `suspension_detail` varchar(255) NOT NULL,
  `susp_date_start` date NOT NULL DEFAULT '0000-00-00',
  `susp_date_end` date NOT NULL DEFAULT '0000-00-00',
  `away` tinyint(4) NOT NULL DEFAULT '0',
  `away_date` int(11) NOT NULL,
  `away_end` int(11) NOT NULL,
  `away_detail` varchar(255) NOT NULL,
  `away_date_start` date NOT NULL DEFAULT '0000-00-00',
  `away_date_end` date NOT NULL DEFAULT '0000-00-00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `asset_id` int(10) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `projectteam_id` (`projectteam_id`),
  KEY `person_id` (`person_id`),
  KEY `project_position_id` (`project_position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_team_staff`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_team_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectteam_id` int(11) DEFAULT '0',
  `person_id` int(11) DEFAULT '0',
  `project_position_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `notes` text  DEFAULT NULL,
  `injury` tinyint(4) NOT NULL DEFAULT '0',
  `injury_date` int(11) DEFAULT NULL,
  `injury_end` int(11) DEFAULT NULL,
  `injury_detail` varchar(255) DEFAULT NULL,
  `injury_date_start` date NOT NULL DEFAULT '0000-00-00',
  `injury_date_end` date NOT NULL DEFAULT '0000-00-00',
  `suspension` tinyint(4) NOT NULL DEFAULT '0',
  `suspension_date` int(11) DEFAULT NULL,
  `suspension_end` int(11) DEFAULT NULL,
  `suspension_detail` varchar(255) DEFAULT NULL,
  `susp_date_start` date NOT NULL DEFAULT '0000-00-00',
  `susp_date_end` date NOT NULL DEFAULT '0000-00-00',
  `away` tinyint(4) NOT NULL DEFAULT '0',
  `away_date` int(11) DEFAULT NULL,
  `away_end` int(11) DEFAULT NULL,
  `away_detail` varchar(255) DEFAULT NULL,
  `away_date_start` date NOT NULL DEFAULT '0000-00-00',
  `away_date_end` date NOT NULL DEFAULT '0000-00-00',
  `picture` varchar(128) NOT NULL DEFAULT '',
  `extended` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  `asset_id` int(10) NOT NULL DEFAULT '0',
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `projectteam_id` (`projectteam_id`),
  KEY `person_id` (`person_id`),
  KEY `project_position_id` (`project_position_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_team_trainingdata`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_team_trainingdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `team_id` int(11) NOT NULL DEFAULT '0',
  `project_team_id` int(11) NOT NULL DEFAULT '0',
  `dayofweek` tinyint(1) NOT NULL DEFAULT '0',
  `time_start` int(11) NOT NULL DEFAULT '0',
  `time_end` int(11) NOT NULL DEFAULT '0',
  `place` varchar(255) NOT NULL DEFAULT '',
  `notes` text  DEFAULT NULL,  
  `ordering` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_template_config`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_template_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL DEFAULT '0',
  `template` varchar(64) NOT NULL DEFAULT '',
  `func` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `published` int(1) unsigned NOT NULL DEFAULT '1',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_treeto`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_treeto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `division_id` int(11) NOT NULL DEFAULT '0',
  `tree_i` int(11) NOT NULL DEFAULT '0',
  `name` varchar(128) DEFAULT NULL,
  `global_bestof` tinyint(1) NOT NULL DEFAULT '0',
  `global_matchday` tinyint(1) NOT NULL DEFAULT '0',
  `global_known` tinyint(1) NOT NULL DEFAULT '0',
  `global_fake` tinyint(1) NOT NULL DEFAULT '0',
  `leafed` tinyint(1) NOT NULL DEFAULT '0',
  `mirror` tinyint(1) NOT NULL DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `trophypic` varchar(128) DEFAULT NULL,
  `extended` text,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_treeto_match`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_treeto_match` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL DEFAULT '0',
  `match_id` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `combi` (`node_id`,`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_treeto_node`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_treeto_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `treeto_id` int(11) NOT NULL DEFAULT '0',
  `node` int(11) NOT NULL DEFAULT '0',
  `row` int(11) NOT NULL DEFAULT '0',
  `bestof` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(50) NOT NULL DEFAULT '',
  `content` varchar(50) NOT NULL DEFAULT '',
  `team_id` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `is_leaf` tinyint(1) NOT NULL DEFAULT '0',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0',
  `is_ready` tinyint(1) NOT NULL DEFAULT '0',
  `got_lc` tinyint(1) NOT NULL DEFAULT '0',
  `got_rc` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomleague_version`
--

CREATE TABLE IF NOT EXISTS `#__joomleague_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `major` int(10) NOT NULL,
  `minor` int(10) NOT NULL,
  `build` int(10) NOT NULL,
  `count` int(10) NOT NULL DEFAULT '0',
  `revision` varchar(128) NOT NULL,
  `file` varchar(255) NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `version` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
