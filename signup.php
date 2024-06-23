<!-- NOTE
SINGLE PAGE FORM ALONG WITH VALIDATION
NO PHP LEAKS BACK TO THE INDEX 
 -->
<?php
require_once("Includes/session.php");
$nameErr = $phoneErr = $addrErr = $emailErr = $passwordErr = $confpasswordErr = "";
$name = $email = $password = $confpassword = $address = "";
$flag=0;
$error="";

//CHECK IF A VALID FORM STRING
function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

if(isset($_POST["reg_submit"])) {
        $email = test_input($_POST['email']); 
        $password = test_input($_POST["inputPassword"]);
        $confpassword = test_input($_POST["confirmPassword"]);
        $address = test_input($_POST["address"]);
        $email = test_input($_POST['email']);

        // NAME VALIDATION
        if (empty($_POST["name"])) {
            $error = "Name is required";
            $flag=1;
            // echo $nameErr;
        } else {
            $name = test_input($_POST["name"]);
            // check if name only contains letters and whitespace
            if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
                $error = "Only letters and white space allowed"; 
                $flag=1;
                // echo $error;
            }
        }

        // EMAIL VALIDATION
        if (empty($_POST["email"])) {
            $error = "Email is required";
            $flag=1;
            } else {
            $email = test_input($_POST["email"]);
            // check if e-mail address is well-formed
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format"; 
                $flag=1;
                // echo $emailErr;
            }
        }

// if (empty($_POST["inputPassword"])) {
//     $error = "PASSWORD missing";
//     $flag = 1;
// } else {
//     $password = $_POST["inputPassword"];
//     // Check password length
//     if (strlen($password) < 8) {
//         $error = "Password must be at least 8 characters long";
//         $flag = 1;
//     }
// }

// // CONFIRM PASSWORD
// if (empty($_POST["confirmPassword"])) {
//     if ($error != "") {
//         $error .= " and ";
//     }
//     $error .= "Confirm Password is missing";
//     $flag = 1;
// } else {
//     if (isset($password) && $_POST['confirmPassword'] == $password) {
//         $confpassword = $_POST["confirmPassword"];
//     } else {
//         if ($error != "") {
//             $error .= " and ";
//         }
//         $error .= "Passwords do not match!";
//         $flag = 1;
//     }
// }

if (empty($_POST["inputPassword"])) {
    $error = "PASSWORD missing";
    $flag = 1;
} else {
    $password = $_POST["inputPassword"];
    // Check password length
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
        $flag = 1;
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Password must contain at least one numeric character";
        $flag = 1;
    } elseif (!preg_match('/[\W_]/', $password)) { // \W matches any non-word character (special character), _ is added to include underscore as well.
        $error = "Password must contain at least one special character";
        $flag = 1;
    }
}

// CONFIRM PASSWORD
if (empty($_POST["confirmPassword"])) {
    if ($error != "") {
        $error .= " and ";
    }
    $error .= "Confirm Password is missing";
    $flag = 1;
} else {
    if (isset($password) && $_POST['confirmPassword'] == $password) {
        $confpassword = $_POST["confirmPassword"];
    } else {
        if ($error != "") {
            $error .= " and ";
        }
        $error .= "Passwords do not match!";
        $flag = 1;
    }
}


        // ADDRESS VALIDATION
        if (empty($_POST["address"])) {
            $error = "Address is required";
            $flag=1;
            echo $error;
        } else {
            $address = test_input($_POST["address"]);
            // check if address only contains letters and whitespace
            // if (!preg_match("/^[a-zA-Z1-9]*$/",$address)) {
            //     $addrErr = "Only letters, numbers and white space allowed";
            //     // $flag=1; 
            //     echo $addrErr;
            // }
        }

        //CONTACT VALIDATION
        if (empty($_POST["contactNo"])) {
            $flag = 1;
            $contactNo = "";
        } else {
            $contactNo = ($_POST["contactNo"]);
            if (!preg_match("/^\d{10}$/", $_POST["contactNo"])) { // Corrected the regex pattern
                $error = "10 digit phone number allowed."; // Set the error message
                $flag = 1;
                // echo $phoneErr; // Print the error message
            }
        }
        

        // Only if succeed from the validation thourough put  
        // echo $flag; 
        // if($flag == 0)
        // {
        //     require_once("Includes/config.php");
        //     $sql = "INSERT INTO user (`name`,`email`,`phone`,`pass`,`address`)
        //             VALUES('$name','$email','$contactNo','$password','$address')";
        //             echo $sql;
        //     if (!mysqli_query($con,$sql))
        //     {
        //         die('Error: ' . mysqli_error($con));
        //     }
        //     header("Location:index.php");
        // }

        if($flag == 0)
        {
            require_once("Includes/config.php");
            $sql = "SELECT email FROM user WHERE email='$email'";
            $result = mysqli_query($con,$sql);
            $count = mysqli_num_rows($result);
            if($count==0)
            {
            $sql = "INSERT INTO user (`name`,`email`,`phone`,`pass`,`address`)
                    VALUES('$name','$email','$contactNo','$password','$address')";
                    echo $sql;
            if (!mysqli_query($con,$sql))
            {
                die('Error: ' . mysqli_error($con));
            }
            $_SESSION['signup_success'] = "Your account has been created successfully!";
            // Redirect to index.php
            header("Location: index.php");
            exit(); // Ensure that no further code is executed after the redirect
            }
            else{
                $_SESSION['exist'] = "User already exist! Please login.";
                header("Location: index.php");
            }
        }
        else
        {
            $_SESSION['signup_fail'] = $error;
            header("Location: index.php");
        }
    }
?>

<?php
    // if(isset($flag)) {
    //     if($flag === 0) {
    //         echo '
    //             <table class="table"> 
    //             <tr class="success">Account Created</tr>
    //             </table>
    //         ';
    //     } elseif ($flag === 1) {
    //         echo '
    //             <table class="table"> 
    //             <tr class="danger">There were errors in the form.</tr>
    //             </table>
    //         ';
    //     } 
    // }
?>
<form action="signup.php" method="post" class="form-horizontal" role="form" onsubmit="return validateForm()">
    <div class="row form-group">
        <div class="col-md-12">
            <input type="name" class="form-control" name="name" id="name" placeholder="Full Name" required>
            <!-- <label><?php echo $nameErr;?></label> -->
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
            <!-- <label><?php echo $emailErr;?></label> -->
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <input type="password" class="form-control" name="inputPassword" id="inputPassword" placeholder="Password" required>
            <!-- <label><?php echo $passwordErr;?></label> -->
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm Password" required>
            <!-- <label><?php echo $confpasswordErr;?></label><label><?php echo $confpasswordErr;?></label> -->
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <input type="tel" class="form-control" name="contactNo" placeholder="Contact No." required>
            <!-- <label><?php echo $phoneErr;?></label> -->
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <input type="address" class="form-control" name="address" placeholder="Address" required>
            <!-- <label><?php echo $addrErr;?></label> -->
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-10">
            <button name="reg_submit" class="btn btn-primary">Register</button>
        </div>
    </div>

</form>
