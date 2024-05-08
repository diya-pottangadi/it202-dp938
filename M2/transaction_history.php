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
<h1>Transaction History</h1>

<?php
if (!isset($_GET["account_number"])) {
    echo "<p>No account selected.</p>";
    exit;
}

$account_number = $_GET["account_number"];

$db = getDB();
$stmt = $db->prepare("SELECT account_number, account_type, balance, created FROM Accounts WHERE account_number = :account_number");
$stmt->execute([":account_number" => $account_number]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT t.transaction_type, t.balance_change, t.transaction_date, t.expected_total, t.memo, a.account_number as src_account, b.account_number as dest_account, a.id as src_account_id, b.id as dest_account_id
                     FROM Transactions t 
                     LEFT JOIN Accounts a ON t.account_src = a.id 
                     LEFT JOIN Accounts b ON t.account_dest = b.id 
                     WHERE t.account_src = :account_id OR t.account_dest = :account_id 
                     ORDER BY t.transaction_date DESC 
                     LIMIT 10");
$stmt->execute([":account_id" => $account_number]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Account Details</h2>";
echo "<p>Account Number: " . $account["account_number"] . "</p>";
echo "<p>Account Type: " . $account["account_type"] . "</p>";
echo "<p>Balance: $" . ($account["balance"] !== null ? number_format($account["balance"], 2) : "N/A") . "</p>";
echo "<p>Opened/Created Date: " . $account["created"] . "</p>";

echo "<h2>Transaction History</h2>";
if ($transactions) {
    echo "<table>";
    echo "<tr><th>Transaction Type</th><th>Change in Balance</th><th>Transaction Date</th><th>Expected Total</th><th>Memo</th></tr>";
    foreach ($transactions as $transaction) {
        echo "<tr>";
        echo "<td>" . $transaction["transaction_type"] . "</td>";
        echo "<td>$" . ($transaction["balance_change"] !== null ? number_format($transaction["balance_change"], 2) : "N/A") . "</td>";
        echo "<td>" . ($transaction["transaction_date"] !== null ? $transaction["transaction_date"] : "N/A") . "</td>";
        echo "<td>$" . ($transaction["expected_total"] !== null ? number_format($transaction["expected_total"], 2) : "N/A") . "</td>";
        echo "<td>" . $transaction["memo"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No transaction history found.</p>";
}


if (isset($_POST["deposit"]) || isset($_POST["withdraw"])) {
    $db = getDB();
    $db->beginTransaction();

    try {
        $account_src_id = $_POST["account"];
        $account_dest_id = $_POST["account_dest"];
        $balance_change = isset($_POST["deposit"]) ? $_POST["deposit_amount"] : -$_POST["withdrawal_amount"];
        $memo = $_POST["memo"];
        $stmt = $db->prepare("SELECT id, balance, account_type FROM Accounts WHERE id IN (:account_src_id, :account_dest_id)");
        $stmt->execute([":account_src_id" => $account_src_id, ":account_dest_id" => $account_dest_id]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($accounts) != 2) {
            throw new Exception("Invalid account selection");
        }
        $expected_totals = [];
        foreach ($accounts as $account) {
            $expected_totals[$account["id"]] = $account["balance"] + $balance_change;
        }
        $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, expected_total, memo) VALUES (:account_src_id, :account_dest_id, :balance_change, :transaction_type, :expected_total, :memo)");
        $stmt->execute([":account_src_id" => $account_src_id,":account_dest_id" => $account_dest_id,":balance_change" => $balance_change,":transaction_type" => isset($_POST["deposit"]) ? 'deposit' : 'withdrawal',":expected_total" => $expected_totals[$account_src_id],":memo" => $memo]);

        foreach ($accounts as $account) {
            $stmt = $db->prepare("UPDATE Accounts SET balance = :balance WHERE id = :account_id");
            $stmt->execute([":balance" => $expected_totals[$account["id"]], ":account_id" => $account["id"]]);
        }

        $db->commit();
        echo "<p>Transaction successful!</p>";
    } catch (Exception $e) {
        $db->rollBack();
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

?>

</div>
