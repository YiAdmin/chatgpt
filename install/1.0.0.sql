CREATE TABLE IF NOT EXISTS `__PREFIX__chatgpt_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `chatid` varchar(45) NOT NULL COMMENT '对话ID',
  `role` varchar(25) NOT NULL COMMENT '角色',
  `content` text NOT NULL COMMENT '内容',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;
