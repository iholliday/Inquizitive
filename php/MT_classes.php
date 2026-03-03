<?php
class item
{
    private $UUID;
    private $sprite;
    private $name;
    private $description;
    private $rarity;
    private $price;
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
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function setSprite($sprite)
    {
        $this->sprite = $sprite;
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
    public function setPrice($price)
    {
        $this->price = $price;      
    }
    public function setEffectType($eT)
    {
        $this->effectType = $eT;
        
    }
    public function setEffectValue($eV)
    {
        $this->effectValue = $eV;        
    }

    public function getUUID()
    {
        return $this->UUID;
    }
    public function getQuantity()
    {
        return $this->quantity;
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
    public function getPrice()
    {
        return $this->price;      
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
class shop
{
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
?>