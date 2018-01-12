<?php

namespace App\Model;
use Nette;

class ZomatoModel
{
    use Nette\SmartObject;

    const URL="https://developers.zomato.com/api/v2.1/dailymenu?res_id=";
    const USER_KEY="26ff1071b42df80193b22f5fc8d0f5fe";
       const MESSAGE_EMPTY_MENU = 'Sorry jako dneska nic nemaj';

    /**
     * @param String $res_id id restaurace
     */
    public function getJsonData($res_id)
    {
        $opt =  [
            "http" => [
                "method" => "GET",
                "header" => "user_key:".self::USER_KEY
            ]
        ];
        $context = stream_context_create($opt);
        $file = file_get_contents(self::URL.$res_id,false, $context);
        $json = json_decode($file);
        if(empty($json->daily_menus))
        {
            $menu = null;

        }
        else
        {
            $menu = $json->daily_menus[0]->daily_menu->dishes;
        }

        return $menu;

    }
}