<?php

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

if (!$isAjax && !$isEmbeddedInDashboard) {
  $DASH_INCLUDE = __FILE__;
  require __DIR__ . '/../dashboardNavigation.php';
  exit;
}


require_once("./php/MT_classes.php");
$db= new inquizitiveDB ();
$shop = new shop();
$userUUID= htmlspecialchars($_SESSION['userUUID']); 
$userCurrency = 0;
if ($result = $db->Query("CALL GetAllShopItems();")) {

    $questionCount = mysqli_num_rows($result);
    if ($questionCount > 0) {
        while($row = mysqli_fetch_assoc($result))
        {
            $item = new item();
            $item->setItemUUID(htmlspecialchars($row['itemUUID']));
            $item->setSprite(htmlspecialchars($row['itemSprite']));
            $item->setName(htmlspecialchars($row['itemName']));
            $item->setDescription(htmlspecialchars($row['itemDescription']));
            $item->setPrice(htmlspecialchars($row['price']));
            $item->setRarity(htmlspecialchars($row['rarity']));
            $item->setEffectType(htmlspecialchars($row['itemEffectType']));
            $item->setEffectValue(htmlspecialchars($row['itemEffectValue']));

            $shop->addItem($item);
        }
    }
}
$shop->sortRarities();

$db= new inquizitiveDB (); //php reference count should drop to 0 on new assignment causing destructor to close previous conn
if ($result = $db->Query("CALL GetInquizitiveCurrencyByUserUUID(?);", [$userUUID])) {

    $rowCount = mysqli_num_rows($result);
    if ($rowCount > 0) {
        $row = mysqli_fetch_assoc($result);
        $userCurrency = htmlspecialchars($row['quantity']);
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

            }
        }

    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="./css/shop.css" />
</head>
<body>
    <section id="shop-page">
        <div id="shop-page-content-wrapper" class="shadow">
            <div id="shop-page-nav-wrapper">
                <div class="links">
                    <div id="shop-page-nav-shop" class="active">Shop</div>
                    <div id="shop-page-nav-gacha">Gacha</div>
                    <div id="shop-page-nav-inventory">Inventory</div>
                </div>
                <div id="shop-page-nav-currency-wrapper">
                    <div class="currency-icon">🔎</div>
                    <div id="shop-page-nav-currency"> <?= $userCurrency ?></div>
                </div>
            </div>
            <div id="shop-page-content-display">
                <div class="shop-items shop-common">
                    <div class="shop-items-header">Common</div>
                    <div class="shop-items-header-divider"></div>
                    <div class="shop-items-grid">
                        <?php
                        if(sizeof($shop->getCommonItems()) >0)
                        {
                            for($i=0; $i<sizeof($shop->getCommonItems());$i++)
                            {
                                echo '
                                <div class="shop-item">
                                    <img src="' . $shop->getCommonItems()[$i]->getSprite() . '" /> 
                                    <div class="shop-item-price" item-id="' . $shop->getCommonItems()[$i]->getUUID() . '">' . $shop->getCommonItems()[$i]->getPrice() . '</div>
                                </div>';
                            }
                        }else
                        {
                            echo'<div class="shop-no-item">No items at this rarity for sale</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="shop-items shop-epic">
                    <div class="shop-items-header">Epic</div>
                    <div class="shop-items-header-divider"></div>
                    <div class="shop-items-grid">
                        <?php
                        if(sizeof($shop->getEpicItems()) >0)
                        {
                            for($i=0; $i<sizeof($shop->getEpicItems());$i++)
                            {
                                echo '
                                <div class="shop-item">
                                    <img src="' . $shop->getEpicItems()[$i]->getSprite() . '" /> 
                                    <div class="shop-item-price" item-id="' . $shop->getEpicItems()[$i]->getUUID() . '">' . $shop->getEpicItems()[$i]->getPrice() . '</div>
                                </div>';
                            }
                        }else
                        {
                            echo'<div class="shop-no-item">No items at this rarity for sale</div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="shop-items shop-legendary">
                    <div class="shop-items-header">Legendary</div>
                    <div class="shop-items-header-divider"></div>
                    <div class="shop-items-grid">
                        <?php
                        if(sizeof($shop->getLegendaryItems()) >0)
                        {
                            for($i=0; $i<sizeof($shop->getLegendaryItems());$i++)
                            {
                                echo '
                                <div class="shop-item">
                                    <img src="' . $shop->getLegendaryItems()[$i]->getSprite() . '" /> 
                                    <div class="shop-item-price" item-id="' . $shop->getLegendaryItems()[$i]->getUUID() . '">' . $shop->getLegendaryItems()[$i]->getPrice() . '</div>
                                </div>';
                            }
                        }else
                        {
                            echo'<div class="shop-no-item">No items at this rarity for sale</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        var shopTabPrevious = "#shop-page-nav-shop";
        $("#shop-page-nav-inventory").ready(function(){
            $("#shop-page-nav-inventory").click(function (){
                $(shopTabPrevious).removeClass("active");
                $(this).addClass("active");
                shopTabPrevious = "#shop-page-nav-inventory";
                $.ajax({
                    url: './shop-inventory', 
                    type: 'GET', 
                    success: function(response) {
                        //console.log('Success:', response);
                        $("#shop-page-content-display").html(response);
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error:', error);
                    }
                });
            })
        })

        $("#shop-page-nav-shop").ready(function(){
            $("#shop-page-nav-shop").click(function (){
                $(shopTabPrevious).removeClass("active");
                $(this).addClass("active");
                shopTabPrevious = "#shop-page-nav-shop";
                $.ajax({
                    url: './shop-items', 
                    type: 'GET', 
                    success: function(response) {
                        //console.log('Success:', response);
                        $("#shop-page-content-display").html(response);
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error:', error);
                    }
                });
            })
        })


        
        $("#shop-page-nav-gacha").ready(function(){
            $("#shop-page-nav-gacha").click(function (){
                $(shopTabPrevious).removeClass("active");
                $(this).addClass("active");
                shopTabPrevious = "#shop-page-nav-gacha";
                $.ajax({
                    url: './shop-gacha', 
                    type: 'GET', 
                    success: function(response) {
                        //console.log('Success:', response);
                        $("#shop-page-content-display").html(response);
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error:', error);
                    }
                });
            })
        })

        $(".shop-item-price").ready(function(){
            $(".shop-item-price").click(function (){
                $.ajax({
                    url: './shop-buy-item', 
                    type: 'POST',
                    data: {
                        item: $(this).attr("item-id"),
                        quantity: 1
                    }, 
                    success: function(response) {
                        //console.log('Success:', response);
                        if(response.status == "success")
                        {
                            
                            Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                            });
                            $("#shop-page-nav-currency").text(response.money);
                        }else if(response.status == "money404")
                        {
                            Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                            });
                        }else
                        {
                            Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: response.status,
                            showConfirmButton: false,
                            timer: 1500
                            });
                        }
                    },
                    error: function(xhr, status, error) {
    
                    }
                });
            })
        })
    </script>
</body>
</html>