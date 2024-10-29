CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NfacultiesleadersOT NULL,
    patronymic VARCHAR(50),
    birth_place VARCHAR(100),
    birth_date DATE,
    phone VARCHAR(20),
    average_grade DECIMAL(3,2) CHECK (average_grade BETWEEN 2.00 AND 5.00),
    group_id INT,
    FOREIGN KEY (group_id) REFERENCES study_groups(group_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;
