DROP TABLE IF EXISTS role;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS log;

CREATE TABLE "role" (
	"id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	"description"	varchar(50)
);


CREATE TABLE "users" (
	"id"	VARCHAR(50) NOT NULL,
	"password"	VARCHAR(255),
	"role"	INT,
	PRIMARY KEY("id"),
	FOREIGN KEY("role") REFERENCES "role"("id")
);


CREATE TABLE "log" (
	"datetime"	INTEGER NOT NULL,
	"user"	varchar(50) NOT NULL,
	"action"	varchar(50) NOT NULL,
	"object"	varchar(50),
	"location"	varchar(500),
	"info"	varchar(255),
	PRIMARY KEY("datetime","user","action"),
	FOREIGN KEY("user") REFERENCES "users"("id")
);

/*------ INSERTION DONNEES ------*/
/*
INSERT INTO role (description) VALUES
("anonymous"),
("user"),
("admin");

INSERT INTO users (id,password,role) VALUES
("anonymous", NULL, 1),
("user", "user", 2),
("admin", "admin", 3);
*/
/*-------------------------------*/