<?php

require_once("./php/MT_classes.php");
$db= new inquizitiveDB ();
$shop = new shop();
$userUUID= htmlspecialchars($_SESSION['userUUID']); 

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
?>

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
                <script>
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
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                            });
                            $("#shop-page-nav-currency").text(response.money);
                        }else if(response.status == "money404")
                        {
                            Swal.fire({
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