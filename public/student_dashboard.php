<?php
require_once '../auth.php';

if (!isLoggedIn() || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit;
}

$entries = getLogbookEntries($_SESSION['user_id']);
$marks = getStudentMarks($_SESSION['user_id']);
$percentage = calculatePercentage($marks);
$hasWorkingHours = !empty($entries);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 h-screen">
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">Student Dashboard</h1>
            <div class="flex items-center">
                <span class="mr-4">Welcome, <?= $_SESSION['username'] ?></span>
                <a href="logout.php" class="text-white hover:underline">Logout</a>
            </div>
        </div>
    </header>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mt-6">Your Logbook Entries</h2>
        <a href="logbook_entry.php" class="text-blue-500 hover:underline">Add New Entry</a>

        <!-- Logbook Entries Table -->
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th class="py-2 px-4 bg-gray-200">Date</th>
                        <th class="py-2 px-4 bg-gray-200">Time</th>
                        <th class="py-2 px-4 bg-gray-200">Days</th>
                        <th class="py-2 px-4 bg-gray-200">Week</th>
                        <th class="py-2 px-4 bg-gray-200">Activity</th>
                        <th class="py-2 px-4 bg-gray-200">Hours</th>
                        <th class="py-2 px-4 bg-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr class="hover:bg-gray-100">
                        <td class="py-2 px-4"><?= $entry['entry_date'] ?></td>
                        <td class="py-2 px-4"><?= $entry['entry_time'] ?></td>
                        <td class="py-2 px-4"><?= $entry['days'] ?></td>
                        <td class="py-2 px-4"><?= $entry['week'] ?></td>
                        <td class="py-2 px-4"><?= $entry['activity_description'] ?></td>
                        <td class="py-2 px-4"><?= $entry['working_hours'] ?></td>
                        <td class="py-2 px-4">
                            <a href="edit_entry.php?id=<?= $entry['id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                            <a href="delete_entry.php?id=<?= $entry['id'] ?>" class="text-red-500 hover:underline ml-2">Delete</a>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Export Button -->
        <div class="mt-4">
            <a href="export_entries.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Export Entries to CSV</a>
        </div>

        <!-- Marks and Percentage -->
        <h2 class="text-2xl font-bold mt-6">Your Marks</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th class="py-2 px-4 bg-gray-200">Course</th>
                        <th class="py-2 px-4 bg-gray-200">Mark</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($marks as $mark): ?>
                    <tr class="hover:bg-gray-100">
                        <td class="py-2 px-4"><?= $mark['course_name'] ?></td>
                        <td class="py-2 px-4"><?= $mark['mark'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <h3 class="text-xl font-bold">Percentage</h3>
            <p><?= number_format($percentage, 2) ?>%</p>
        </div>

        <!-- Data Visualization -->
        <?php if ($hasWorkingHours): ?>
        <div class="mt-6">
            <canvas id="logbookChart"></canvas>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($hasWorkingHours): ?>
    <script>
        // Data for visualization
        const logbookData = <?= json_encode($entries) ?>;
        const dates = logbookData.map(entry => entry.entry_date);
        const hours = logbookData.map(entry => entry.working_hours);

        const ctx = document.getElementById('logbookChart').getContext('2d');
        const logbookChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Working Hours',
                    data: hours,
                    backgroundColor: 'rgba(245, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
