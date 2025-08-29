<?php

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require_once 'PHPMailer/Exception.php';
	require_once 'PHPMailer/PHPMailer.php';
	require_once 'PHPMailer/SMTP.php';

    function isAdminLogin(){
        if(isset($_SESSION['admin-id'])){
		    return true;
	    }
        else{
	        return false;
        }
    }

    function convertData($string, $action = 'encrypt'){
		$encrypt_method = "AC";
		$secret_key = 'Z';
		$secret_iv = '4t';
		$key = hash('s', $secret_key);
		$iv = substr(hash('s', $secret_iv), 0, 16);
		if ($action == 'en'){
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} 
		else if ($action == 'decrypt'){
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}

	function isUser(){
        if(isset($_SESSION['id'])){
		    return true;
	    }
        else{
	        return false;
        }
    }

	function isAdmin(){
        if(isset($_SESSION['admin-id'])){
		    return true;
	    }
        else{
	        return false;
        }
    }

	function restoreCartFromDB($userId) {
		global $connection;
		$_SESSION['cart'] = []; // Clear old cart (optional)

		$stmt = $connection->prepare("SELECT product_id, size, qty FROM user_cart WHERE user_id = ?");
		$stmt->execute([$userId]);
		$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($cartItems as $item) {
			$_SESSION['cart'][] = [
				'productId' => $item['product_id'],
				'size' => $item['size'],
				'qty' => (int)$item['qty']
			];
		}
	}

	function syncGuestCartToDB($userId) {
		global $connection;

		foreach ($_SESSION['cart'] as $item) {
			// Check if the item already exists
			$stmt = $connection->prepare("SELECT qty FROM user_cart WHERE user_id = ? AND product_id = ? AND size = ?");
			$stmt->execute([$userId, $item['productId'], $item['size']]);
			$existing = $stmt->fetch();

			if ($existing) {
				// Update quantity
				$stmt = $connection->prepare("UPDATE user_cart SET qty = qty + ? WHERE user_id = ? AND product_id = ? AND size = ?");
				$stmt->execute([$item['qty'], $userId, $item['productId'], $item['size']]);
			} else {
				// Insert new
				$stmt = $connection->prepare("INSERT INTO user_cart (user_id, product_id, size, qty) VALUES (?, ?, ?, ?)");
				$stmt->execute([$userId, $item['productId'], $item['size'], $item['qty']]);
			}
		}
	}

	function sendMail($to, $subject, $body, $altBody = '') {
		$mail = new PHPMailer(true);

		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'crabieorder@gmail.com';
			$mail->Password = 'tpkf pxhz zugv bbtx';
			$mail->SMTPSecure = 'tls';
			$mail->Port = 587;

			$mail->setFrom('crabieorder@gmail.com', 'Crabie');
			$mail->addAddress($to);

			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = $altBody ?: strip_tags($body);

			$mail->send();
			return true;
		} 
		catch (Exception $e) {
			error_log("Mail Error: " . $mail->ErrorInfo);
			return false;
		}
	}

?>