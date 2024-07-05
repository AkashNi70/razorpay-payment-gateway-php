<?php

require('config.php');
require('razorpay-php/Razorpay.php');
session_start();

use Razorpay\Api\Api;

// Initialize Razorpay API
$api = new Api($keyId, $keySecret);

$price = $_POST['price'];
$_SESSION['price'] = $price;
$customername = $_POST['name'];
$email = $_POST['email'];
$_SESSION['email'] = $email;
$contactno = $_POST['phone'];

// Generate order ID
$orderId = "order_id" . rand(10000, 99999);

$orderData = [
    'receipt'         => $orderId,
    'amount'          => $price * 100, // amount in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

// Create Razorpay order
$razorpayOrder = $api->order->create($orderData);
$razorpayOrderId = $razorpayOrder['id'];
$_SESSION['razorpay_order_id'] = $razorpayOrderId;

// Prepare data for Razorpay checkout
$data = [
    "key"               => $keyId,
    "amount"            => $orderData['amount'],
    "name"              => "Dummy Data",
    "description"       => "Coding for Everyone",
    "image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
    "prefill"           => [
        "name"          => $customername,
        "email"         => $email,
        "contact"       => $contactno,
    ],
    "notes"             => [
        "address"       => "GKP",
        "merchant_order_id" => "12312321",
    ],
    "theme"             => [
        "color"         => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

if (isset($displayCurrency) && $displayCurrency !== 'INR') {
    $data['display_currency'] = $displayCurrency;
    $data['display_amount'] = $displayAmount;
}

$json = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay Payment</title>
</head>
<body>
    <style>
        .razorpay-payment-button {
            display: none;
        }
    </style>
    <form id="razorpay-form" action="verify" method="POST">
        <script
            src="https://checkout.razorpay.com/v1/checkout.js"
            data-key="<?php echo htmlspecialchars($data['key']); ?>"
            data-amount="<?php echo htmlspecialchars($data['amount']); ?>"
            data-currency="INR"
            data-name="<?php echo htmlspecialchars($data['name']); ?>"
            data-image="<?php echo htmlspecialchars($data['image']); ?>"
            data-description="<?php echo htmlspecialchars($data['description']); ?>"
            data-prefill.name="<?php echo htmlspecialchars($data['prefill']['name']); ?>"
            data-prefill.email="<?php echo htmlspecialchars($data['prefill']['email']); ?>"
            data-prefill.contact="<?php echo htmlspecialchars($data['prefill']['contact']); ?>"
            data-order_id="<?php echo htmlspecialchars($data['order_id']); ?>"
            <?php if (isset($data['display_amount']) && $data['display_amount']) { ?>
            data-display_amount="<?php echo htmlspecialchars($data['display_amount']); ?>"
            <?php } ?>
            <?php if (isset($data['display_currency']) && $data['display_currency']) { ?>
            data-display_currency="<?php echo htmlspecialchars($data['display_currency']); ?>"
            <?php } ?>
        >
        </script>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#razorpay-form").submit();
        });
    </script>
</body>
</html>
