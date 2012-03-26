CREATE TABLE changes (
  change_id bigint(20) NOT NULL AUTO_INCREMENT,
  timestamp int(10) unsigned zerofill NOT NULL,
  user_id bigint(20) NOT NULL,
  item_type varchar(255) NOT NULL,
  item_id bigint(20) NOT NULL,
  change_type varchar(255) NOT NULL,
  old_value mediumtext,
  new_value mediumtext,
  PRIMARY KEY (change_id),
  KEY timestamp (timestamp),
  KEY user_id (user_id),
  KEY change_combo (item_type(100),item_id,change_type(100))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE dictionary (
  morpheme_id bigint(20) NOT NULL AUTO_INCREMENT,
  morpheme varchar(255) NOT NULL,
  gloss tinytext NOT NULL,
  comments text NOT NULL,
  morpheme_type int(11) NOT NULL,
  parent_morpheme bigint(20) DEFAULT NULL,
  PRIMARY KEY (morpheme_id),
  UNIQUE KEY unique_morpheme (morpheme,gloss(50)),
  KEY morpheme (morpheme,parent_morpheme)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE records (
  record_id bigint(20) NOT NULL AUTO_INCREMENT,
  transcription text NOT NULL,
  translation text NOT NULL,
  comments text NOT NULL,
  grammaticality tinyint(1) NOT NULL,
  creator_id bigint(20) NOT NULL,
  creation_time int(10) unsigned zerofill NOT NULL,
  PRIMARY KEY (record_id),
  UNIQUE KEY unique_record (transcription(100),translation(100),grammaticality),
  KEY grammaticality (grammaticality),
  KEY creator_id (creator_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE sessions (
  session_hash char(40) NOT NULL,
  user_id bigint(10) NOT NULL DEFAULT '-1',
  creation_date int(10) unsigned NOT NULL,
  expiration_date int(10) unsigned NOT NULL,
  secure tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (session_hash),
  KEY user_id (user_id),
  KEY creation (creation_date),
  KEY expiration (expiration_date)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

CREATE TABLE users (
  user_id bigint(20) NOT NULL AUTO_INCREMENT,
  name tinytext NOT NULL,
  email_address varchar(320) NOT NULL,
  passphrase varchar(40) NOT NULL,
  registration_date int(10) unsigned zerofill NOT NULL,
  user_type int(11) NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE KEY email_address (email_address)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO users (user_id, name, email_address, passphrase, registration_date, user_type) VALUES(-1, 'Anonymous', '', '', 0000000000, -1);
