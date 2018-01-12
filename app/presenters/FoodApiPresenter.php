<?php

namespace App\Presenters;

use App\Model\FoodMenuParsers;
use App\Model\SlackModel;
use App\Model\ZomatoModelLiberec;
use App\Model\ZomatoModelPraha;
use Nette;
use Nette\Caching\Cache;
use Nette\Utils\Callback;
use Nette\Utils\Strings;
use Tracy\Debugger;


/**
 * FoodApi presenter
 */
class FoodApiPresenter extends BasePresenter
{

	const SLACK_INCOMING_TOKEN = 'TOKEN';
    const HELP_MESSAGE = 'Použití: */jidlo {Liberec, Praha, vse}*';
	const ALLOWED_LIBEREC = [
		'sendChicagoMenu',
        'sendZitoMenu',
        'sendZtratyMenu',
		'sendSklipekMenu',
		'sendSteakMenu',


	];

    const ALLOWED_PRAHA = [
        'sendGateMenu',
        'sendPrestoMenu',
        'sendFriendlyMenu',
        'sendMlynMenu',
        'sendInLocoMenu',
        'sendPeterBurgerMenu',
        'sendDanubio',
        'sendOlive',
        'sendSukotai',


    ];

	const CACHE_EXPIRATION = '1 hour';

	/**
	 * @var FoodMenuParsers @inject
	 */
	public $foodMenuParser;

	/**
	 * @var SlackModel @inject
	 */
	public $slackModel;

	/**
	 * @var Cache
	 */
	public $cache;

    /**
     * @var ZomatoModelLiberec @inject
     */
    public $zomatoModelLiberec;

    /**
     * @var ZomatoModelPraha @inject
     */
    public $zomatoModelPraha;

	private $requestFinished = FALSE;

	protected function startup()
	{
		Debugger::timer('foodApi');
		parent::startup();

		if(!isset($this->request->post['token']) || $this->request->post['token']!==self::SLACK_INCOMING_TOKEN) {
			$this->getHttpResponse()->setCode(Nette\Http\Response::S403_FORBIDDEN);
			$this->terminate();
		}

		$this->cache = new Cache($this->context->getService('cache.storage'), 'foodApi');
		$this->detectAction();
	}

	private function detectAction()
	{
		$text = $this->request->post['text'];
		$username = $this->request->post['user_name'];

		if($username=='david.priplata') {
			if(Strings::contains($text, 'invalidate')) {
				$this->cache->clean([
					Cache::ALL => TRUE,
				]);
			}
		}

		if(Strings::startsWith($text, 'help')) {
			$this->sendMessage(self::HELP_MESSAGE);
		} elseif (Strings::startsWith($text, 'chicago')) {
			$this->sendChicagoMenu();
		} elseif (Strings::startsWith($text, 'sklipek')) {
			$this->sendSklipekMenu();
		} elseif (Strings::startsWith($text, 'zito')) {
			$this->sendZitoMenu();
		} elseif (Strings::startsWith($text, 'husa')) {
            $this->sendHusaMenu();
        }elseif (Strings::startsWith($text, 'ztraty')) {
            $this->sendZtratyMenu();
        }elseif (Strings::startsWith($text, 'milenium')) {
            $this->sendMilenium();
        }elseif (Strings::startsWith($text, 'steak')) {
            $this->sendSteakMenu();
        } elseif (Strings::startsWith($text, 'Liberec') || Strings::startsWith($text, 'liberec')) {
			$this->sendAllLiberecMenus();
        } elseif (Strings::startsWith($text, 'Praha') || Strings::startsWith($text, 'praha')) {
            $this->sendAllPrahaMenus();
        } elseif (Strings::startsWith($text, 'all') || Strings::startsWith($text, 'vse')) {
            $this->sendAllMenus();
		} else {
			$this->sendMessage('Neznámá akce.'.self::HELP_MESSAGE);
		}
	}

	public function sendChicagoMenu()
	{
	    $finalString = $this->zomatoModelLiberec->getChicagoMenu();
        $this->send($this->request->post['response_url'], $finalString);
	}

	public function sendZitoMenu()
	{
		try {
			$that = $this;
			$menus = $this->cache->load(__FUNCTION__, function (&$dependencies) use ($that) {
				$dependencies = [
					Cache::EXPIRATION => self::CACHE_EXPIRATION,
				];
				return $that->foodMenuParser->getZitoMenu();
			});

			if(empty($menus)) {
				//@todo pracovat s temito exceptiony
				throw new \Exception('Nemůžu najít aktuální jidelníček pro Žito. Asi mě bude muset někdo opravit :(');
			}
		} catch (\Exception $e) {
			$this->sendMessage('Ajaj. Buď na webu není jídelníček nebo jsem slepý a budu potřebovat opravit.');
			$this->terminate();
		}

		$finalString = "*ŽITO*" . PHP_EOL;
		foreach ($menus as $menu) {
			$finalString .= $menu['label'] . PHP_EOL;

		}

		$this->send($this->request->post['response_url'], $finalString);
	}

