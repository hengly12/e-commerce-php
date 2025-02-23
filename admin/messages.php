<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
    header('location:admin_login.php');
}

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
    $delete_message->execute([$delete_id]);
    header('location:messages.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        .messages-section {
            padding: 2rem;
            width: 100%;
            flex-direction: column;
        }

        .table-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 2rem;
        }

        .messages-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .messages-table th {
            background: #f8f9fa;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #dee2e6;
        }

        .messages-table td {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            color: #444;
        }

        .messages-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .message-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table-actions {
            display: flex;
            gap: 0.5rem;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .empty-message {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
            font-size: 1.1rem;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .total-messages {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            color: #495057;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="flex-dash">
        <?php include '../components/admin_header.php'; ?>
        
        <section class="messages-section">
            <div class="section-header">
                <h1 class="heading">Messages Dashboard</h1>
                <?php
                $count_messages = $conn->prepare("SELECT COUNT(*) FROM `messages`");
                $count_messages->execute();
                $total = $count_messages->fetchColumn();
                ?>
                <span class="total-messages">Total Messages: <?= $total ?></span>
            </div>

            <div class="table-container">
                <div class="table-wrapper">
                    <?php
                    $select_messages = $conn->prepare("SELECT * FROM `messages` ORDER BY id DESC");
                    $select_messages->execute();
                    if($select_messages->rowCount() > 0){
                    ?>
                    <table class="messages-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Number</th>
                                <th>Message</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)){
                            ?>
                            <tr>
                                <td><?= $fetch_message['id']; ?></td>
                                <td><?= $fetch_message['user_id']; ?></td>
                                <td><?= htmlspecialchars($fetch_message['name']); ?></td>
                                <td><?= htmlspecialchars($fetch_message['email']); ?></td>
                                <td><?= htmlspecialchars($fetch_message['number']); ?></td>
                                <td class="message-content" title="<?= htmlspecialchars($fetch_message['message']); ?>">
                                    <?= htmlspecialchars($fetch_message['message']); ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="messages.php?delete=<?= $fetch_message['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this message?');" 
                                           class="delete-btn">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                    }else{
                        echo '<p class="empty-message">No messages found</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>

    <script src="../js/admin_script.js"></script>
</body>
</html>