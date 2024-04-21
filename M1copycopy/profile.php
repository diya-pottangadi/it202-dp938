<style>
    /* background */
    html{
        background-image: url("https://marketplace.canva.com/EAFPnhwhzx4/1/0/900w/canva-yellow-daisy-cute-flower-iphone-wallpaper-XUyIHx9eH2Q.jpg");
    }
    /* form styling */
    form {
        max-width: 500px;
        margin: 0;
        padding: 20px;
        background-color: white;
        border-radius: 15px;
        box-sizing: border-box;
        border-radius: 10px;
    }
    label {
        display: inline-block;
        margin-bottom: 4px;
    }
    /* text boxes */
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        box-sizing: border-box;
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
</style>

<?php
//session_start();
require(__DIR__ . "/partials/nav.php");
//require_once(__DIR__ . "/flash_messages.php"); // Include flash_messages.php to access flash() function

echo '<h2>Profile Page</h2>';
echo '<img src="https://i.pinimg.com/474x/74/fc/99/74fc99a07a4aae549b90a5f46ac3de0b.jpg" alt="Profile Picture" style="max-width: 20%; height: 20%;">';

if (is_logged_in()) {
    echo "You're logged in under: " . $_SESSION['user']['email'];
} else {
    echo "</br></br>You're Not Logged In!</br></br>";
}
?>
<?php
if (isset($_POST["save"])) {
    $email = se($_POST, "email", null, false);
    $username = se($_POST, "username", null, false);

    $params = [":email" => $email, ":username" => $username, ":id" => get_user_id()];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
    try {
        $stmt->execute($params);
        flash("Profile saved", "success");
    } catch (Exception $e) {
        if ($e->errorInfo[1] === 1062) {
            //https://www.php.net/manual/en/function.preg-match.php
            preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
            if (isset($matches[1])) {
                flash("The chosen " . $matches[1] . " is not available.", "warning");
            } else {
                //TODO come up with a nice error message
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            //TODO come up with a nice error message
            echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
        }
    }
    //select fresh data from table
    $stmt = $db->prepare("SELECT id, email, username from Users where id = :id LIMIT 1");
    try {
        $stmt->execute([":id" => get_user_id()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            //$_SESSION["user"] = $user;
            $_SESSION["user"]["email"] = $user["email"];
            $_SESSION["user"]["username"] = $user["username"];
        } else {
            flash("User doesn't exist", "danger");
        }
    } catch (Exception $e) {
        flash("An unexpected error occurred, please try again", "danger");
        //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
    }


    //check/update password
    $current_password = se($_POST, "currentPassword", null, false);
    $new_password = se($_POST, "newPassword", null, false);
    $confirm_password = se($_POST, "confirmPassword", null, false);
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            //TODO validate current
            $stmt = $db->prepare("SELECT password from Users where id = :id");
            try {
                $stmt->execute([":id" => get_user_id()]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($result["password"])) {
                    if (password_verify($current_password, $result["password"])) {
                        $query = "UPDATE Users set password = :password where id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->execute([
                            ":id" => get_user_id(),
                            ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                        ]);

                        flash("Password reset", "success");
                    } else {
                        flash("Current password is invalid", "warning");
                    }
                }
            } catch (Exception $e) {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            flash("New passwords don't match", "warning");
        }
    }
}
?>

<?php
$email = get_user_email();
$username = get_username();
?>
<form method="POST" onsubmit="return validate(this);">
    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php se($email); ?>" />
    </div>
    <div class="mb-3">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?php se($username); ?>" />
    </div>
    <!-- DO NOT PRELOAD PASSWORD -->
   
    <div style="center; font:serif; font-size: 20px; font-weight: bold; color: black; margin-bottom: 10px;">Password Reset:</div>

    <div class="mb-3">
        <label for="cp">Current Password</label>
        <input type="password" name="currentPassword" id="cp" />
    </div>
    <div class="mb-3">
        <label for="np">New Password</label>
        <input type="password" name="newPassword" id="np" />
    </div>
    <div class="mb-3">
        <label for="conp">Confirm Password</label>
        <input type="password" name="confirmPassword" id="conp" />
    </div>
    <input type="submit" value="Update Profile" name="save" />
</form>

<script>
    function validate(form) {
        let pw = form.newPassword.value;
        let con = form.confirmPassword.value;
        let isValid = true;
        //TODO add other client side validation....

        //example of using flash via javascript
        //find the flash container, create a new element, appendChild
        if (pw !== con) {
            //find the container
            let flash = document.getElementById("flash");
            //create a div (or whatever wrapper we want)
            let outerDiv = document.createElement("div");
            outerDiv.className = "row justify-content-center";
            let innerDiv = document.createElement("div");

            //apply the CSS (these are bootstrap classes which we'll learn later)
            innerDiv.className = "alert alert-warning";
            //set the content
            innerDiv.innerText = "Password and Confirm password must match";

            outerDiv.appendChild(innerDiv);
            //add the element to the DOM (if we don't it merely exists in memory)
            flash.appendChild(outerDiv);
            isValid = false;
        }
        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/partials/flash.php");
?>