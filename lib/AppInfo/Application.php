<?php
/**
 * Nextcloud - OvhAi
 *
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 * @copyright Julien Veyssier 2022
 */

namespace OCA\OvhAi\AppInfo;

use OCA\OvhAi\TextProcessing\FreePromptProvider;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;

use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\IConfig;

class Application extends App implements IBootstrap {

	public const APP_ID = 'integration_ovhai';

	public const DEFAULT_LLM_NAME = '/mistralai/Mistral-7B-Instruct-v0.1';
	public const DEFAULT_LLM_URL_PREFIX = 'TODO';
	//public const DEFAULT_LLM_NAME = '/OVHcloud/Mixtral-8x7B-Instruct-v0.1-AWQ';

	private IConfig $config;

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);

		$container = $this->getContainer();
		$this->config = $container->get(IConfig::class);
	}

	public function register(IRegistrationContext $context): void {
		$apiKey = $this->config->getAppValue(self::APP_ID, 'api_key');
		if ($apiKey !== '') {
			$context->registerTextProcessingProvider(FreePromptProvider::class);
		}
	}

	public function boot(IBootContext $context): void {
	}
}
