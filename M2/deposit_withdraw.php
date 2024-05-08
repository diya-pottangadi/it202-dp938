<style>
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
    input[type="submit"] {
        background-color: #8A9A5B;
        color: white;
        padding: 10px 20px;
        border: white;
        border-radius: 10px;
    }

    input[type="submit"]:hover {
        background-color: green;
    }
</style>

<?php
require(__DIR__ . "/partials/nav.php");
?>

<div class = "box">
<h1>Deposit</h1>

<form method="POST">
    <label for="account">Select Account:</label>
    <select name="account" id="account">
        <?php
        
        $db = getDB();
        $stmt = $db->prepare("SELECT id, account_number, balance FROM Accounts WHERE user_id = :user_id AND account_type != 'world'");
        $stmt->execute([":user_id" => $_SESSION["user"]["id"]]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($accounts as $account) {
            echo "<option value='" . $account["id"] . "'>" . $account["account_number"] . " - $" . number_format($account["balance"], 2) . "</option>";
        }
        ?>
    </select>
    <br><br>
    <div>
        <label for="deposit_amount">Enter Deposit Amount:</label>
        <input type="number" name="deposit_amount" id="deposit_amount" min="1" required>
    </div>
    <br>
    <div>
        <label for="memo">Memo: </label>
        <input type="text" name="memo">
    </div>
    <br>
    <input type="submit" name="deposit" value="Deposit">
  
</form>

<?php
if (isset($_POST["deposit"])) {
    $account_id = $_POST["account"];
    $deposit_amount = $_POST["deposit_amount"];

    $db = getDB();
    $stmt = $db->prepare("UPDATE Accounts SET balance = balance + :deposit_amount WHERE id = :account_id");
    $stmt->execute([":deposit_amount" => $deposit_amount, ":account_id" => $account_id]);

    $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, expected_total, memo) VALUES (:account_id, :account_id, :balance_change, 'deposit', :expected_total, :memo)");
    $stmt->execute([":account_id" => $account_id,":balance_change" => $deposit_amount,":expected_total" => $deposit_amount,":memo" => $_POST["memo"]]);

    echo "<p>Deposit successful!</p>";
}
?>

<h1>Withdraw</h1>

<form method="POST">
    <label for="account">Select Account:</label>
    <select name="account" id="account">
        <?php
        foreach ($accounts as $account) {
            echo "<option value='" . $account["id"] . "'>" . $account["account_number"] . " - $" . number_format($account["balance"], 2) . "</option>";
        }
        ?>
    </select>
    <br><br>
    <div>
        <label for="withdrawal_amount">Enter Withdrawal Amount:</label>
        <input type="number" name="withdrawal_amount" id="withdrawal_amount" min="1" required>
    </div>
    <br>
    <div>
        <label for="memo">Memo: </label>
        <input type="text" name="memo">
    </div>
    <br>
    <input type="submit" name="withdraw" value="Withdraw">
    
    </div>
</form>

<?php
if (isset($_POST["withdraw"])) {
    $account_id = $_POST["account"];
    $withdrawal_amount = $_POST["withdrawal_amount"];

    $db = getDB();
    $stmt = $db->prepare("SELECT balance FROM Accounts WHERE id = :account_id");
    $stmt->execute([":account_id" => $account_id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($account["balance"] >= $withdrawal_amount) {
        // updating account balance
        $stmt = $db->prepare("UPDATE Accounts SET balance = balance - :withdrawal_amount WHERE id = :account_id");
        $stmt->execute([":withdrawal_amount" => $withdrawal_amount, ":account_id" => $account_id]);

        // transaction record
        $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, expected_total, memo) VALUES (:account_id, :account_id, :balance_change, 'withdrawal', :expected_total, :memo)");
        $stmt->execute([":account_id" => $account_id,":balance_change" => -$withdrawal_amount, ":expected_total" => $account["balance"] - $withdrawal_amount, ":memo" => $_POST["memo"]]);

        echo "<p>Withdrawal successful!</p>";
    } else {
        echo "<p>Error: Insufficient funds for withdrawal.</p>";
    }
}
?>