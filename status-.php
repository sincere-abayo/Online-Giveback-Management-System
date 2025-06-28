<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    include 'configs.php'; // Your database connection

    $id = intval($_POST['id']);

    // Update status to 'Paid' (assuming status is stored as a string or set to 1 for paid)
    $sql = "UPDATE donation SET status = 'UnPaid' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
