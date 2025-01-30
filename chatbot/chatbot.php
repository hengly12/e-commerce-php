<?php
// Include database connection details
include('../includes/db_config.php');

// Start session
session_start();

// Get user message
$message = $_POST['message'];

// Process user message
$response = processMessage($message, $conn); // Pass $conn as a parameter

// Send response
echo $response;
// Function to process user message
function processMessage($message, $conn) { // Accept $conn as a parameter
    $botname = "Agrina";
    // Get current hour
    $currentHour = date('G');

    // Greetings
    $greetings = array("hello", "hi", "hey", "howdy", "good morning", "good afternoon", "good evening");
    $messageLowercase = strtolower($message); // Convert message to lowercase
    if (in_array($messageLowercase, $greetings)) {
        if ($messageLowercase == "good morning" && $currentHour >= 5 && $currentHour < 12) {
            return "Good morning buddy! I'm $botname , your chat assistant. How can I assist you today?";
        } elseif ($messageLowercase == "good afternoon" && $currentHour >= 12 && $currentHour < 18) {
            return "Good afternoon buddy! I'm $botname , your chat assistant. How can I assist you today?";
        } elseif ($messageLowercase == "good evening" && ($currentHour >= 18 || $currentHour < 5)) {
            return "Good evening buddy! ! I'm $botname , your chat assistant. How can I assist you today?";
        } else {
            return "Hello there! I'm $botname , your chat assistant. How can I assist you today?";
        }
    }

    // Retrieve FAQs from the database
    $faqs = getFAQsFromDatabase($conn);

    // Loop through the retrieved FAQs
    // foreach ($faqs as $faq) {
    //     $question = strtolower($faq['question']);
    //     $answer = $faq['answer'];
        // Check if the user's message contains the FAQ question
    //     if (strpos($message, $question) !== false) {
    //         return $answer;
    //     }
    // }
    // Retrieve FAQ answer based on user's message
    $faqAnswer = retrieveFAQAnswer($message, $faqs);

    // If FAQ answer is found
    if ($faqAnswer !== false) {
        return $faqAnswer;
    }

    // Handle order cancellation
    if (strpos($message, "cancel order") !== false) {
        // Extract order ID from the message
        $orderId = extractOrderId($message);
        // Check if order ID is provided
        if ($orderId) {
            // Ask for user's name to confirm identity
            $_SESSION['cancel_order_id'] = $orderId; // Store order ID in session
            return "To confirm your identity, please provide your name associated with the order.";
        } else {
            return "Please provide a valid order ID to cancel an order.";
        }
    }

    // Handle user's name for order cancellation confirmation
    if (isset($_SESSION['cancel_order_id'])) {
        // Confirm identity and ask for order cancellation confirmation
        if (isset($_SESSION['confirm_cancel_name'])) {
            if (strtolower($message) === 'yes') {
                $orderId = $_SESSION['cancel_order_id'];
                $customerName = $_SESSION['confirm_cancel_name'];
                unset($_SESSION['cancel_order_id'], $_SESSION['confirm_cancel_name']);
                
                $query = "UPDATE orders SET status = 'Cancelled' WHERE order_id = '$orderId' AND customer_name = '$customerName'";
                if ($conn->query($query) === TRUE) {
                    $orderId = strtoupper($orderId);
                    return "Your order $orderId has been cancelled successfully.";
                } else {
                    return "Error cancelling the order: " . $conn->error;
                }
            } elseif (strtolower($message) === 'no') {
                unset($_SESSION['cancel_order_id'], $_SESSION['confirm_cancel_name']);
                return "Order cancellation process cancelled.";
            } else {
                return "Please respond with 'yes' or 'no' to confirm order cancellation.";
            }
        } else {
            $_SESSION['confirm_cancel_name'] = ucfirst($message);
            $orderId = strtoupper($_SESSION['cancel_order_id']);
            return "Identity confirmed! Hey {$_SESSION['confirm_cancel_name']}, are you sure you want to cancel your order {$_SESSION['cancel_order_id']}?";
        }
    }


    // Handle order tracking
    if (strpos($message, "track order") !== false || strpos($message, "track my order") !== false || strpos($message, "show order status of") !== false) {
        // Extract tracking number from the message
        $trackingNumber = extractTrackingNumber($message);
        
        // Query the database to retrieve order information
        $orderInfo = getOrderInfo($trackingNumber, $conn);
        $customerSupport = "<a href='tel:+18001234567'>1-800-123-4567</a>"; // Customer support number with on-click link
        
        if ($orderInfo) {
            // Construct and return response with order details
            $statusMessage = getOrderStatusMessage($orderInfo['status']);
            return "Your order status for Order ID $trackingNumber is: " . $statusMessage. ". To know more, please feel free to contact our customer support agent $customerSupport.";
        } else {
            return "Sorry, we couldn't find any order with Order ID $trackingNumber. Please verify the Order ID and try again.";
        }
    }
    

   // Handle request to show all available products
    if (strpos($message, "show me products") !== false || strpos($message, "show products") !== false || strpos($message, "show me all available products") !== false || strpos($message, "what are the products you have") !== false) {
        // Query the database to retrieve all available products
        $products = getAllProducts($conn);
    
        if ($products) {
        // Construct and return response with all available products in table format
            $response = "Here are all available products:\n";
            $response .= "<table><tr><th>Name</th><th>Price</th></tr>";
            foreach ($products as $product) {
                $response .= "<tr><td>" . $product['name'] . "</td><td>$" . $product['price'] . "</td></tr>";
            }
            $response .= "</table>";
            return $response;
        } else {
            return "Sorry, we couldn't retrieve the list of available products at the moment. Please try again later.";
        }
        
    }

    

    // Handle purchase option
    if (strpos($message, "buy") !== false || strpos($message, "purchase") !== false) {
        // Check if the user wants to buy something
        $product = extractProductName($message, $conn);
        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = array(); // Initialize cart as an array if not already set
            }
            $_SESSION['cart'][] = array('name' => $product['name'], 'price' => $product['price']); // Default quantity set to 1
            return "Can you tell me the quantity?";
        } else {
            return "I'm sorry, I didn't catch that. Could you please specify the product you want to buy?";
        }
    }

    // Handle quantity input
    if (isset($_SESSION['cart']) && preg_match('/(\d+\.?\d*)\s*(liters|kg|kgs|pounds|lbs?)/', $message, $matches)) {
        // Extract the quantity and unit
        $quantity = intval($matches[1]);
        $unit = $matches[2];
        // Update the quantity in the cart
        $lastProductIndex = count($_SESSION['cart']) - 1;
        $_SESSION['cart'][$lastProductIndex]['quantity'] = $quantity;
        $_SESSION['cart'][$lastProductIndex]['unit'] = $unit;
        return "Added to cart. Do you want to buy something else?";
    }

    // Handle additional purchase
    if (isset($_SESSION['cart']) && strtolower($message) === 'yes') {
        return "What else would you like to buy?";
    }

    // Handle checkout
    if (isset($_SESSION['cart']) && strtolower($message) === 'no') {
        // Prepare invoice
        $invoice = "<table>
                      <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                      </tr>";
        $totalPrice = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal = $item['quantity'] * $item['price'];
            $invoice .= "<tr>
                            <td>{$item['name']}</td>
                            <td>{$item['quantity']} {$item['unit']}</td>
                            <td>$subtotal</td>
                          </tr>";
            $totalPrice += $subtotal;
        }
        $invoice .= "<tr>
                        <td colspan='2'>Total</td>
                        <td>$totalPrice</td>
                     </tr>
                     </table>";

        // Reset cart
        unset($_SESSION['cart']);

        // Provide checkout link
        return "Here is your invoice:\n$invoice\nClick the link to continue purchase: <a href='checkout.php'>Checkout</a>\n\nIs there something else that I can help you with?";
    }
    
    // Feedback Handle
    if (strpos($message, "bye") !== false || strpos($message, "Bye") !== false || strpos($message, "BYE") !== false || strpos($message, "seeyou soon") !== false || strpos($message, "Seeyou Soon") !== false) {
        // Initiate feedback process
        $_SESSION['rate_experience'] = true;
        return "<div class='feedbacksection'>
        <div class='feedbackform'>
        Goodbye! Kindly rate your experience with us: <br>
          <label></label>
          <br>
          <input type='text' name='customer_name' id='customerName' placeholder='name'>
          <br>
          <label></label>
          <br>
          <input type='email' name='email' id='customerEmail' placeholder='email'>
          <br>
          <label></label>
          <br>
          <textarea name='feedback' id='feedbackText' placeholder='feedback'></textarea>
          <br>
          <label></label>
          <br>
          <input type='radio' name='rating' id='rating' value='Very Bad'>😡 <input type='radio' name='rating' id='rating' value='bad'>😕 <input type='radio' name='rating' id='rating' value='Average'>😐 <input type='radio' name='rating' id='rating' value='Good'>🙂 <input type='radio' name='rating' id='rating' value='Very Good'>😃 <br>
          <button onclick='submitFeedback()'>Submit</button>
        </div>
        <div class='thankyousection' style='display: none''>
        Thank you for your feedback
        </div>
        ";
    }

    // Default response
    return "I'm sorry, I didn't understand that. Could you please say that in another way?";
}