	public function sendSklipekMenu()
	{
		try {
			$that = $this;
			$menus = $this->cache->load(__FUNCTION__, function (&$dependencies) use ($that) {
				$dependencies = [
					Cache::EXPIRATION => self::CACHE_EXPIRATION,
				];
				return $that->foodMenuParser->getSklipekMenu();
			});

			if(empty($menus)) {
				//@todo pracovat s temito exceptiony
				throw new \Exception('Nemůžu najít aktuální jidelníček pro Žito. Asi mě bude muset někdo opravit :(');
			}
		} catch (\Exception $e) {
			$this->sendMessage('Ajaj. Buď na webu není jídelníček nebo jsem slepý a budu potřebovat opravit.');
			$this->terminate();
		}

		$finalString = "*RADNIČNÍ SKLÍPEK*" . PHP_EOL;
		foreach ($menus as $menu) {
			if(empty($menu['price'])) {
				$finalString .= '*' . $menu['label'] . '*' . PHP_EOL;
			} else {
				$finalString .= $menu['label'] . ' (_' . $menu['price'] . '_)' . PHP_EOL;
			}

		}

		$this->send($this->request->post['response_url'], $finalString);
	}

	public function sendHusaMenu()
	{
		try {
			$that = $this;
			$menus = $this->cache->load(__FUNCTION__, function (&$dependencies) use ($that) {
				$this->sendWaitJsonResponse();

				$dependencies = [
					Cache::EXPIRATION => self::CACHE_EXPIRATION,
				];
				return $that->foodMenuParser->getHusaMenu();
			});

			if(empty($menus)) {
				//@todo pracovat s temito exceptiony
				throw new \Exception('Nemůžu najít aktuální jidelníček pro Žito. Asi mě bude muset někdo opravit :(');
			}
		} catch (\Exception $e) {
			$this->sendMessage('Ajaj. Buď na webu není jídelníček nebo jsem slepý a budu potřebovat opravit.');
			$this->terminate();
		}

		$finalString = "*POTREFENÁ HUSA*" . PHP_EOL;
		foreach ($menus as $menu) {
			if(empty($menu['price'])) {
				$finalString .= '*' . $menu['label'] . '*' .PHP_EOL;
			} else {
				$finalString .= $menu['label'] . ' (_' . $menu['price'] . '_)' . PHP_EOL;
			}

		}

		$this->send($this->request->post['response_url'], $finalString);
	}

	public function sendZtratyMenu()
    {
        $finalString = $this->zomatoModelLiberec->getZtratyoMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }
    public function sendMilenium()
    {
        $finalString = $this->zomatoModelLiberec->getMileniumMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }
    public function sendSteakMenu()
    {
        $finalString = $this->zomatoModelLiberec->getSteakMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }


    /*
     * Praha hospody
     *
     *
     */



    public function sendGateMenu()
    {
        $finalString = $this->zomatoModelPraha->getGateMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }

    public function sendPrestoMenu()
    {
        $finalString = $this->zomatoModelPraha->getPrestoMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }

    public function sendFriendlyMenu()
    {
        $finalString = $this->zomatoModelPraha->getFriendlyMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }

    public function sendMlynMenu()
    {
        $finalString = $this->zomatoModelPraha->getMlynMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }

    public function sendInLocoMenu()
    {
        $finalString = $this->zomatoModelPraha->getInlocoMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }

    public function sendPeterBurgerMenu()
    {
        $finalString = $this->zomatoModelPraha->getPeterBurgerMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }

    public function sendDanubioMenu()
    {
        $finalString = $this->zomatoModelPraha->getDanubioMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }

    public function sendSukotaiMenu()
    {
        $finalString = $this->zomatoModelPraha->getSukotaiMenu();
        $this->send($this->request->post['response_url'], $finalString);
    }

    public function sendOliveMenu()
    {
    $finalString = $this->zomatoModelPraha->getOliveMenu();
    $this->send($this->request->post['response_url'], $finalString);
    }
    /**
     *
     */

        private function sendAllLiberecMenus()
	{
        $this->sendWaitJsonResponse();
	    $finalString = $this->zomatoModelLiberec->getAllMenus();
        $this->send($this->request->post['response_url'], $finalString);
		$this->sendZitoMenu();
		$this->sendSklipekMenu();

		$this->terminate();
	}

    private function sendAllPrahaMenus()
    {
        $this->sendWaitJsonResponse();
		$finalString = $this->zomatoModelPraha->getAllMenus();
 		$this->send($this->request->post['response_url'], $finalString);
        $this->terminate();
    }
    private function sendAllMenus()
    {
        $this->sendWaitJsonResponse("*To jako vážně?*");
        $this->sendMessage('tak jo tedy no');
        foreach (self::ALLOWED_LIBEREC as $restaurantMethod) {
            $this->sendWaitJsonResponse();
            Callback::invoke([$this, $restaurantMethod]);
        }
        $this->sendAllPrahaMenus();
        $this->terminate();
    }
	private function sendMessage($message)
	{
		$this->send($this->request->post['response_url'], $message);
	}

	/**
	 * @param $url
	 * @param $text
	 * @return mixed
	 */
	public function send($url, $text)
	{
		if(Debugger::timer('foodApi')>=3 || $this->requestFinished === TRUE) {
			$this->slackModel->postResponse($text, $url);
		} else {
			return $this->jsonResponse($text);
		}
	}

	/**
	 * @param string $text
	 */
	private function jsonResponse($text)
	{
		$data = [
			'text' =>  $text,
			'mrkdwn' => TRUE,
		];

		$this->sendJson($data);
	}

	/**
	 * @return void
	 */
	private function sendWaitJsonResponse($text=null)
	{
	    if(empty($text)){$text = 'Chvilku vydrž';}
		if($this->requestFinished === FALSE) {
			$this->requestFinished = TRUE;

			$this->getHttpResponse()->setHeader('Content-Type', 'application/json');
			echo Nette\Utils\Json::encode([
				'text' =>  $text,
				'mrkdwn' => TRUE,
			]);
			fastcgi_finish_request();
		}
	}

}
