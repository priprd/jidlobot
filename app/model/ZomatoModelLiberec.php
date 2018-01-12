<?php
namespace App\Model;


use Nette\Utils\Callback;
use Tracy\Debugger;

class ZomatoModelLiberec extends ZomatoModel
{
    const ID_CHICAGO="16513820";
    const ID_ZTRATY="16513434";
    const ID_MILENIUM = "16513808";
    const ID_STEAK = "16512952";
    const ID_OMAM="18442441";
    const ID_COFFE26="16513136";
    const ALLOWED_LIBEREC = [
        'getChicagoMenu',
        'getZtratyMenu',
        'getSteakMenu',
        'getOmamMenu',
        'getCoffe26Menu',
     ];


    /**
     * @return string
     */
    public function getChicagoMenu()
    {
        $data = $this->getJsonData(self::ID_CHICAGO);
        $menu = "*CHICAGO*" . PHP_EOL;
        if(empty($data))
        {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        }
        else {

            foreach ($data as $dat) {
                if (empty($dat->dish->price)) {
                    $menu .= $dat->dish->name . PHP_EOL;
                } else {
                    $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
                }
            }
            return $menu;
        }
    }
    public function getZtratyMenu()
    {
        $data = $this->getJsonData(self::ID_ZTRATY);
        $menu = "*ZTRATY*" . PHP_EOL;
        if(empty($data))
        {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        }
        else {

            foreach ($data as $dat) {
                if (empty($dat->dish->price)) {
                    $menu .= $dat->dish->name . PHP_EOL;
                } else {
                    $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
                }
            }
            return $menu;
        }
    }
    public function getMileniumMenu()
    {
        $data = $this->getJsonData(self::ID_MILENIUM);
        $menu = "*MILENIUM*" . PHP_EOL;
        if(empty($data))
        {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        }
        else {

            foreach ($data as $dat) {
                if (empty($dat->dish->price)) {
                    $menu .= $dat->dish->name . PHP_EOL;
                } else {
                    $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
                }
            }
            return $menu;
        }
    }

    public function getSteakMenu()
    {
        $data = $this->getJsonData(self::ID_STEAK);
        $menu = "*STEAK HOUSE LIBEREC*" . PHP_EOL;
        if(empty($data))
        {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        }
        else {
            $menu = "*STEAK HOUSE LIBEREC*" . PHP_EOL;
                foreach ($data as $dat) {
                    if (empty($dat->dish->price)) {
                        $menu .= $dat->dish->name . PHP_EOL;
                    } else {
                        $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
                    }
                }
            return $menu;
        }
    }
    public function getOmamMenu()
    {
        $data = $this->getJsonData(self::ID_OMAM);
        $menu = "*OMAM*" . PHP_EOL;
        if(empty($data))
        {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        }
        else {
            foreach ($data as $dat) {
                if (empty($dat->dish->price)) {
                    $menu .= $dat->dish->name . PHP_EOL;
                } else {
                    $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
                }
            }
            return $menu;
        }
    }
    public function getCoffe26Menu()
    {
        $data = $this->getJsonData(self::ID_COFFE26);
        $menu = "*Coffe26*" . PHP_EOL;
        if(empty($data))
        {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        }
        else {
            foreach ($data as $dat) {
                if (empty($dat->dish->price)) {
                    $menu .= $dat->dish->name . PHP_EOL;
                } else {
                    $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
                }
            }
            return $menu;
        }
    }
    public function getAllMenus()
    {
        $menu = null;
        foreach (self::ALLOWED_LIBEREC as $restaurantMethod)
            $menu .=Callback::invoke([$this, $restaurantMethod]).PHP_EOL;

        return $menu;
    }
}