// Function to extract tracking number from message
function extractTrackingNumber($message) {
    // Implement logic to extract tracking number from the message
    // For demonstration purposes, let's assume the tracking number is everything after "track order"
    $startIndex = strpos($message, "track order") + strlen("track order");
    return trim(substr($message, $startIndex));
}

// Function to retrieve order information from the database
function getOrderInfo($trackingNumber, $conn) {
    // Implement logic to retrieve order information from the database based on the tracking number
    // For demonstration purposes, let's assume there is a table named 'orders' with columns 'tracking_number' and 'status'
    $trackingNumber = mysqli_real_escape_string($conn, $trackingNumber);
    $query = "SELECT * FROM orders WHERE order_id = '$trackingNumber'";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

// Function to get status message based on order status
function getOrderStatusMessage($status) {
    switch ($status) {
        case 'shipped':
            return "Your order has been shipped and will be arriving in 4-5 working days.";
        case 'dispatched':
            return "Your order has been dispatched and will be arriving in 7 working days.";
        case 'refunded':
            return "Your order has been refunded.";
        case 'awaiting for payment':
            return "Your order is awaiting payment update the payment status if needed";
        // Add more cases for other order statuses as needed
        default:
            return "Your order status: $status";
    }
}

// Function to retrieve all available products from the database
function getAllProducts($conn) {
    // Implement logic to retrieve all available products from the database
    // For demonstration purposes, let's assume there is a table named 'products' with columns 'name', 'price', 'image', 'description', 'badge', and 'original_price'
    $query = "SELECT * FROM products";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $products = array();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    } else {
        return false;
    }
}

