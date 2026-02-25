<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$pdo = get_pdo();

echo "<h1>Database Check and Initialization</h1>";

// Check if users table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'users' does not exist. Creating it...</p>";

        $createUsersTable = "
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            username VARCHAR(50) UNIQUE,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('owner', 'trainer', 'customer', 'admin', 'staff') DEFAULT 'customer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $pdo->exec($createUsersTable);
        echo "<p style='color: green;'>✓ Users table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Users table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking users table: " . $e->getMessage() . "</p>";
}

// Check if members table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'members'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'members' does not exist. Creating it...</p>";

        $createMembersTable = "
        CREATE TABLE members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(20),
            fitness_level ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";

        $pdo->exec($createMembersTable);
        echo "<p style='color: green;'>✓ Members table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Members table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking members table: " . $e->getMessage() . "</p>";
}

// Check if schedules table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'schedules'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'schedules' does not exist. Creating it...</p>";

        $createSchedulesTable = "
        CREATE TABLE schedules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            trainer_id INT NOT NULL,
            date DATE NOT NULL,
            time_start TIME NOT NULL,
            time_end TIME NOT NULL,
            status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES users(id),
            FOREIGN KEY (trainer_id) REFERENCES users(id)
        )";

        $pdo->exec($createSchedulesTable);
        echo "<p style='color: green;'>✓ Schedules table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Schedules table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking schedules table: " . $e->getMessage() . "</p>";
}

// Check if attendance table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'attendance'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'attendance' does not exist. Creating it...</p>";

        $createAttendanceTable = "
        CREATE TABLE attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            date DATE NOT NULL,
            check_in_time TIME,
            check_out_time TIME,
            status ENUM('present', 'absent', 'late') DEFAULT 'present',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";

        $pdo->exec($createAttendanceTable);
        echo "<p style='color: green;'>✓ Attendance table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Attendance table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking attendance table: " . $e->getMessage() . "</p>";
}

// Check if payments table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'payments' does not exist. Creating it...</p>";

        $createPaymentsTable = "
        CREATE TABLE payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50),
            transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";

        $pdo->exec($createPaymentsTable);
        echo "<p style='color: green;'>✓ Payments table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Payments table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking payments table: " . $e->getMessage() . "</p>";
}

// Check if workout_logs table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'workout_logs'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'workout_logs' does not exist. Creating it...</p>";

        $createWorkoutLogsTable = "
        CREATE TABLE workout_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exercise VARCHAR(100) NOT NULL,
            sets INT,
            reps INT,
            weight DECIMAL(5,2),
            duration INT, -- durasi dalam menit
            calories_burned INT,
            log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";

        $pdo->exec($createWorkoutLogsTable);
        echo "<p style='color: green;'>✓ Workout logs table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Workout logs table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking workout logs table: " . $e->getMessage() . "</p>";
}

// Check if exercise_library table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'exercise_library'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'exercise_library' does not exist. Creating it...</p>";

        $createExerciseLibraryTable = "
        CREATE TABLE exercise_library (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            category VARCHAR(50) NOT NULL,
            description TEXT,
            difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
            video_url VARCHAR(200),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $pdo->exec($createExerciseLibraryTable);
        echo "<p style='color: green;'>✓ Exercise library table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Exercise library table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking exercise library table: " . $e->getMessage() . "</p>";
}

// Check if ai_coach_tips table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'ai_coach_tips'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'ai_coach_tips' does not exist. Creating it...</p>";

        $createAiCoachTipsTable = "
        CREATE TABLE ai_coach_tips (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            tip_text TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";

        $pdo->exec($createAiCoachTipsTable);
        echo "<p style='color: green;'>✓ AI coach tips table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ AI coach tips table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking ai coach tips table: " . $e->getMessage() . "</p>";
}

// Check if fitness_goals table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'fitness_goals'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'fitness_goals' does not exist. Creating it...</p>";

        $createFitnessGoalsTable = "
        CREATE TABLE fitness_goals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(100) NOT NULL,
            description TEXT,
            target_date DATE,
            progress_percentage INT DEFAULT 0,
            status ENUM('not_started', 'in_progress', 'completed', 'failed') DEFAULT 'not_started',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";

        $pdo->exec($createFitnessGoalsTable);
        echo "<p style='color: green;'>✓ Fitness goals table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Fitness goals table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking fitness goals table: " . $e->getMessage() . "</p>";
}

// Check if body_measurements table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'body_measurements'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'body_measurements' does not exist. Creating it...</p>";

        $createBodyMeasurementsTable = "
        CREATE TABLE body_measurements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            weight DECIMAL(5,2),
            height DECIMAL(5,2),
            body_fat_percentage DECIMAL(4,2),
            chest DECIMAL(5,2),
            waist DECIMAL(5,2),
            hips DECIMAL(5,2),
            measurement_date DATE DEFAULT (CURRENT_DATE),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";

        $pdo->exec($createBodyMeasurementsTable);
        echo "<p style='color: green;'>✓ Body measurements table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Body measurements table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking body measurements table: " . $e->getMessage() . "</p>";
}

// Check if classes table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'classes'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'classes' does not exist. Creating it...</p>";

        $createClassesTable = "
        CREATE TABLE classes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            schedule VARCHAR(255),
            trainer_id INT,
            max_participants INT DEFAULT 10,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $pdo->exec($createClassesTable);
        echo "<p style='color: green;'>✓ Classes table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Classes table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking classes table: " . $e->getMessage() . "</p>";
}

// Check if enrollments table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'enrollments'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'enrollments' does not exist. Creating it...</p>";

        $createEnrollmentsTable = "
        CREATE TABLE enrollments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            class_id INT NOT NULL,
            enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (class_id) REFERENCES classes(id)
        )";

        $pdo->exec($createEnrollmentsTable);
        echo "<p style='color: green;'>✓ Enrollments table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Enrollments table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking enrollments table: " . $e->getMessage() . "</p>";
}

// Check if workout_schedules table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'workout_schedules'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'workout_schedules' does not exist. Creating it...</p>";

        $createWorkoutSchedulesTable = "
        CREATE TABLE workout_schedules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_id INT NOT NULL,
            schedule_name VARCHAR(255) NOT NULL,
            schedule_type ENUM('manual', 'ai_generated') DEFAULT 'manual',
            target_muscle_groups TEXT,
            training_goals TEXT,
            difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
            schedule_data JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (member_id) REFERENCES users(id)
        )";

        $pdo->exec($createWorkoutSchedulesTable);
        echo "<p style='color: green;'>✓ Workout schedules table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Workout schedules table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking workout schedules table: " . $e->getMessage() . "</p>";
}

// Check if schedule_days table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'schedule_days'");
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p>Table 'schedule_days' does not exist. Creating it...</p>";

        $createScheduleDaysTable = "
        CREATE TABLE schedule_days (
            id INT AUTO_INCREMENT PRIMARY KEY,
            schedule_id INT NOT NULL,
            day_of_week INT NOT NULL, -- 0-6 for Sunday-Saturday
            day_name VARCHAR(50) NOT NULL, -- e.g., 'Senin', 'Monday'
            exercises JSON, -- Store exercises as JSON
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (schedule_id) REFERENCES workout_schedules(id)
        )";

        $pdo->exec($createScheduleDaysTable);
        echo "<p style='color: green;'>✓ Schedule days table created successfully.</p>";
    } else {
        echo "<p style='color: green;'>✓ Schedule days table exists.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error checking schedule days table: " . $e->getMessage() . "</p>";
}

echo "<h2>Database initialization complete!</h2>";
echo "<p>You should now be able to access your website normally.</p>";