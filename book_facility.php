<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You are not logged in.'); window.location.href='login.php';</script>";
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'kasitnet');

if ($conn->connect_error) {
    echo "<script>alert('Database connection failed.'); window.location.href='bookings.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $facility_id = $_POST['facility_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $description = isset($_POST['description']) ? $_POST['description'] : 'No description provided';

    //validate user ID
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Error: Invalid user ID. Please check the user information.'); window.location.href='bookings.php';</script>";
        exit();
    }

    //check if facility is available
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE facility_id = ? AND date = ? AND time = ?");
    $stmt->bind_param("iss", $facility_id, $date, $time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        //facility is available,insert the booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, facility_id, date, time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $facility_id, $date, $time);
        if ($stmt->execute()) {
            //also insert into academic schedule
            $stmt = $conn->prepare("INSERT INTO academic_schedule (user_id, facility_id, date, time, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $user_id, $facility_id, $date, $time, $description);
            if ($stmt->execute()) {
                echo "<script>alert('Booking and academic schedule updated successfully!'); window.location.href='bookings.php';</script>";
            } else {
                echo "<script>alert('Error inserting into academic schedule: " . addslashes($stmt->error) . "'); window.location.href='bookings.php';</script>";
            }
        } else {
            echo "<script>alert('Error inserting into bookings: " . addslashes($stmt->error) . "'); window.location.href='bookings.php';</script>";
        }
    } else {
        echo "<script>alert('Facility is already booked for this time.'); window.location.href='bookings.php';</script>";
    }
}

$conn->close();
?>
