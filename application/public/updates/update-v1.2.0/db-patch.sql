
ALTER TABLE quiz_stats DROP FOREIGN KEY quiz_stats_quiz_id_foreign;
ALTER TABLE quiz_user_activity DROP FOREIGN KEY quiz_user_activity_quiz_id_foreign;
ALTER TABLE quiz_user_answers DROP FOREIGN KEY quiz_user_answers_quiz_id_foreign;
ALTER TABLE quiz_user_results DROP FOREIGN KEY quiz_user_results_quiz_id_foreign;

ALTER TABLE quizes ENGINE = MyISAM;
ALTER TABLE quizes ADD FULLTEXT search(topic, description);

CREATE TABLE IF NOT EXISTS `leaderboard` (
`id` int(10) unsigned NOT NULL,
  `boardable_id` int(10) unsigned NOT NULL,
  `boardable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `points` int(10) unsigned NOT NULL,
  `rank` int(10) unsigned NOT NULL,
  `blacklisted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

ALTER TABLE `leaderboard` ADD PRIMARY KEY (`id`);

ALTER TABLE `leaderboard` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;


