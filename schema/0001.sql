# feeds
CREATE TABLE feeds(
	id INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(256) NOT NULL DEFAULT '',
	link VARCHAR(256) NOT NULL DEFAULT '',
	last_fetch_started INT NOT NULL DEFAULT 0,
	last_fetch_finished INT NOT NULL DEFAULT 0
);

INSERT INTO feeds VALUES(
	null,
	'',
	'http://www.tagesschau.de/xml/rss2',
	0,
	0
);

# items
CREATE TABLE items(
	id INT AUTO_INCREMENT PRIMARY KEY,
	feed_id INT REFERENCES feeds(id),
	author TEXT NOT NULL DEFAULT '',
	category TEXT NOT NULL DEFAULT '',
	comments TEXT NOT NULL DEFAULT '',
	description TEXT NOT NULL DEFAULT '',
	enclosure TEXT NOT NULL DEFAULT '',
	guid TEXT NOT NULL DEFAULT '',
	hash VARCHAR(256) NOT NULL DEFAULT '',
	link TEXT NOT NULL DEFAULT '',
	pubDate INT NOT NULL DEFAULT 0,
	source TEXT NOT NULL DEFAULT '',
	title TEXT NOT NULL DEFAULT ''
);

ALTER TABLE items ADD COLUMN added INT NOT NULL DEFAULT 0;
