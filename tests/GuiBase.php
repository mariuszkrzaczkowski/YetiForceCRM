<?php
/**
 * Base test class.
 *
 * @package   Tests
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace tests;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

abstract class GuiBase extends TestCase
{
	/**
	 * Last logs.
	 *
	 * @var mixed
	 */
	public $logs;
	public $driver;
	protected static $isLogin = false;

	/**
	 * @codeCoverageIgnore
	 *
	 * @param \Throwable $t
	 */
	protected function onNotSuccessfulTest(\Throwable $t): void
	{
		if (isset($this->logs)) {
			echo "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
			\print_r($this->logs);
			print_r(array_shift($t->getTrace()));
		}
		file_put_contents(ROOT_DIRECTORY . '/cache/logs/gui.txt', print_r([
			333,
			$t->__toString(),
			$this->driver
		], true), FILE_APPEND);
		echo "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
		if (null !== $this->driver) {
			print_r($this->driver->getPageSource());
		} else {
			echo 'No $this->driver';
		}
		echo "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
		throw $t;
	}

	/**
	 * Setup test.
	 */
	protected function setUp(): void
	{
		parent::setUp();
		file_put_contents(ROOT_DIRECTORY . '/cache/logs/gui.txt', print_r([
			111111,
			null === $this->driver,
		], true), FILE_APPEND);
		if (null === $this->driver) {
			$capabilities = Facebook\WebDriver\Remote\DesiredCapabilities::chrome();
			$capabilities->setCapability('chromeOptions', ['args' => ['headless', 'disable-dev-shm-usage', 'no-sandbox']]);
	
			$this->driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities, 5000);
			file_put_contents(ROOT_DIRECTORY . '/cache/logs/gui.txt', print_r([
				2222,
				$this->driver,
			], true), FILE_APPEND);
		}
		if (!self::$isLogin) {
			$this->login();
		}
	}

	/**
	 * Go to URL.
	 *
	 * @param string $url
	 *
	 * @throws \ReflectionException
	 */
	public function url(string $url): void
	{
		$this->driver->get(\App\Config::main('site_URL') . $url);
	}

	/**
	 * Testing login page display.
	 */
	public function login(): void
	{
		$this->driver->get(\App\Config::main('site_URL') . 'index.php?module=Users&view=Login');
		$this->driver->findElement(WebDriverBy::id('username'))->sendKeys('demo');
		$this->driver->findElement(WebDriverBy::id('password'))->sendKeys(\Tests\Base\A_User::$defaultPassrowd);
		$this->driver->findElement(WebDriverBy::tagName('form'))->submit();
	}
}
