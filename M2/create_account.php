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
</style>

<?php
require(__DIR__ . "/partials/nav.php");
?>

<style>
   
    h1, a {
        font-size: larger;
        color: black
    }
    
    input[type="number"] {
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
        border: white;
        border-radius: 10px;
        cursor: pointer; 
        transition: background-color 0.3s ease;
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

<?php
function account_number($length) {
    $character = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $result = '';
    $maxIndex = strlen($character)-1;
    for ($i = 0; $i < $length; $i++) {
        $result .= $character[mt_rand(0, $maxIndex)]; 
    }
    return $result;
}
$number = account_number(12);
?>

<h1>  Create Accounts </h1>

<form onsubmit="return validate(this)" method="POST">
        <label for="text">Account Number: </label>
        
        <input type="text" id="account_number" name="account_number" value="<?php echo $number; ?>" readonly />
        <br> <br>
    </div>
    <div>
        <label for="deposit">Minimum Deposit of $5</label>
        <input type="text" name="deposit" required />
        <br><br>
    </div>
    <div>
        <input type="submit" value="Register" />
    </div>
    </div>
</form>

<?php
//TODO 2: add PHP Code

if (isset($_POST["deposit"])) {
    $deposit = se($_POST, "deposit", "", false);
    $number = account_number(12);
    $confirm = se(
        $_POST,
        "confirm",
        "",
        false
    );
    //TODO 3
    $hasError = false;
    if (empty($deposit)) {
        echo "Please enter a deposit";
        $hasError = true;
    }

    if ($deposit < 5.00) {
        echo "Please enter a deposit of more than $5.00";
        $hasError = true;
    }

    if (!$hasError) {
        echo "Welcome, account " . $number;
        //TODO 4
        $user_id = $_SESSION["user"]["id"];

        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Accounts (account_number, balance, account_type, user_id) VALUES(:account_number, :balance, :account_type, :user_id)");

        try {
            $stmt->execute(["account_number" => $number, ":balance" => $deposit, ":account_type" => "checking", ":user_id" => $user_id]);
            $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, expected_total) VALUES (:account_src, :account_dest, :balance_change, :transaction_type, :expected_total)");
            $balance_change = $deposit; 
            $expected_total = $deposit; 
            $transaction_type = "deposit"; 
            $stmt->execute([":account_src" => null, ":account_dest" => $account_number,":balance_change" => $balance_change,":transaction_type" => $transaction_type,":expected_total" => $expected_total]);
            $stmt->execute([":account_src" => $account_number,":account_dest" => null, ":balance_change" => $balance_change * -1, ":transaction_type" => $transaction_type,":expected_total" => $expected_total ]);
        
            echo "<br>You successfully created a checking account!";
            
        } 
       
        catch (Exception $e) {
            echo "There was a problem creating your checking account. Please try again.";
            "<pre>" . var_export($e, true) . "</pre>";
        }
    }
}
?>