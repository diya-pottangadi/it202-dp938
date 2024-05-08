<style>
    /*background */
    html{
        background-image: url("https://marketplace.canva.com/EAFPnhwhzx4/1/0/900w/canva-yellow-daisy-cute-flower-iphone-wallpaper-XUyIHx9eH2Q.jpg");  
    }

    .box {
    background-color: white;
    padding: 10px; 
    border: whitesmoke; 
    border-radius: 5px; 
    width: 500;

}
</style>

<?php
require(__DIR__ . "/partials/nav.php");
?>

<div class = "box">

<?php
$db = getDB();
$stmt = $db->prepare("SELECT account_number, account_type, modified, balance FROM Accounts WHERE user_id = :user_id LIMIT 5");
$user_id = $_SESSION["user"]["id"];
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($accounts) {
    echo "<h1>My Accounts</h1>";
    echo "<table>";
    echo "<tr><th>Account Number</th><th>Account Type</th><th>Modified</th><th>Balance</th></tr>";
    foreach ($accounts as $account) {
        echo "<tr>";
        echo "<td>" . $account["account_number"] . "</td>";
        echo "<td>" . $account["account_type"] . "</td>";
        echo "<td>" . $account["modified"] . "</td>";
        echo "<td>$" . number_format($account["balance"], 2) . "</td>";
        echo "<td><a href='transaction_history.php?account_number=" . urlencode($account["account_number"]) . "'>View Transactions</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No accounts found.</p>";
}
?>
</div>