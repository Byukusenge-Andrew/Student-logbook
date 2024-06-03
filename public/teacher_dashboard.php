<?php
require_once '../auth.php';

if (!isLoggedIn() || $_SESSION['role'] != 'teacher') {
    header('Location: login.php');
    exit;
}

$students = getAllStudentsWithMarks();
$courses = getAllCourses();
$marksSummary = getMarksSummary();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_student'])) {
        $studentId = $_POST['student_id'];
        deleteStudent($studentId);
        header('Location: teacher_dashboard.php');
        exit;
    } elseif (isset($_POST['create_student'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        createStudent($username, $email, $password);
        header('Location: teacher_dashboard.php');
        exit;
    } elseif (isset($_POST['create_course'])) {
        $courseName = $_POST['course_name'];
        createCourse($courseName);
        header('Location: teacher_dashboard.php');
        exit;
    } elseif (isset($_POST['create_mark'])) {
        $studentId = $_POST['student_id'];
        $courseId = $_POST['course_id'];
        $mark = $_POST['mark'];
        createMark($studentId, $courseId, $mark);
        header('Location: teacher_dashboard.php');
        exit;
    } elseif (isset($_POST['update_mark'])) {
        $markId = $_POST['mark_id'];
        $mark = $_POST['mark'];
        updateMark($markId, $mark);
        header('Location: teacher_dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 h-screen">
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">Teacher Dashboard</h1>
            <div class="flex items-center">
                <span class="mr-4">Welcome, <?= $_SESSION['username'] ?></span>
                <a href="logout.php" class="text-white hover:underline">Logout</a>
            </div>
        </div>
    </header>
    <div class="container mx-auto p-4">
        <div class="mt-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold">Manage Students</h2>
            <button onclick="document.getElementById('createStudentModal').classList.remove('hidden');" class="text-blue-500 hover:underline">Create Student</button>
        </div>

        <!-- Students Table -->
        <div class="mt-6 overflow-x-auto">
            <table id="studentsTable" class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th class="py-2 px-4 bg-gray-200">ID</th>
                        <th class="py-2 px-4 bg-gray-200">Username</th>
                        <th class="py-2 px-4 bg-gray-200">Email</th>
                        <th class="py-2 px-4 bg-gray-200">Marks</th>
                        <th class="py-2 px-4 bg-gray-200">Percentage</th>
                        <th class="py-2 px-4 bg-gray-200">Created At</th>
                        <th class="py-2 px-4 bg-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $student): ?>
                    <?php 
                        $marks = getStudentMarks($student['id']);
                        $percentage = calculatePercentage($marks);
                    ?>
                    <tr class="hover:bg-gray-100">
                        <td class="py-2 px-4 border"><?= $student['id'] ?></td>
                        <td class="py-2 px-4 border"><?= $student['username'] ?></td>
                        <td class="py-2 px-4 border"><?= $student['email'] ?></td>
                        <td class="py-2 px-4 border">
                            <ul>
                                <?php foreach ($marks as $mark): ?>
                                    <li><?= $mark['course_name'] ?>: <?= $mark['mark'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td class="py-2 px-4 border"><?= number_format($percentage, 2) ?>%</td>
                        <td class="py-2 px-4 border"><?= $student['created_at'] ?></td>
                        <td class="py-2 px-4 border">
                            <form method="post" class="inline">
                                <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                                <button type="submit" name="delete_student" class="text-red-500 hover:underline">Delete</button>
                            </form>
                            <button onclick="openEditMarksModal(<?= $student['id'] ?>)" class="text-blue-500 hover:underline">Edit Marks</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold">Manage Courses</h2>
            <button onclick="document.getElementById('createCourseModal').classList.remove('hidden');" class="text-blue-500 hover:underline">Create Course</button>
            <div class="mt-4">
                <ul>
                    <?php foreach ($courses as $course): ?>
                        <li class="bg-white shadow-md rounded-lg p-4 mt-2"><?= $course['course_name'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold">Manage Marks</h2>
            <button onclick="document.getElementById('createMarkModal').classList.remove('hidden');" class="text-blue-500 hover:underline">Create Mark</button>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold">Student Performance Graph</h2>
            <canvas id="marksChart" width="400" height="200"></canvas>
        </div>
        <div class="mt-4">
            <button onclick="exportTableToCSV('students_marks.csv')" class="bg-green-500 text-white font-bold py-2 px-4 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">Export to CSV</button>
        </div>
    </div>

    <!-- Create Student Modal -->
    <div id="createStudentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Create Student</h2>
            <form method="post">
                <div class="mb-4">
                    <label for="username" class="block font-bold mb-2">Username</label>
                    <input type="text" id="username" name="username" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('createStudentModal').classList.add('hidden');" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 mr-2">Cancel</button>
                    <button type="submit" name="create_student" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Course Modal -->
    <div id="createCourseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Create Course</h2>
            <form method="post">
                <div class="mb-4">
                    <label for="course_name" class="block font-bold mb-2">Course Name</label>
                    <input type="text" id="course_name" name="course_name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('createCourseModal').classList.add('hidden');" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 mr-2">Cancel</button>
                    <button type="submit" name="create_course" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Mark Modal -->
    <div id="createMarkModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Create Mark</h2>
            <form method="post">
                <div class="mb-4">
                    <label for="student_id" class="block font-bold mb-2">Student</label>
                    <select id="student_id" name="student_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student['id'] ?>"><?= $student['username'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="course_id" class="block font-bold mb-2">Course</label>
                    <select id="course_id" name="course_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"><?= $course['course_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="mark" class="block font-bold mb-2">Mark</label>
                    <input type="number" id="mark" name="mark" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('createMarkModal').classList.add('hidden');" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 mr-2">Cancel</button>
                    <button type="submit" name="create_mark" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Marks Modal -->
    <div id="editMarksModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Edit Marks</h2>
            <form method="post">
                <input type="hidden" id="edit_student_id" name="student_id">
                <div id="editMarksContainer"></div>
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('editMarksModal').classList.add('hidden');" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 mr-2">Cancel</button>
                    <button type="submit" name="update_mark" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditMarksModal(studentId) {
            // Fetch student marks via AJAX or pre-populate in the backend
            const marks = <?= json_encode($students) ?>.find(student => student.id === studentId).marks;
            const container = document.getElementById('editMarksContainer');
            container.innerHTML = '';
            marks.forEach(mark => {
                container.innerHTML += `
                    <div class="mb-4">
                        <label class="block font-bold mb-2">${mark.course_name}</label>
                        <input type="number" name="marks[${mark.id}]" value="${mark.mark}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                    </div>
                `;
            });
            document.getElementById('edit_student_id').value = studentId;
            document.getElementById('editMarksModal').classList.remove('hidden');
        }

        function exportTableToCSV(filename) {
            const table = document.getElementById('studentsTable');
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                
                for (let j = 0; j < cols.length; j++) {
                    row.push(cols[j].innerText);
                }
                
                csv.push(row.join(','));
            }

            const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
            const downloadLink = document.createElement('a');
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }

        const ctx = document.getElementById('marksChart').getContext('2d');
        const marksChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($marksSummary, 'username')) ?>,
                datasets: [{
                    label: 'Average Marks',
                    data: <?= json_encode(array_column($marksSummary, 'average_mark')) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
</body>
</html>
