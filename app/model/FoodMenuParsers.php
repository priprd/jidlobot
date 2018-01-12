<?php

namespace App\Model;

use GuzzleHttp\Client;
use Nette;
use Nette\Caching\Cache;
use Symfony\Component\DomCrawler\Crawler;

/**
 * FoodMenuParsers
 */
class FoodMenuParsers
{
	use Nette\SmartObject;



	/**
	 * @var Client
	 */
	private $client;



	public function __construct()
	{
		$this->client = new Client([
			'timeout'  => 20,
		]);

	}

	/**
	 * @return array
	 */
	public function getZitoMenu()
	{
		$sourceUrl = 'http://www.zito.cz/data/clanky/clanky_index.php?action=clanky&id_menu1=58';
		$html = $this->client->get($sourceUrl)->getBody()->getContents();

		$crawler = new Crawler($html);
		$td = $crawler->filter('td#clanek_text em strong p');

		$data = [];
		foreach ($td as $line) {
			$rowCrawler = new Crawler($line);
			$text = trim(html_entity_decode(strip_tags($rowCrawler->text())), " \t\n\r\0\x0B\xC2\xA0");
			if(empty($text) || Nette\Utils\Strings::startsWith($text, 'DENNÍ MENU')) continue;

			$data[] = [
				'label' => $text,
			];
		}

		return $data;
	}

	public function getSklipekMenu()
	{
		$sourceUrl = 'http://www.sklipekliberec.cz/cz/';
		$html = $this->client->get($sourceUrl)->getBody()->getContents();

		$crawler = new Crawler($html);
		$menus = $crawler->filter('div#hp-polednimenu div.cenik')->children();

		$data = [];
		foreach ($menus as $item) {
			$itemCrawler = new Crawler($item);

			$priceCrawler = $itemCrawler->filter('span.td');
			$price = NULL;
			if(count($priceCrawler)>0) {
				$price = (trim($priceCrawler->text(), " \t\n\r\0\x0B\xC2\xA0"));
			}

			$labelWithMess = $itemCrawler->filterXPath('//span[@class="th"]/text()')->extract(['_text']);
			if(empty($labelWithMess)) {
				$label = trim($itemCrawler->filter('span.th')->text(), " \t\n\r\0\x0B\xC2\xA0");
			} else {
				$label = trim($labelWithMess[0], " \t\n\r\0\x0B\xC2\xA0");
			}
			$info = '';


			$data[] = [
				'label' => $label,
				'info' => $info,
				'price' => $price,
			];
		}

		return $data;
	}



    public function sendSklipekMenu()
    {
        $finalString = "*RADNIČNÍ SKLÍPEK*" . PHP_EOL;
        $menus = $this->getSklipekMenu();



        foreach ($menus as $menu) {
            if(empty($menu['price'])) {
                $finalString .= '*' . $menu['label'] . '*' . PHP_EOL;
            } else {
                $finalString .= $menu['label'] . ' (_' . $menu['price'] . '_)' . PHP_EOL;
            }

        }

        return $finalString;
    }

    public function sendZitoMenu()
    {
        $finalString = "*ŽITO*".PHP_EOL;
        $menus = $this->getZitoMenu();
        foreach ($menus as $menu) {
            if(empty($menu['price'])) {
                $finalString .= '*' . $menu['label'] . '*' . PHP_EOL;
            } else {
                $finalString .= $menu['label'] . ' (_' . $menu['price'] . '_)' . PHP_EOL;
            }

        }

        return $finalString;
    }

}
