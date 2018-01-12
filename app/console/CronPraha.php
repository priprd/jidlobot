<?php
/**
 * Created by PhpStorm.
 * User: priprd
 * Date: 13.11.2017
 * Time: 20:24
 */

namespace App\Console;


use App\Model\ZomatoModelPraha;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronPraha extends Command
{
    /**
     * @var ZomatoModelPraha
     * @inject
     */
    public $zomatoModelPraha;
    const URI_WEBHOOK = 'https://hooks.slack.com/services/blabla/blablababa';

    protected function configure()
    {
        $this->setName('console:Praha');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = "***************************".PHP_EOL.
            "*****     ".date("d.m.y")."     *****".PHP_EOL.
            "***************************".PHP_EOL.PHP_EOL;
        $data.= $this->zomatoModelPraha->getAllMenus();
        //$data = json_decode($data);
        $clint = new Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
        $request = $clint->post(self::URI_WEBHOOK,['json'=>['text'=>$data]]);
    }

}
