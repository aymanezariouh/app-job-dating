CREATE DATABASE job_dating CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE job_dating;

CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(100) NOT NULL,
                       email VARCHAR(150) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       role ENUM('admin','student') NOT NULL,
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE companies (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           name VARCHAR(150) NOT NULL,
                           sector VARCHAR(150) NOT NULL,
                           location VARCHAR(150) NOT NULL,
                           email VARCHAR(150) NOT NULL UNIQUE,
                           phone VARCHAR(50) NOT NULL,
                           avatar VARCHAR(255),
                           created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE announcements (
                               id INT AUTO_INCREMENT PRIMARY KEY,
                               title VARCHAR(150) NOT NULL,
                               company_id INT NOT NULL,
                               contract_type VARCHAR(100) NOT NULL,
                               location VARCHAR(150) NOT NULL,
                               image VARCHAR(255),
                               description TEXT NOT NULL,
                               skills TEXT NOT NULL,
                               deleted BOOLEAN DEFAULT FALSE,
                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                               updated_at TIMESTAMP NULL DEFAULT NULL,
                               FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);
CREATE TABLE students (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          user_id INT NOT NULL,
                          promotion VARCHAR(100) NOT NULL,
                          specialization VARCHAR(150) NOT NULL,
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role)
VALUES (
           'Admin',
           'admin@youcode.com',
           '$2y$10$Q9WmX0bFZ1gGZq2X6ZqQhecO2F1RZxZ0m0l1oUq5Qp0y3dYB4M8lC',
           'admin'
       );

INSERT INTO companies (name, sector, location, email, phone)
VALUES
    ('TechCorp', 'IT', 'Casablanca', 'contact@techcorp.com', '0600000000'),
    ('Designify', 'Design', 'Rabat', 'contact@designify.com', '0611111111');

INSERT INTO announcements (title, company_id, contract_type, location, description, skills)
VALUES
    (
        'Junior Web Developer',
        1,
        'CDI',
        'Casablanca',
        'Développement et maintenance des applications web',
        'PHP, MySQL, HTML, CSS'
    ),
    (
        'UI/UX Designer',
        2,
        'Stage',
        'Rabat',
        'Conception des interfaces utilisateurs',
        'Figma, UX, UI'
    );

INSERT INTO users (name, email, password, role)
VALUES
    (
        'Student One',
        'student@youcode.com',
        '$2y$10$Q9WmX0bFZ1gGZq2X6ZqQhecO2F1RZxZ0m0l1oUq5Qp0y3dYB4M8lC',
        'student'
    );

INSERT INTO students (user_id, promotion, specialization)
VALUES (2, '2024', 'Full Stack Web');