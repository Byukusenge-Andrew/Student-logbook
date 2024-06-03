<?php
// auth.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// Register a new user
function register($username, $email, $password, $role) {
    global $pdo;
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO user_student (username, email, password, role) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$username, $email, $passwordHash, $role]);
}

// Login user
function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM user_student WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

// Get a specific logbook entry
function getLogbookEntry($entryId, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM logbook_entries WHERE id = ? AND user_id = ?");
    $stmt->execute([$entryId, $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update a logbook entry
function updateLogbookEntry($entryId, $userId, $updatedEntry) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE logbook_entries 
                          SET entry_date = ?, 
                              entry_time = ?,
                              days = ?,
                              week = ?,
                              activity_description = ?,
                              working_hours = ?
                          WHERE id = ? AND user_id = ?");
    return $stmt->execute([
        $updatedEntry['entry_date'],
        $updatedEntry['entry_time'],
        $updatedEntry['days'],
        $updatedEntry['week'],
        $updatedEntry['activity_description'],
        $updatedEntry['working_hours'],
        $entryId,
        $userId
    ]);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Logout user
function logout() {
    session_destroy();
}

// Create a logbook entry
function createLogbookEntry($user_id, $entry_date, $entry_time, $days, $week, $activity_description, $working_hours) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO logbook_entries (user_id, entry_date, entry_time, days, week, activity_description, working_hours) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$user_id, $entry_date, $entry_time, $days, $week, $activity_description, $working_hours]);
}

// Get logbook entries
function getLogbookEntries($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM logbook_entries WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Delete a logbook entry
function deleteLogbookEntry($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM logbook_entries WHERE id = ?");
    return $stmt->execute([$id]);
}

// Get all students (for teachers)
function getAllStudents() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM user_student WHERE role = 'student'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Forgot password - find user by email
function findUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM user_student WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Reset password
function resetPassword($email, $newPassword) {
    global $pdo;
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE user_student SET password = ? WHERE email = ?");
    return $stmt->execute([$passwordHash, $email]);
}
// Create a new student
function createStudent($username, $email, $password) {
    global $pdo;
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO user_student (username, email, password, role) VALUES (?, ?, ?, 'student')");
    return $stmt->execute([$username, $email, $passwordHash]);
}

// Delete a student by ID
function deleteStudent($studentId) {
    global $pdo;
    // Delete all logbook entries associated with the student
    $stmt = $pdo->prepare("DELETE FROM logbook_entries WHERE user_id = ?");
    $stmt->execute([$studentId]);

    // Delete the student
    $stmt = $pdo->prepare("DELETE FROM user_student WHERE id = ?");
    return $stmt->execute([$studentId]);
}
// Create a new course
function createCourse($course_name) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO courses (course_name) VALUES (?)");
    return $stmt->execute([$course_name]);
}

// Get all courses
function getAllCourses() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM courses");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create a new mark for a student
function createMark($student_id, $course_id, $mark) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO marks (student_id, course_id, mark) VALUES (?, ?, ?)");
    return $stmt->execute([$student_id, $course_id, $mark]);
}

// Get all marks for a student
function getMarksByStudent($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT m.*, c.course_name FROM marks m JOIN courses c ON m.course_id = c.id WHERE student_id = ?");
    $stmt->execute([$student_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all students with their marks
function getAllStudentsWithMarks() {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT s.*, GROUP_CONCAT(CONCAT(c.course_name, ': ', m.mark) SEPARATOR ', ') AS marks 
        FROM user_student s 
        LEFT JOIN marks m ON s.id = m.student_id 
        LEFT JOIN courses c ON m.course_id = c.id 
        WHERE s.role = 'student' 
        GROUP BY s.id
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Update a mark for a student
function updateMark($markId, $mark) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE marks SET mark = ? WHERE id = ?");
    return $stmt->execute([$mark, $markId]);
}

// Get all marks and percentage for a student
function getStudentMarks($student_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT m.id, m.mark, c.course_name 
        FROM marks m 
        JOIN courses c ON m.course_id = c.id 
        WHERE m.student_id = ?
    ");
    $stmt->execute([$student_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get summary of marks for graph data
function getMarksSummary() {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT s.username, AVG(m.mark) as average_mark 
        FROM marks m 
        JOIN user_student s ON m.student_id = s.id 
        WHERE s.role = 'student' 
        GROUP BY s.id
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Calculate percentage of marks
function calculatePercentage($marks) {
    $totalMarks = array_sum(array_column($marks, 'mark'));
    $numberOfMarks = count($marks);
    return $numberOfMarks > 0 ? ($totalMarks / $numberOfMarks) : 0;
}