// Function to retrieve FAQs from the database
function getFAQsFromDatabase($conn) {
    $faqs = array();

    // Query the FAQs from the database
    $query = "SELECT * FROM faqs";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // Fetch FAQs and store them in an array
        while ($row = $result->fetch_assoc()) {
            $faqs[] = array(
                'question' => $row['question'],
                'answer' => $row['answer']
            );
        }
    }

    return $faqs;
}

// Function to retrieve FAQ answer based on user's message
function retrieveFAQAnswer($message, $faqs) {
    $messageLowercase = strtolower($message); // Convert message to lowercase

    // Loop through the retrieved FAQs
    foreach ($faqs as $faq) {
        $question = strtolower($faq['question']);
        $answer = $faq['answer'];
        
        // Check if the user's message contains the FAQ question
        if (strpos($messageLowercase, $question) !== false) {
            return $answer;
        }
    }

    // If no FAQ matches the user's message
    return false;
}
// Function to extract order ID from message for cancellation
function extractOrderId($message) {
    // Implement logic to extract order ID from the message
    // For demonstration purposes, let's assume the order ID is everything after "cancel order"
    $startIndex = strpos($message, "cancel order") + strlen("cancel order");
    return trim(substr($message, $startIndex));
}

// Function to extract product name and price from message
function extractProductName($message, $conn) {
    // Extract product name
    $startIndex = strpos($message, "buy") !== false ? strpos($message, "buy") + strlen("buy") : strpos($message, "purchase") + strlen("purchase");
    $productName = trim(substr($message, $startIndex));

    // Query the database to fetch product details
    $productName = mysqli_real_escape_string($conn, $productName);
    $query = "SELECT * FROM products WHERE name = '$productName'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        return array('name' => $productName, 'price' => $product['price']);
    } else {
        return false;
    }
}

// Function to calculate total amount
function calculateTotalAmount($cart) {
    $total = 0;
    foreach ($cart as $product) {
        $total += $product['price'] * $product['quantity'];
    }
    return $total;
}

?>

<script>
function submitFeedback() {
    // Get the feedback text
    var feedback = document.getElementById('feedbackText').value;
    var customerName = document.getElementById('customerName').value;
    var customerEmail = document.getElementById('customerEmail').value;
    var rating = document.querySelector('input[name="rating"]:checked').value;

    // Send the rating and feedback to the server
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "chatbot/save_feedback.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Handle the response from the server
            if (xhr.responseText.trim() === "success") {
                // Hide the feedback form
                document.querySelector('.feedbackform').style.display = 'none';
                
                // Show the thank you message
                document.querySelector('.thankyousection').style.display = 'block';
            } else {
                // Handle error if needed
                console.error("Error submitting feedback:", xhr.responseText);
            }
        }
    };
    xhr.send(JSON.stringify({ rating: rating, name: customerName, email: customerEmail, feedback: feedback }));
}


</script>