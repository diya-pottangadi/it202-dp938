<?php

session_start();
require(__DIR__ . "/partials/nav.php");

//php code for log out message
echo "<br>\n";
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    echo "You have been logged out <br>";
}

?>

<h1>  Login Page: Login Here </h1>

<style>
    /*background */
    html{
        background-image: url("https://marketplace.canva.com/EAFPnhwhzx4/1/0/900w/canva-yellow-daisy-cute-flower-iphone-wallpaper-XUyIHx9eH2Q.jpg");  
    }
    h1, a{
        font-size: large;
        color: black;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        box-sizing: border-box;
        border-radius: 10px;
    }

    input[type="submit"] {
        background-color: #8A9A5B;
        color: white;
        padding: 10px 20px;
        border-radius: 0px;
    }

    input[type="submit"]:hover {
        background-color: green;
    }

    form {
        max-width: 500px;
        margin: 0;
        padding: 20px;
        background-color: white;
        border-radius: 15px;
    }
</style>


<form onsubmit="return validate(this)" method="POST">
    <div>
        <br>
        <label for="email">Email: </label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="pw">Password: </label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <input type="submit" value="Login" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php
//TODO 2: add PHP Code

if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);

    //TODO 3
    $hasError = false;
    if (empty($email)) {
        echo "Email must not be empty";
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        echo "Invalid email address";
        $hasError = true;
    }
    if (empty($password)) {
        echo "password must not be empty";
        $hasError = true;
    }
    if (strlen($password) < 8) {
        echo "Password too short";
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, password, username from Users where email = :email");
        try {
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        echo "Weclome $email";
                        $_SESSION["user"] = $user;
                        $_SESSION["user_id"] = $user_id;
                        try {
                            //lookup potential roles
                            $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                            $stmt->execute([":user_id" => $user["id"]]);
                            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        } catch (Exception $e) {
                            error_log(var_export($e, true));
                        }
                        //save roles or empty array
                        if (isset($roles)) {
                            $_SESSION["user"]["roles"] = $roles; //at least 1 role
                        } else {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }


                        die(header("Location: home.php"));
                    } else {
                        echo "Invalid password";
                    }
                } else {
                    echo "Email not found";
                }
            }
        } catch (Exception $e) {
            echo "<pre>" . var_export($e, true) . "</pre>";
        }
    }
}
?>