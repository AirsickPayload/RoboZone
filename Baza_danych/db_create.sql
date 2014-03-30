create table Users
(
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	login VARCHAR(255) NOT NULL UNIQUE,
	pass VARCHAR(255) NOT NULL,
	name VARCHAR(255) NOT NULL,
	lastname VARCHAR(255) NOT NULL,
	address VARCHAR(250) NOT NULL,
	university VARCHAR(255) NOT NULL
);

create table Scripts
(
	script_id INT AUTO_INCREMENT PRIMARY KEY,
	u_id INT NOT NULL,
	name VARCHAR(255) NOT NULL,
	script_data BLOB NOT NULL,
	script_size INT NOT NULL,
	upload_date DATETIME NOT NULL,
	FOREIGN KEY(u_id) REFERENCES Users(user_id)
);

create table Access
(
	access_id INT AUTO_INCREMENT PRIMARY KEY,
	u_id INT NOT NULL,
	date_from DATETIME NOT NULL,
	date_to DATETIME NOT NULL,
	FOREIGN KEY(u_id) REFERENCES Users(user_id)
);

create table History
(
	history_id INT AUTO_INCREMENT PRIMARY KEY,
	u_id INT NOT NULL,
	s_id INT NOT NULL,
	exec_date DATETIME NOT NULL,
	log TEXT NOT NULL,
	FOREIGN KEY(u_id) REFERENCES Users(user_id),
	FOREIGN KEY(s_id) REFERENCES Scripts(script_id)
);

create table Robots
(
	robot_id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL UNIQUE,
	ip VARCHAR(15) NOT NULL,
	port VARCHAR(5) NOT NULL
);

create table Taken
(
	r_id INT NOT NULL,
	u_id INT NOT NULL,
	FOREIGN KEY(r_id) REFERENCES Robots(robot_id),
	FOREIGN KEY(u_id) REFERENCES Users(user_id),
	PRIMARY KEY(r_id, u_id)
);