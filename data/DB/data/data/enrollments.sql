CREATE TABLE enrollments (
	enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
	student_id INT,     
	course_id INT,     
	enrollment_date DATE NOT NULL,     
	FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE ON UPDATE CASCADE,     
	FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB;