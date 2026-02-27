<?php
require_once("./php/MT_classes.php");


$db= new inquizitiveDB ();
$shop = new shop();
$userUUID= htmlspecialchars($_SESSION['userUUID']); 
if(!isset($userUUID)){die("user session not set!");};
if ($result = $db->Query("CALL GetAllInventoryItemsByUserUUID (?);",[$userUUID])) {

    $questionCount = mysqli_num_rows($result);
    if ($questionCount > 0) {
        while($row = mysqli_fetch_assoc($result))
        {
            $item = new item();
            $item->setItemUUID(htmlspecialchars($row['itemUUID']));
            $item->setSprite(htmlspecialchars($row['itemSprite']));
            $item->setName(htmlspecialchars($row['itemName']));
            $item->setDescription(htmlspecialchars($row['itemDescription']));
            $item->setRarity(htmlspecialchars($row['rarity']));
            $item->setEffectType(htmlspecialchars($row['itemEffectType']));
            $item->setEffectValue(htmlspecialchars($row['itemEffectValue']));
            $item->setQuantity(htmlspecialchars($row['quantity']));
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
                                    <div class="shop-item-price" item-id="' . $shop->getCommonItems()[$i]->getUUID() . '">' . $shop->getCommonItems()[$i]->getQuantity() . '</div>
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
                                    <div class="shop-item-price" item-id="' . $shop->getEpicItems()[$i]->getUUID() . '">' . $shop->getEpicItems()[$i]->getQuantity() . '</div>
                                </div>';
                            }
                        }else
                        {
                            echo'<div class="shop-no-item">No items at this rarity in inventory</div>';
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
                                    <div class="shop-item-price" item-id="' . $shop->getLegendaryItems()[$i]->getUUID() . '">' . $shop->getLegendaryItems()[$i]->getQuantity() . '</div>
                                </div>';
                            }
                        }else
                        {
                            echo'<div class="shop-no-item">No items at this rarity in inventorye</div>';
                        }
                        ?>
                    </div>
                </div>