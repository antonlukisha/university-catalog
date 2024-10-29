CREATE TABLE performances (     
	performance_id INT AUTO_INCREMENT PRIMARY KEY,     
	student_id INT,     
	course_id INT,     
	grade DECIMAL(2,1) CHECK (grade BETWEEN 2.0 AND 5.0),     
	FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE ON UPDATE CASCADE,     
	FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE ON UPDATE CASCADE 
) ENGINE=InnoDB;
