<?php
namespace App\Model;



use Nette\Utils\Callback;

class ZomatoModelPraha extends ZomatoModel
{
    const ID_GATE="16506013";
    const ID_PRESTO="16520713";
    const ID_FRIENDLY="16506911";
    const ID_MLYN="16506380";
    const ID_INLOCO="18257513";
    const ID_PETERBURGER="16506740";
    const ID_DANUBIO="16507444";
    const ID_SUKOTAI="16521528";
    const ID_OLIVE="16511122";
    const ID_AMBOSELI="16506469";
    const ALLOWED_PRAHA = [
        'getGateMenu',
        'getPrestoMenu',
        'getFriendlyMenu',
        'getMlynMenu',
        'getInLocoMenu',
        'getPeterBurgerMenu',
        'getDanubioMenu',
        'getOliveMenu',
        'getSukotaiMenu',
        'getAmboseliMenu',


    ];

    /**
     * @return string
     */
    public function getGateMenu()
    {
        $data = $this->getJsonData(self::ID_GATE);
        $menu = "*GATE*" . PHP_EOL;
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

    public function getPrestoMenu()
    {
        $data = $this->getJsonData(self::ID_PRESTO);
        $menu = "*PRESTO*" . PHP_EOL;
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

    public function getFriendlyMenu()
    {
        $data = $this->getJsonData(self::ID_FRIENDLY);
        $menu = "*BE FRIENDLY*" . PHP_EOL;
        if (empty($data)) {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        } else {
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

    public function getMlynMenu()
    {
        $data = $this->getJsonData(self::ID_MLYN);
        $menu = "*MLYN*" . PHP_EOL;
        if (empty($data)) {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        } else {
            foreach ($data as $dat) {
            if(empty($dat->dish->price))
            {
                $menu .= $dat->dish->name . PHP_EOL;
            }
            else {
                $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
                }
            }
            return $menu;
        }
    }

    public function getInlocoMenu()
    {
        $data = $this->getJsonData(self::ID_INLOCO);
        $menu = "*INLOCO*" . PHP_EOL;
        if (empty($data)) {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        } else {
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

    public function getPeterBurgerMenu()
    {
        $data = $this->getJsonData(self::ID_PETERBURGER);
        $menu = "*PETERS BURGER PUB*" . PHP_EOL;
        if (empty($data)) {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        } else {
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

    public function getDanubioMenu()
    {
        $data = $this->getJsonData(self::ID_DANUBIO);
        $menu = "*DANUBIO*" . PHP_EOL;
        if (empty($data)) {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        } else {
            foreach ($data as $dat) {
                $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
            }
            return $menu;
        }
    }

    public function getSukotaiMenu()
    {
        $data = $this->getJsonData(self::ID_SUKOTAI);
        $menu = "*SUKOTAI* - proste nudle" . PHP_EOL;
        if (empty($data)) {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        } else {
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

    public function getOliveMenu()
    {
        $data = $this->getJsonData(self::ID_OLIVE);
        $menu = "*OLIVE*" . PHP_EOL;
        if (empty($data)) {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        } else {
            foreach ($data as $dat) {
                $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
            }
            return $menu;
        }
    }

    public function getAmboseliMenu()
    {
        $data = $this->getJsonData(self::ID_AMBOSELI);
        $menu = "*AMBOSELI*" . PHP_EOL;
        if (empty($data)) {
            return $menu .= self::MESSAGE_EMPTY_MENU;
        } else {
            foreach ($data as $dat) {
                $menu .= $dat->dish->name . ' (_' . $dat->dish->price . '_)' . PHP_EOL;
            }
            return $menu;
        }
    }

    public function getAllMenus()
    {
        $menu = null;
        foreach (self::ALLOWED_PRAHA as $restaurantMethod)
            $menu .=Callback::invoke([$this, $restaurantMethod]).PHP_EOL;

        return $menu;
    }
}