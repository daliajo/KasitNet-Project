<?php
include 'session_check.php';

$conn = new mysqli('localhost', 'root', '', 'kasitnet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//fetch user information 
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


// $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - KasitNet</title>
    <link rel="icon" type="image/x-icon" href="logo11.ico">
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js" defer></script>
</head>
<body>
<header>
        <img src="logo1.png" alt="KasitNet Logo" style="height: 70px; margin-right: -10px; vertical-align: middle;">
        <h1>KasitNet</h1>
    <nav class="header-right">
        <ul>
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="bookings.html">Bookings</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="facility_report.html">Usage Report</a></li>
        </ul>
        <button class="btn-small" onclick="logoutUser()">Logout</button>
    </nav>
</header>

<main>
    <section id="home">
        <div class="content-wrapper">
            <div class="profile-section">
                <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
                <p style="text-align:left">Manage your profile and bookings from this dashboard.</p>
                <div class="profile-card">
                    <img src="profile.png" alt="profile" style="height: 100px; margin-right: -10px; vertical-align: middle;">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <button class="btn-primary" onclick="window.location.href='edit_profile.php';">Edit Profile</button>

                </div>
            </div>


            <section id="recent-bookings">
    <div class="bookings-section">
        <h3>Recent Bookings</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Facility</th>
                    <th>Time</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //fetch recent bookings
                $result = $conn->query("SELECT facilities.name AS facility_name, bookings.date, bookings.time 
                                        FROM bookings 
                                        JOIN facilities ON bookings.facility_id = facilities.id 
                                        ORDER BY bookings.date DESC, bookings.time DESC 
                                        LIMIT 5");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['facility_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['time']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['date']) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3" style="text-align: center;">No bookings available.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>






        </div>
    </section>
</main>


<section id="notifications">
    <div class="content-wrapper">
        <h3>Notifications</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Date</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //fetch recent notifications 
                $result = $conn->query("SELECT 
                                            bookings.date, 
                                            bookings.time, 
                                            facilities.name AS facility_name, 
                                            'Booking confirmed' AS message 
                                        FROM bookings 
                                        JOIN facilities ON bookings.facility_id = facilities.id 
                                        ORDER BY bookings.date DESC, bookings.time DESC 
                                        LIMIT 5"); //limit to the 5 most recent notifications

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['time']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['date']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['message']) . ': ' . htmlspecialchars($row['facility_name']) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3" style="text-align: center;">No notifications available.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>



<footer>
    <p>&copy; 2025 KasitNet</p>
</footer>
</body>
</html>