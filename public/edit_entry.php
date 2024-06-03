<?php
require_once '../auth.php';

if (!isLoggedIn() || $_SESSION['role'] != 'student') {
    header('Location: login.php');
    exit;
}

$entryId = $_GET['id'];
$entry = getLogbookEntry($entryId, $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission and update the entry
    $updatedEntry = [
        'entry_date' => $_POST['entry_date'],
        'entry_time' => $_POST['entry_time'],
        'days' => $_POST['days'],
        'week' => $_POST['week'],
        'activity_description' => $_POST['activity_description'],
        'working_hours' => $_POST['working_hours']
    ];

    updateLogbookEntry($entryId, $_SESSION['user_id'], $updatedEntry);
    header('Location: student_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Logbook Entry</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold">Edit Logbook Entry</h1>
        <form method="post" class="mt-4 bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="entry_date" class="block font-bold mb-2">Entry Date</label>
                <input type="date" id="entry_date" name="entry_date" value="<?= htmlspecialchars($entry['entry_date']) ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
            </div>
            <div class="mb-4">
                <label for="entry_time" class="block font-bold mb-2">Entry Time</label>
                <input type="time" id="entry_time" name="entry_time" value="<?= htmlspecialchars($entry['entry_time']) ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
            </div>
            <div class="mb-4">
                <label for="days" class="block font-bold mb-2">Days</label>
                <input type="text" id="days" name="days" value="<?= htmlspecialchars($entry['days']) ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
            </div>
            <div class="mb-4">
                <label for="week" class="block font-bold mb-2">Week</label>
                <input type="text" id="week" name="week" value="<?= htmlspecialchars($entry['week']) ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
            </div>
            <div class="mb-4">
                <label for="activity_description" class="block font-bold mb-2">Activity Description</label>
                <textarea id="activity_description" name="activity_description" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required><?= htmlspecialchars($entry['activity_description']) ?></textarea>
            </div>
            <div class="mb-4">
                <label for="working_hours" class="block font-bold mb-2">Working Hours</label>
                <input type="number" id="working_hours" name="working_hours" value="<?= htmlspecialchars($entry['working_hours']) ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>
