<?php

    include("dataBase.php");
    include("functions.php");

    $message = '';

	if(isset($_POST['regBtn'])){
		$formData = array();

        $formData['name'] = $_POST['name'];

        $formData['phoneNum'] = $_POST['phoneNum'];

		if(!filter_var($_POST["userEmail"], FILTER_VALIDATE_EMAIL)){
			$message .= '<li>Invalid Email Address</li>';
		}
		else{
			$formData['userEmail'] = $_POST['userEmail'];
		}

        $formData['password'] = $_POST['password'];

        $formData['address'] = $_POST['address'];

        $formData['landmark'] = $_POST['landmark'];

        $formData['city'] = $_POST['city'];

        $formData['state'] = $_POST['state'];

        $formData['pincode'] = $_POST['pincode'];

		if($message == ''){
			$data = array(
				':userEmail' => $formData['userEmail'],
                ':phoneNum'     => $formData['phoneNum']
			);

			$query = "
				SELECT name FROM users
				WHERE email = :userEmail
                OR phone = :phoneNum
			";

			$statement = $connection->prepare($query);
			$statement->execute($data);

			if($statement->rowCount() > 0){
				$message = '<li>User Already Exist, Please Login</li>';
			}	
			else{
                $data = array(
                    ':userName'			    =>	$formData['name'],
                    ':phoneNum'			    =>	$formData['phoneNum'],
                    ':userEmail'			=>	$formData['userEmail'],
                    ':password'	            =>	$formData['password'],
                    ':address'			    =>	$formData['address'],
                    ':landmark'			    =>	$formData['landmark'],
                    ':city'			        =>	$formData['city'],
                    ':state'			    =>	$formData['state'],
                    ':pincode'			    =>	$formData['pincode']
                );

                $query = "
                    INSERT INTO users
                    (name, phone, email, password, address, landmark, city, state, pincode) 
                    VALUES (:userName, :phoneNum, :userEmail, :password, :address, :landmark, :city, :state, :pincode)
			    ";

                $statement = $connection->prepare($query);
                $statement->execute($data);
                header('location:register.php?msg=registerd');
            }
		}
	}

    $msg = '';

	if(isset($_POST["loginBtn"])){
		$formdata = array();

		if(!filter_var(trim($_POST["loginID"]), FILTER_VALIDATE_EMAIL)){
			$msg .= '<li>Invalid Email Address</li>';
		}
		else{
			$formdata['loginID'] = $_POST['loginID'];
		}

		$formdata['loginPassword'] = $_POST['loginPassword'];

        if($msg == ''){
            $data = array(
                ':loginID' => $formdata['loginID']
            );
            $query = "
			SELECT * FROM users
			WHERE email = :loginID
			";

            $statement = $connection->prepare($query);
            $statement->execute($data);

            if($statement->rowCount() > 0){
                foreach($statement->fetchAll() as $row){
                    if($row['password'] == $formdata['loginPassword']){
                        $_SESSION['id']         = $row['id'];
                        $_SESSION['number']     = $row['phone'];
                        $_SESSION['name']       = $row['name'];
                        if (!empty($_SESSION['cart'])) {
                            syncGuestCartToDB($row['id']); // Uff merge guest cart into DB
                        }
                        restoreCartFromDB($row['id']);
                        $_SESSION['welcome_back'] = "Welcome back, " . $_SESSION['name'] . "!";
                        
                        if (isset($_SESSION['redirect_after_login'])) {
                            $redirectTo = $_SESSION['redirect_after_login'];
                            unset($_SESSION['redirect_after_login']); // clear the session value
                            header("Location: $redirectTo");
                            exit;
                        }
                        else{
                            header("Location: index.php");
                        }
                    }
                    else{
                        $msg = '<li>Incorrect Password</li>';
                    }
                }
			}
		}
		else{
			$msg = '<li>Wrong Email Address</li>';
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login & Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f8;
    }
    header {
      background-color: #fff;
      color: black;
      text-align: center;
      padding: 20px 5%;
      font-size: 1.5rem;
      font-weight: bold;
      font-weight: 800;
      font-size: 24px;
      border-bottom: 1px solid #ddd;
    }

    a{
        text-decoration: none;
    }

    .auth-wrapper {
        max-width: 500px;
        margin: 60px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 40px;
    }

    .toggle-btns {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .toggle-btns button {
        width: 48%;
        padding: 10px;
        font-weight: 500;
        border-radius: 8px;
    }

    .form-section {
        display: none;
    }

    .form-section.active {
        display: block;
    }

    .form-label {
        font-weight: 500;
        color: #444;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 0.95rem;
    }

    .btn-primary {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        margin-top: 15px;
        background-color: #000000ff;
        border: none;
    }

    .btn-primary:hover{
        background-color: #000000ff;
    }

    .btn-outline-primary1{
        border: 1px solid #000000ff;
        color: #000000ff;
    }

    .btn-outline-primary1:hover{
        background-color: #000000ff;
        color:rgb(255, 255, 255);
    }

    .center{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .alert-danger{
        margin-top: 10px;
        padding: 0px 50px 0px 50px;
        margin-bottom: 10px;
        padding-top: 10px;
        border-radius: 8px;
        background-color:rgb(252, 92, 92);
        font-weight: bold;
    }
    .alert-success{
        margin-top: 10px;
        padding: 0px 50px 0px 50px;
        margin-bottom: 10px;
        padding-top: 10px;
        border-radius: 8px;
        background-color:rgba(95, 252, 92, 1);
        font-weight: bold;
    }
    </style>
</head>
<body>

    <a href="index"><header>Crabie</header></a>

    <div class="auth-wrapper">        
        <div class="toggle-btns">
            <button id="show-login" class="btn btn-outline-primary1">Login</button>
            <button id="show-register" class="btn btn-primary">Register</button>
        </div>
        

    <?php 
        if($message != ''){
            echo '
                <div class="center">
                    <div class="alert fade show alert-danger" role="alert">
                    <ul class="list-unstyled">'.$message.'</ul>
                    </div>
                </div>
            ';
        }
    ?>

	<?php 
        if($msg != ''){
            echo '
                <div class="center">
                    <div class="alert fade show alert-danger" role="alert">
                    <ul class="list-unstyled">'.$msg.'</ul>
                    </div>
                </div>
            ';
        }
	?>

	<?php 
        if(isset($_GET['msg'])){
            $logMsg = $_GET['msg'];
            echo '
                <div class="center">
                    <div class="alert fade show alert-success" role="alert">
                    <ul class="list-unstyled">Registered Successfully! Please Login</ul>
                    </div>
                </div>
            ';
        }
	?>

        <!-- Register Form -->
        <div id="register-section" class="form-section active">
            <h4 class="mb-3">Create Account</h4>
            <form id="register-form" method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="number" class="form-control" name="phoneNum" id="phoneNum" pattern="[0-9]{10}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="userEmail" id="userEmail" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea type="text" class="form-control" id="address" name="address" required ></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Landmark</label>
                    <input type="text" class="form-control" id="landmark" name="landmark" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">State</label>
                    <input type="text" class="form-control" id="state" name="state" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Pincode</label>
                    <input type="text" class="form-control" id="pincode" name="pincode" pattern="[0-9]{6}" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required />
                </div>
                <input type="submit" name="regBtn" class="btn btn-primary w-100" value="Register">
            </form>
        </div>

        <!-- Login Form -->
        <div id="login-section" class="form-section">
            <h4 class="mb-3">Login</h4>
            <form id="login-form" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" name="loginID" id="loginID" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="loginPassword" id="loginPassword" required>
                </div>
                <input type="submit" name="loginBtn" class="btn btn-primary w-100" value="Login">
            </form>
        </div>
    </div>

    <script>
        // Preloader
        window.addEventListener("load", function () {
            const preloader = document.getElementById("preloader");
            preloader.style.display = "none";

            // Auto-switch to login tab if redirected from successful register
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get("login") === "1") {
                showLogin.classList.replace('btn-outline-primary1', 'btn-primary');
                showRegister.classList.replace('btn-primary', 'btn-outline-primary1');
                loginSection.classList.add('active');
                registerSection.classList.remove('active');
            }
        });

        // Tab toggle styling
        const showLogin = document.getElementById('show-login');
        const showRegister = document.getElementById('show-register');
        const loginSection = document.getElementById('login-section');
        const registerSection = document.getElementById('register-section');

        showLogin.addEventListener('click', () => {
            showLogin.classList.replace('btn-outline-primary1', 'btn-primary');
            showRegister.classList.replace('btn-primary', 'btn-outline-primary1');
            loginSection.classList.add('active');
            registerSection.classList.remove('active');
        });

        showRegister.addEventListener('click', () => {
            showRegister.classList.replace('btn-outline-primary1', 'btn-primary');
            showLogin.classList.replace('btn-primary', 'btn-outline-primary1');
            registerSection.classList.add('active');
            loginSection.classList.remove('active');
        });
    </script>
</body>
</html>