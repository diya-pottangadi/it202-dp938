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
    padding: 10px; 
    border: whitesmoke; 
    border-radius: 5px; 
    width: 500; 
    }

    li, a{
        font-family: 'Times New Roman', Times, serif;
        font-size: 16px;
    }
    
    a:hover{
        font-size:larger;
    }
</style>

<body>
    <div class="dashboard">
        <div class = "box", >
        <h1>Welcome to Your Dashboard</h1>
        <ul>
            <li><a href="create_account.php">Create Account</a></li>
            <li><a href="my_accounts.php">My Accounts</a></li>
            <li><a href="deposit_withdraw.php?">Deposit/Withdraw</a></li>
            <li><a href="#">Transfer</a></li>
            <li><a href="#">Profile</a></li>
        </ul>
    </div>
</div>
</body>