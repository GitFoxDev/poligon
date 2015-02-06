CREATE TABLE files (
  id int(5) NOT NULL AUTO_INCREMENT,
  name text NOT NULL,
  data blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;