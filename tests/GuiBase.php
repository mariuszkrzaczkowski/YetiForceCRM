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
	/** @var mixed Last logs. */
	public $logs;

	/** @var \Facebook\WebDriver\Remote\RemoteWebDriver Web driver. */
	protected static $driver;

	/** @var bool Is login */
	protected static $isLogin = false;

	/**
	 * Not success test.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param \Throwable $t
	 *
	 * @return void
	 */
	protected function onNotSuccessfulTest(\Throwable $t): void
	{
		if (isset($this->logs)) {
			echo "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
			\print_r($this->logs);
		}
		echo "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
		if (null !== self::$driver) {
			echo 'URL: ';
			self::$driver->getCurrentURL();
			echo PHP_EOL;
			echo 'Title: ';
			self::$driver->getTitle();
			echo PHP_EOL;
			file_put_contents(ROOT_DIRECTORY . '/cache/logs/selenium_source.png', self::$driver->getPageSource());
			self::$driver->takeScreenshot(ROOT_DIRECTORY . '/cache/logs/selenium_screenshot.png');
		} else {
			echo 'No self::$driver';
			print_r($t->__toString());
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
		if (empty(self::$driver)) {
			$capabilities = DesiredCapabilities::chrome();
			$capabilities->setCapability('chromeOptions', ['args' => ['headless', 'disable-dev-shm-usage', 'no-sandbox']]);
			self::$driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities, 5000);
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
		self::$driver->get(\App\Config::main('site_URL') . $url);
	}

	/**
	 * Testing login page display.
	 */
	public function login(): void
	{
		self::$driver->get(\App\Config::main('site_URL') . 'index.php?module=Users&view=Login');
		self::$driver->findElement(WebDriverBy::id('username'))->sendKeys('demo');
		self::$driver->findElement(WebDriverBy::id('password'))->sendKeys(\Tests\Base\A_User::$defaultPassrowd);
		self::$driver->findElement(WebDriverBy::tagName('form'))->submit();
	}
}
