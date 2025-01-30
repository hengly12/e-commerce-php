<?php
// Include database connection details
include('../includes/db_config.php');

// Get data sent from client
$data = json_decode(file_get_contents("php://input"), true);

// Extract data
$rating = $data['rating'];
$customerName = $data['name']; // Assuming 'name' is sent from the client for customer's name
$email = $data['email']; // Assuming 'email' is sent from the client for customer's email
$feedback = $data['feedback'];


// Insert feedback into database
$query = "INSERT INTO feedback (rating, customer_name, email, feedback) VALUES ('$rating', '$customerName', '$email', '$feedback')";
if ($conn->query($query) === TRUE) {
    echo "success"; // Return success message
} else {
    echo "Error submitting feedback: " . $conn->error;
}
?>

