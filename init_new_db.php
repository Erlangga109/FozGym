<?php
// Script untuk menginisialisasi database dbfozgym dan membuat semua tabel yang diperlukan
require_once 'config.php';
require_once 'db.php';

try {
    // Koneksi ke database
    $pdo = get_pdo();
    
    // Membuat tabel users
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('owner', 'trainer', 'customer') DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'users' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel schedules
    $sql = "CREATE TABLE IF NOT EXISTS schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        trainer_id INT NOT NULL,
        date DATE NOT NULL,
        time_start TIME NOT NULL,
        time_end TIME NOT NULL,
        status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES users(id),
        FOREIGN KEY (trainer_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'schedules' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel members
    $sql = "CREATE TABLE IF NOT EXISTS members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        membership_type VARCHAR(50),
        start_date DATE,
        end_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'members' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel attendance
    $sql = "CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        date DATE NOT NULL,
        check_in_time TIME,
        check_out_time TIME,
        status ENUM('present', 'absent', 'late') DEFAULT 'present',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'attendance' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel payments
    $sql = "CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50),
        transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'payments' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel workout_logs jika digunakan
    $sql = "CREATE TABLE IF NOT EXISTS workout_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        exercise VARCHAR(100) NOT NULL,
        sets INT,
        reps INT,
        weight DECIMAL(5,2),
        duration INT, -- durasi dalam menit
        calories_burned INT,
        log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'workout_logs' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel exercise_library jika digunakan
    $sql = "CREATE TABLE IF NOT EXISTS exercise_library (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        category VARCHAR(50) NOT NULL,
        description TEXT,
        difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
        video_url VARCHAR(200),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'exercise_library' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel ai_coach_tips jika digunakan
    $sql = "CREATE TABLE IF NOT EXISTS ai_coach_tips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        tip_text TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'ai_coach_tips' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel fitness_goals jika digunakan
    $sql = "CREATE TABLE IF NOT EXISTS fitness_goals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        target_date DATE,
        progress_percentage INT DEFAULT 0,
        status ENUM('not_started', 'in_progress', 'completed', 'failed') DEFAULT 'not_started',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'fitness_goals' berhasil dibuat atau sudah ada!\n";
    
    // Membuat tabel body_measurements jika digunakan
    $sql = "CREATE TABLE IF NOT EXISTS body_measurements (
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
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Tabel 'body_measurements' berhasil dibuat atau sudah ada!\n";
    
    // Menambahkan user owner default jika belum ada
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'owner'");
    $ownerCount = $stmt->fetchColumn();
    
    if ($ownerCount == 0) {
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['admin', 'admin@fozgym.com', $defaultPassword, 'owner']);
        echo "✓ User owner default ditambahkan (username: admin, password: admin123)\n";
    } else {
        echo "ℹ️  User owner sudah ada, melewati pembuatan user default\n";
    }
    
    echo "\nDatabase 'dbfozgym' telah siap digunakan!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}