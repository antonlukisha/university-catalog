CREATE TABLE courses (
	course_id INT AUTO_INCREMENT PRIMARY KEY,
	course_name VARCHAR(100) NOT NULL,
	`description` TEXT,
	capacity INT NOT NULL CHECK (capacity > 0),
	faculty_id INT,
	FOREIGN KEY (faculty_id) REFERENCES faculties(faculty_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;