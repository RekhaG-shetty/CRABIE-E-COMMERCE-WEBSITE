<?php

    include("dataBase.php");

    $message = '';

	if(isset($_POST["login"])){
		$formdata = array();

		if(empty($_POST["email"])){
			$message .= 'Email Address is required';
		}
		else{
			if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
				$message .= 'Invalid Email Address';
			}
			else{
				$formdata['email'] = $_POST['email'];
			}
		}

		if(empty($_POST['password'])){
			$message .= 'Password is Required';
		}
		else{
			$formdata['password'] = $_POST['password'];
		}

		if($message == ''){
			$data = array(
				':email' => $formdata['email']
			);

			$query = "
				SELECT * FROM admin 
				WHERE admin_email = :email
			";

			$statement = $connection->prepare($query);
			$statement->execute($data);

			if($statement->rowCount() > 0){
				foreach($statement->fetchAll() as $row){
                    if($row['admin_pwd'] == $formdata['password']){
                        $_SESSION['admin-name']  = $row['admin_name'];
                        $_SESSION['admin-email'] = $row['admin_email'];
                        $_SESSION['admin-id']    = $row['admin_id'];
                        header('location:admin-panel');
                    }
                    else{
                        $message = 'Wrong Password';
                    }
                }
			}
			else{
				$message = 'Wrong Email Address';
			}
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Crabie</title>
  <link rel="stylesheet" href="./css/style.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f6f6f6;
      margin: 0;
      padding: 0;
    }

    .login-container {
      max-width: 400px;
      margin: 100px auto;
      padding: 40px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #1a1e23;
    }

    .login-container label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
      color: #333;
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
    }

    .submit {
      width: 100%;
      padding: 12px;
      background-color: #000;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .submit:hover {
      background-color: #333;
    }

    .center{
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .alert-danger{
        background-color: rgb(244, 86, 86);
        color: black;
        padding: 0px 50px 10px 50px;
        margin-bottom: 10px;
        padding-top: 10px;
        border-radius: 50px;
    }
  </style>
</head>
<body>

    <!-- Login Form -->
    <div class="login-container">
        <h2>Welcome to Crabie</h2>
		<?php 
            if($message != ''){
                echo '
                    <div class="center">
                        <div class="alert-danger" role="alert">
                            '.$message.'
                        </div>
                    </div>
                ';
            }
		?>
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required />

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required />

            <input type="submit" value="Login" name="login" class="submit">
        </div>
        </form>
    </div>

  
</body>
</html>
