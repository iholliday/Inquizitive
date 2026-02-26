<?php
class item
{
    private $UUID;
    private $sprite;
    private $name;
    private $description;
    private $rarity;
    private $effectType;
    private $effectValue;
    private $quantity;
    public function __construct()
    {
    }
        public function setItemUUID($UUID)
    {
        $this->UUID = $UUID;
    }
    public function setSprite($sprite)
    {
        $this->sprite = $sprite;
    }
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setDescription($desc)
    {
        $this->description =$desc;       
    }
    public function setRarity($rarity)
    {
        $this->rarity = $rarity;    
    }
    public function setEffectType($eT)
    {
        $this->effectType = $eT;
        
    }
    public function setEffectValue($eV)
    {
        $this->effectValue = $eV;        
    }
    public function getQuantity()
    {
        return $this->quantity;
    }
    public function getUUID()
    {
        return $this->UUID;
    }
    public function getSprite()
    {
        return $this->sprite;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getDescription()
    {
        return $this->description;       
    }
    public function getRarity()
    {
        return $this->rarity;    
    }
    public function getEffectType()
    {
        return $this->effectType;
        
    }
    public function getEffectValue()
    {
        return $this->effectValue;        
    }
}
class inventory
{
    private $currency;
    private $items; //unsorted total list
    private $commonItems;
    private $epicItems;
    private $legendaryItems;
    public function __construct()
    {
        $this->items = Array();
        $this->commonItems = Array();
        $this->epicItems = Array();
        $this->legendaryItems = Array();
    }

    public function addItem($item)
    {
        array_push($this->items,$item);
    }
    public function addCommonItem($item)
    {
        array_push($this->commonItems,$item);
    }
    public function addEpicItem($item)
    {
        array_push($this->epicItems,$item);
    }
    public function addLegendaryItem($item)
    {
        array_push($this->legendaryItems,$item);
    }

    public function sortRarities()
    {
        $this->commonItems =[];
        $this->epicItems =[];
        $this->legendaryItems =[]; 
        
        for($i=0;$i<sizeof($this->items);$i++)
        {
            if($this->items[$i]->getRarity() == "Common")
            {
                $this->addCommonItem($this->items[$i]);
            }else if($this->items[$i]->getRarity() == "Epic")
            {
                $this->addEpicItem($this->items[$i]);

            }else if($this->items[$i]->getRarity() == "Legendary")
            {
                $this->addLegendaryItem($this->items[$i]);

            }
        }
    }

    public function getItems()
    {
        return $this->items;
    }
    public function getCommonItems()
    {
        return $this->commonItems;
    }
    public function getEpicItems()
    {
        return $this->epicItems;
    }
    public function getLegendaryItems()
    {
        return $this->legendaryItems;
    }

}

$db= new inquizitiveDB ();
$shop = new inventory();
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
                                    <div class="shop-item-price" item-id="' . $shop->getLegendaryItems()[$i]->getUUID() . '">' . $shop->getLegendaryItems()[$i]->getQuantity() . '</div>
                                </div>';
                            }
                        }else
                        {
                            echo'<div class="shop-no-item">No items at this rarity for sale</div>';
                        }
                        ?>
                    </div>
                </div>