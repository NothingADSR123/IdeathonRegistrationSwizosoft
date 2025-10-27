CREATE DATABASE IF NOT EXISTS ideathon_db;

USE ideathon_db;

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) UNIQUE NOT NULL,
    leader_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    university VARCHAR(150),
    state VARCHAR(100),
    region VARCHAR(100),
    member1_name VARCHAR(100),
    member2_name VARCHAR(100),
    payment_screenshot VARCHAR(255),
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO
    registrations (
        team_name,
        leader_name,
        email,
        phone,
        university,
        state,
        region,
        member1_name,
        member2_name,
        payment_screenshot
    )
VALUES (
        'Team Alpha',
        'John Doe',
        'john@example.com',
        '9999999999',
        'Tech University',
        'Maharashtra',
        'West',
        'Jane',
        'Arjun',
        'uploads/team_alpha.png'
    ),
    (
        'Team Beta',
        'Aarav Sharma',
        'aarav@example.com',
        '8888888888',
        'Future Institute',
        'Karnataka',
        'South',
        'Neha',
        'Isha',
        'uploads/team_beta.png'
    );