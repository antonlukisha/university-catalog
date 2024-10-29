CREATE TABLE study_groups (
	group_id INT AUTO_INCREMENT PRIMARY KEY,
	group_name VARCHAR(100) NOT NULL,
	faculty_id INT,
	leader_id INT,
	FOREIGN KEY (faculty_id) REFERENCES faculties(faculty_id) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (leader_id) REFERENCES leaders(leader_id) ON DELETE SET NULL ON UPDATE CASCADE 
) ENGINE=InnoDB;