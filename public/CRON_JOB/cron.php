<?php 
$dbhost = "127.0.0.1";
$dbuser = "cypcrsh_panel";
$dbpass = "XUzFalC20nVL";
$dbname = "cypcrsh_panel";
$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



//select all transaction where status is 2 with bind
$stmt = $conn->prepare("SELECT * FROM `transactions` WHERE `transaction_status` = :bind_status");
$stmt->bindValue(':bind_status', 2);
$stmt->execute();
$result = $stmt->fetchAll();

//loop through the result and update the status to 3
foreach($result as $row){
    //get row created_at
    $created_at = $row['created_at'];
    //get current time
    $current_time = date("Y-m-d H:i:s");
    //if difference is greater than 24 hour then update the status to 1
    if(strtotime($current_time) - strtotime($created_at) >= 86400){
        //update the status to 1
        $stmt = $conn->prepare("UPDATE `transactions` SET `transaction_status` = :bind_status WHERE `id` = :bind_id");
        $stmt->bindValue(':bind_status', 1);
        $stmt->bindValue(':bind_id', $row['id']);
        $stmt->execute();
    }
}

//get all transaction where status is 0
$stmt = $conn->prepare("SELECT * FROM `transactions` WHERE `transaction_status` = :bind_status");
$stmt->bindValue(':bind_status', 0);
$stmt->execute();
$result = $stmt->fetchAll();

//foreach result

foreach($result as $row){

    $now=date("Y-m-d H:i:s");

    $expireDate=$row['check_out_date'];

    $expireDate=strtotime($expireDate);

    if($expireDate <= $now){
       //get roomid
        $room_id=$row['room_id'];
        //select rooms where id is room_id
        $stmt = $conn->prepare("SELECT * FROM `rooms` WHERE `id` = :bind_id");
        $stmt->bindValue(':bind_id', $room_id);
        $stmt->execute();
        $room = $stmt->fetch();

        //update  the status to 0 and set transaction_id to null
        $stmt = $conn->prepare("UPDATE `rooms` SET `room_status` = :bind_status, `transaction_id` = :bind_transaction_id WHERE `id` = :bind_id");
        $stmt->bindValue(':bind_status', 0);
        $stmt->bindValue(':bind_transaction_id', null);
        $stmt->bindValue(':bind_id', $room_id);
        $stmt->execute();
    }
    

}

echo "Cron job started at ".date("Y-m-d H:i:s");
?>
