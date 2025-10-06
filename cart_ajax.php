<?php
session_start();
include 'db_connect.php';

if ($_POST['action'] == 'change_qty') {
    $id = (int)$_POST['id'];
    $qty_action = $_POST['qty_action'];
    if ($qty_action == 'plus') {
        $_SESSION['cart'][$id]++;
    } elseif ($qty_action == 'minus' && $_SESSION['cart'][$id] > 1) {
        $_SESSION['cart'][$id]--;
    }
}
if ($_POST['action'] == 'remove') {
    $id = (int)$_POST['id'];
    unset($_SESSION['cart'][$id]);
}

include 'cart_table_content.php';
?>
