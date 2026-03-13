<?php
require_once("./php/MT_classes.php");
header('Content-Type: application/json; charset=utf-8');

$db= new inquizitiveDB (); //php reference count should drop to 0 on new assignment causing destructor to close previous conn
$userUUID= htmlspecialchars($_SESSION['userUUID']);
$boughtItemUUID =   htmlspecialchars($_POST['item']);
$boughtItemQuantity =   htmlspecialchars($_POST['quantity']);

$currencyItemUUIDResult = $db->Query("CALL GetCurrencyItemUUID()");
$currencyItemUUID;
$rowCount = mysqli_num_rows($currencyItemUUIDResult);
if ($rowCount > 0) {
        $row = mysqli_fetch_assoc($currencyItemUUIDResult);
        $currencyItemUUID = htmlspecialchars($row['itemUUID']);
    }
$db= new inquizitiveDB ();
if ($result = $db->Query("CALL GetInquizitiveCurrencyByUserUUID(?)", [$userUUID])) {

    $rowCount = mysqli_num_rows($result);
    if ($rowCount > 0) {
        $row = mysqli_fetch_assoc($result);
        $userCurrency = htmlspecialchars($row['quantity']);
        $db= new inquizitiveDB ();
        $item = new item();
        if($itemResult = $db->Query("CALL GetUserAllItemDataByUserUUIDAndItemUUID(?,?)", [$userUUID,$boughtItemUUID]))
        {
            $numRows = mysqli_num_rows($itemResult);
            if ($numRows > 0) 
            {
                $row = mysqli_fetch_assoc($itemResult);
                $moneyLeft =  (int)$userCurrency - ((int)$row['price'] * (int)$boughtItemQuantity);
                if($row['quantity'] == NULL) //You should never really see this
                {
                    $returnResponse = ["status" => "error", "message" => "NULL quantity in inventory"];
                    $json = json_encode($returnResponse);
                    echo $json;
                    exit();
                }else if($row['price'] == NULL)//item doesn't have a price, therefore item isn't for sale in shop
                {
                    $returnResponse = ["status" => "error", "message" => "Item is not for sale"];
                    $json = json_encode($returnResponse);
                    echo $json;
                    exit();
                }else if($moneyLeft < 0)//not enough money to buy item
                {
                    $returnResponse = ["status" => "money404", "message" => "You lack the funds to buy this"];
                    $json = json_encode($returnResponse);
                    echo $json;
                    exit();
                }else
                {
                    //item is in shop, and inventory and we have money so we just perform updates
                    $db= new inquizitiveDB ();
                    $newQuantity = (int)$row['quantity'] + (int)$boughtItemQuantity;
                    $a = $db->Query("CALL SetUserInventoryItemQuantity(?,?,?);", [$userUUID,$boughtItemUUID,$newQuantity]);
                    $db= new inquizitiveDB ();
                    $moneyLeft =  (int)$userCurrency - ((int)$row['price'] * (int)$boughtItemQuantity);
                    $b = $db->Query("CALL SetUserInventoryItemQuantity(?,?,?);", [$userUUID,$currencyItemUUID,$moneyLeft]);
                    $returnResponse = ["status" => "success", "message" => "Item bought successfully", "money" => $moneyLeft];
                    $json = json_encode($returnResponse);
                    echo $json;
                    exit();
                }
            }else
            { //no trace of the item in the inventory
            $db = new inquizitiveDB();
            if($itemResult = $db->Query("CALL GetItemByUUID(?)", [$boughtItemUUID]))
            {
                $numRows = mysqli_num_rows($itemResult);
                if ($numRows > 0) 
                {
                    $row = mysqli_fetch_assoc($itemResult);
                    $moneyLeft =  (int)$userCurrency - ((int)$row['price'] * (int)$boughtItemQuantity);
                    if($moneyLeft < 0)//not enough money to buy item
                    {
                        $returnResponse = ["status" => "money404", "message" => "You lack the funds to buy this"];
                        $json = json_encode($returnResponse);
                        echo $json;
                        exit();
                    }else{
                        //since we have money to buy it we can add it directly with the quantity purchased
                        $db= new inquizitiveDB ();
                        $addResult = $db->Query("CALL AddItemToGivenAccountInventory(?,?,?);", [$userUUID,$boughtItemUUID,$boughtItemQuantity]);
                        $db= new inquizitiveDB ();
                    
                        $updateResult = $db->Query("CALL SetUserInventoryItemQuantity(?,?,?);", [$userUUID,$currencyItemUUID,$moneyLeft]);
                        $returnResponse = ["status" => "success", "message" => "Item bought successfully", "money" => $moneyLeft];
                        $json = json_encode($returnResponse);
                        echo $json;
                        exit();
                    }
                }
            }

            $awoo = ["status"=>"success","message"=>$row];
            $json = json_encode($awoo);
            echo $json;
            exit();
            }
        
        }
        
        //add to inventory if they have money

    }else
    {
        $db= new inquizitiveDB ();
        $userCurrency =0;
        if($itemUUIDResult = $db->Query("CALL GetCurrencyItemUUID();"))
        {
            $returnedRowsNum = mysqli_num_rows($itemUUIDResult);
            if ($returnedRowsNum > 0) {
                $row = mysqli_fetch_assoc($itemUUIDResult);
                $itemUUID = htmlspecialchars($row['itemUUID']);
                $db= new inquizitiveDB ();
                $result = $db->Query("CALL AddItemToGivenAccountInventory(?,?,?);", [$userUUID,$itemUUID,$userCurrency]);
                $returnResponse = ["status" => "money404", "message" => "You lack the funds to buy this"];
                $json = json_encode($returnResponse);
                echo $json;
                exit(); //If they didn't have funds before clearly they do not afford this item.
            }
        }

    }
}

?>
