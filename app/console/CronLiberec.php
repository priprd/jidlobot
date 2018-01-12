<?php
/**
 * Created by PhpStorm.
 * User: priprd
 * Date: 13.11.2017
 * Time: 20:24
 */

namespace App\Console;


use App\Model\FoodMenuParsers;
use App\Model\ZomatoModelLiberec;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronLiberec extends Command
{
    /**
     * @var ZomatoModelLiberec
     * @inject
     */
    public $zomatoModelLiberec;
    /**
     * @var FoodMenuParsers
     * @inject
     */
    public $foodMenuParsers;
    /**
    * Change URI WEBHOOK
    */
    const URI_WEBHOOK= 'https://hooks.slack.com/services/bla/blaba';

    protected function configure()
    {
        $this->setName('console:Liberec');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = "***************************".PHP_EOL.
                "*****     ".date("d.m.y")."     *****".PHP_EOL.
                "***************************".PHP_EOL.PHP_EOL;
        $data.= $this->zomatoModelLiberec->getAllMenus();
        $data.= $this->foodMenuParsers->sendSklipekMenu();
        $data.= $this->foodMenuParsers->sendZitoMenu();
        //$data = json_decode($data);
        $clint = new Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
        $request = $clint->post(self::URI_WEBHOOK,['json'=>['text'=>$data]]);
    }

}
