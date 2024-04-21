<?php
require(__DIR__ . "/partials/nav.php");
?>

<style>
    /*background */
    html{
        background-image: url("https://marketplace.canva.com/EAFPnhwhzx4/1/0/900w/canva-yellow-daisy-cute-flower-iphone-wallpaper-XUyIHx9eH2Q.jpg");  
    }

    .box {
    background-color: white;
    padding: 10px; /* Adjust as needed */
    border: whitesmoke; /* Add border for the box */
    border-radius: 5px; /* Optional: Add rounded corners */
    width: 500; /* Fixed width */
}
</style>

<h1>Home</h1>
<?php
if (is_logged_in()) {
    echo "<span class='logged-in'>Welcome, " . get_user_email();
} else {
    echo "<span class='not-logged-in'>You're not logged in</span>";
}
//shows session info
echo '<div class="box">';
echo "<pre>" . var_export($_SESSION, true) . "</pre>";
echo '</div>';

echo "<pre>" . var_export($_SESSION, true) . "</pre>";
?>