<?php

include 'session_check.php';
$conn = new mysqli('localhost', 'root', '', 'kasitnet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

//fetch current user data
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT name, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $name = $row['name'];
        $email = $row['email'];
    } else {
        echo "Error fetching user data.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    //validate input
    if (empty($name) || empty($email)) {
        echo "Name and Email are required.";
        exit();
    }

    //update query
    if ($password) {
        $query = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $name, $email, $password, $user_id);
    } else {
        $query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $name, $email, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;

        header('Location: index.php');
        exit(); 
    } else {
        echo "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="icon" type="image/x-icon" href="logo11.ico">
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script> 
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        #profile-section {
            background-color: #ffffff; 
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        #profile-section h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #1a2238; 
        }

        #editProfileForm label {
            display: block;
            text-align: left;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2e3b4e; 
        }

        #editProfileForm input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #c5cbd5;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        #editProfileForm button {
            background-color: #1a2238; 
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        #editProfileForm button:hover {
            background-color: #3b4967; 
        }

        /*responsive design */
        @media (max-width: 480px) {
            #profile-section {
                padding: 20px;
            }

            #editProfileForm input {
                font-size: 14px;
            }

            #editProfileForm button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div id="profile-section">
        <h2>Edit Profile</h2>
        <form id="editProfileForm" action="edit_profile.php" method="POST">
            <label for="profile-name">Name:</label>
            <input type="text" id="profile-name" name="name" required value="<?php echo htmlspecialchars($name); ?>">

            <label for="profile-email">Email:</label>
            <input type="email" id="profile-email" name="email" required value="<?php echo htmlspecialchars($email); ?>">

            <label for="profile-password">Password (optional):</label>
            <input type="password" id="profile-password" name="password">

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
