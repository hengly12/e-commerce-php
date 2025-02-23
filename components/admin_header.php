<?php

if (isset($message)) {
    echo '<div class="message-container">';
    
    $messages = is_array($message) ? $message : [$message];
    
    foreach ($messages as $msg) {
        $type = 'info'; 
        $icon = 'info-circle'; 
        
        if (stripos($msg, 'success') !== false) {
            $type = 'success';
            $icon = 'check-circle';
        } elseif (stripos($msg, 'error') !== false || 
                  stripos($msg, 'already exist') !== false || 
                  stripos($msg, 'not matched') !== false) {
            $type = 'error';
            $icon = 'exclamation-circle';
        } elseif (stripos($msg, 'warning') !== false) {
            $type = 'warning';
            $icon = 'exclamation-triangle';
        }
        
        echo '
        <div class="message message--' . $type . '" role="alert">
            <div class="message__content">
                <i class="fas fa-' . $icon . ' message__icon" aria-hidden="true"></i>
                <span class="message__text">' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</span>
            </div>
            <button type="button" class="message__close" onclick="closeMessage(this);" aria-label="Close message">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>';
    }
    
    echo '</div>';
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <style>
      .header .flex .navbar a {
         text-decoration: none;
         color: black;
         padding: 10px;
      }

      .header .flex .navbar a.active {
         color: white;
         background: #007bff;
         border-radius: 5px;
         font-weight: bold;
      }
   </style>
</head>
<body>

<header class="header">
<section class="flex">
      <nav class="navbar">
         <a href="products.php" class="<?= ($current_page == 'products.php') ? 'active' : ''; ?>">Products</a>
         <a href="admin_accounts.php" class="<?= ($current_page == 'admin_accounts.php') ? 'active' : ''; ?>">Create User</a>
         <a href="messages.php" class="<?= ($current_page == 'messages.php') ? 'active' : ''; ?>">Contact</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="update_profile.php" class="btn">update profile</a>
         <a href="../components/admin_logout.php" class="delete-btn" onclick="return confirm('logout from the website?');">logout</a> 
      </div>

   </section>
</header>
<script>
const MessageHandler = {
    /**
     * @param {HTMLElement} element - The close button element
     */
    closeMessage: function(element) {
        const messageElement = element.closest('.message');
        if (!messageElement) return;

        messageElement.classList.add('message--closing');
        
        messageElement.style.opacity = '0';
        messageElement.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            messageElement.remove();
            this.checkContainer();
        }, 300); 
    },


    closeAllMessages: function() {
        const messages = document.querySelectorAll('.message');
        messages.forEach(message => {
            message.style.opacity = '0';
            message.style.transform = 'translateX(20px)';
            setTimeout(() => message.remove(), 300);
        });
        this.checkContainer();
    },

    /**
     * Close messages by type
     * @param {string} type - Message type (success, error, warning, info)
     */
    closeMessagesByType: function(type) {
        const messages = document.querySelectorAll(`.message--${type}`);
        messages.forEach(message => {
            message.style.opacity = '0';
            message.style.transform = 'translateX(20px)';
            setTimeout(() => message.remove(), 300);
        });
        this.checkContainer();
    },

    /**
     * Auto-close messages after a delay
     * @param {number} delay - Delay in milliseconds before closing
     * @param {string} type - Optional: specific type of messages to auto-close
     */
    autoCloseMessages: function(delay = 5000, type = null) {
        const selector = type ? `.message--${type}` : '.message';
        const messages = document.querySelectorAll(selector);
        
        messages.forEach(message => {
            if (message.classList.contains('message--persistent')) return;
            
            setTimeout(() => {
                if (document.body.contains(message)) {
                    message.style.opacity = '0';
                    message.style.transform = 'translateX(20px)';
                    setTimeout(() => message.remove(), 300);
                    this.checkContainer();
                }
            }, delay);
        });
    }
};

const style = document.createElement('style');
style.textContent = `
    .message {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    .message--closing {
        pointer-events: none;
    }
    
    .message-container {
        transition: opacity 0.3s ease;
    }
`;
document.head.appendChild(style);
document.addEventListener('DOMContentLoaded', () => {
    MessageHandler.autoCloseMessages(5000, 'success');
    MessageHandler.autoCloseMessages(7000, 'info');
    MessageHandler.autoCloseMessages(10000, 'warning');
    
});

</script>
</body>
</html>
