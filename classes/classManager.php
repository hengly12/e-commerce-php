<?php
class CartManager {
    private $db;
    private $user_id;
    
    public function __construct($db, $user_id = null) {
        $this->db = $db;
        $this->user_id = $user_id;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }
    
    public function deleteCartItem($cart_id) {
        $this->db->query("DELETE FROM `cart` WHERE id = ?");
        return $this->db->execute([$cart_id]);
    }
    
    public function deleteAllItems() {
        $this->db->query("DELETE FROM `cart` WHERE user_id = ?");
        return $this->db->execute([$this->user_id]);
    }
    
    public function updateQuantity($cart_id, $quantity) {
        $this->db->query("UPDATE `cart` SET quantity = ? WHERE id = ?");
        return $this->db->execute([$quantity, $cart_id]);
    }
    
    public function getCartItems() {
        $this->db->query("SELECT * FROM `cart` WHERE user_id = ?");
        $this->db->execute([$this->user_id]);
        return $this->db->resultSet();
    }
    
    public function getGrandTotal() {
        $items = $this->getCartItems();
        return array_reduce($items, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }
    
    public function hasItems() {
        $this->db->query("SELECT COUNT(*) as count FROM `cart` WHERE user_id = ?");
        $this->db->execute([$this->user_id]);
        $result = $this->db->single();
        return $result['count'] > 0;
    }
}