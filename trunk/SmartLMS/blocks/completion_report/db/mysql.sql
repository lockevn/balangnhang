--
-- Definition of table `prefix_completion_assignments`
--

CREATE TABLE `prefix_completion_assignments` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `userid` bigint(10) unsigned NOT NULL default '0',
  `assignmentid` bigint(10) unsigned NOT NULL default '0',
  `intAssignmentGrade` bigint(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Stores assignment grades ';

--
-- Definition of table `prefix_completion_configure`
--

CREATE TABLE `prefix_completion_configure` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `intPercentQuiz` bigint(10) unsigned NOT NULL default '80',
  `intPercentAssignment` bigint(10) unsigned NOT NULL default '80',
  `intPercentManual` bigint(10) unsigned NOT NULL default '80',
  `blnEnableResource` enum('on','off') NOT NULL default 'on',
  `blnEnableAssignment` enum('on','off') NOT NULL default 'off',
  `blnEnableQuiz` enum('on','off') NOT NULL default 'on',
  `blnEnableManual` enum('on','off') NOT NULL default 'on',
  `intFinalProjectID` bigint(10) unsigned NOT NULL default '0',
  `intFinalExamID` bigint(10) unsigned NOT NULL default '0',
  `strApplyPassingGrade` enum('FinalExam','AverageAllQuizzes') NOT NULL default 'FinalExam',
  `strApplyAssignmentGrade` enum('FinalProject','AverageAllAssignments') NOT NULL default 'FinalProject',
  `strCurrentRequiredResources` text NOT NULL,
  `intCourseID` bigint(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='Stores configuration for completion report';

--
-- Definition of table `prefix_completion_quizzes`
--

CREATE TABLE `prefix_completion_quizzes` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `userid` bigint(10) unsigned NOT NULL default '0',
  `quizid` bigint(10) unsigned NOT NULL default '0',
  `intQuizGrade` bigint(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Stores quiz grades';

--
-- Definition of table `prefix_completion_report`
--

CREATE TABLE `prefix_completion_report` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `userid` bigint(10) NOT NULL default '0',
  `userfirstname` char(50) NOT NULL default '',
  `userlastname` char(50) NOT NULL default '',
  `intaverageQuiz` bigint(10) unsigned NOT NULL default '0',
  `intGradeFinalExam` bigint(10) unsigned NOT NULL default '0',
  `intaverageAssignment` bigint(10) unsigned NOT NULL default '0',
  `intGradeFinalProject` bigint(10) unsigned NOT NULL default '0',
  `intGradeManual` bigint(10) unsigned NOT NULL default '0',
  `strCompletedResources` bigint(10) unsigned NOT NULL default '0',
  `courseid` bigint(10) unsigned NOT NULL default '0',
  `strCompletionStatus` char(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='Stores completion report for individual user';

--
-- Definition of table `prefix_completion_resources`
--

CREATE TABLE `prefix_completion_resources` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `userid` bigint(10) unsigned NOT NULL default '0',
  `resourceid` bigint(10) unsigned NOT NULL default '0',
  `logtime` bigint(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Stores resource logtime';

