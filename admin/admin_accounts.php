<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Accounts Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        .dashboard {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .heading {
            font-size: 2.5rem;
            color: #333;
            margin: 0;
            text-transform: capitalize;
        }

        .add-admin-btn {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .add-admin-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .admin-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
            font-size: 0.9rem;
            border-bottom: 2px solid #dee2e6;
            width: 300px;
        }

        .admin-table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            color: #555;
        }

        .admin-table tr:hover {
            background-color: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .delete-btn, .update-btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            margin: 0 !important;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
            max-width: 80px;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        .update-btn {
            background: #007bff;
            color: white;
        }

        .update-btn:hover {
            background: #0056b3;
        }

        .empty {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            color: #666;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .dashboard {
                padding: 1rem;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .heading {
                font-size: 2rem;
            }

            .admin-table {
                display: block;
                overflow-x: auto;
            }
        }
        .flex-dashboard{
            display: flex;
        }
        .dashboard{
            height: calc(100vh - 3px);
            overflow: auto;
        }
    </style>
</head>
<body>
    <div class="flex-dashboard">
        <?php
        include '../components/connect.php';
        session_start();
        
        $admin_id = $_SESSION['admin_id'];
        if(!isset($admin_id)){
            header('location:admin_login.php');
        }
        
        if(isset($_GET['delete'])){
            $delete_id = $_GET['delete'];
            $delete_admins = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
            $delete_admins->execute([$delete_id]);
            header('location:admin_accounts.php');
        }
        
        include '../components/admin_header.php';
        ?>

<section class="dashboard flex-col">
        <div class="dashboard-header">
            <h1 class="heading">Admin Accounts</h1>
            <a href="register_admin.php" class="add-admin-btn">
                <i class="fas fa-user-plus"></i>
                Add New Admin
            </a>
        </div>

        <?php
        $select_accounts = $conn->prepare("SELECT * FROM `admins`");
        $select_accounts->execute();
        
        if($select_accounts->rowCount() > 0){
        ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Admin ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)){ ?>
                        <tr>
                            <td><?= htmlspecialchars($fetch_accounts['id']) ?></td>
                            <td><?= htmlspecialchars($fetch_accounts['name']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="admin_accounts.php?delete=<?= $fetch_accounts['id'] ?>" 
                                       onclick="return confirm('Delete this account?')" 
                                       class="delete-btn">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                    <?php if($fetch_accounts['id'] == $admin_id){ ?>
                                        <a href="update_profile.php" class="update-btn">
                                            <i class="fas fa-edit"></i> Update
                                        </a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="empty">No accounts available!</p>
        <?php } ?>
    </section>
    </div>

    <script src="../js/admin_script.js"></script>
</body>
</html>