<?php

if (!isset($_POST['razorpay_payment_id'])) {
    header("Location: index");
    exit();
}

require('config.php');
session_start();

require('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;
$error = "Payment Failed";
$paymentId = null;

if (empty($_POST['razorpay_payment_id']) === false) {
    $api = new Api($keyId, $keySecret);

    try {
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    } catch (SignatureVerificationError $e) {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true) {
    $paymentId = $_POST['razorpay_payment_id'];
} else {
    $paymentId = "null....";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Razorpay Payment Gateway Integration using PHP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .fas {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            width: 100px;
            background-color: #65c18c;
            color: #fff;
            font-size: 50px;
            border-radius: 50%;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <section class="bg-light py-3 py-md-5 d-flex align-items-center" style="min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                    <div class="card border border-light-subtle rounded-3 shadow-sm">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="text-center mb-3 d-flex justify-content-center align-items-center">
                                <i class="fas fa-check" style="font-size: 2rem;"></i>
                            </div>
                            <h4 class="text-center"> Congratulations! </h4>
                            <p class="text-center"> Your payment is successful... </p>
                            <h5 class="fw-bold">Payment Id : <span><?php echo htmlspecialchars($paymentId); ?></span></h5>
                            <div class="btn-wrapper mt-4 text-center">
                                <a href="index.php" class="btn btn-primary">Go to Home</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>