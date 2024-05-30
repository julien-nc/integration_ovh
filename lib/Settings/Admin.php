<?php

namespace OCA\OvhAi\Settings;

use OCA\OvhAi\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\Settings\ISettings;

class Admin implements ISettings {

	public function __construct(
		private IConfig $config,
		private IInitialState $initialStateService,
	) {
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$apiKey = $this->config->getAppValue(Application::APP_ID, 'api_key');
		$llmModelName = $this->config->getAppValue(Application::APP_ID, 'llm_model_name', Application::DEFAULT_LLM_NAME);
		$llmUrlPrefix = $this->config->getAppValue(Application::APP_ID, 'llm_url_prefix', Application::DEFAULT_LLM_URL_PREFIX);
		$llmExtraParams = $this->config->getAppValue(Application::APP_ID, 'llm_extra_params');

		$adminConfig = [
			'api_key' => $apiKey,
			'llm_model_name' => $llmModelName,
			'llm_url_prefix' => $llmUrlPrefix,
			'llm_extra_params' => $llmExtraParams,
		];
		$this->initialStateService->provideInitialState('admin-config', $adminConfig);

		return new TemplateResponse(Application::APP_ID, 'adminSettings');
	}

	public function getSection(): string {
		return 'ai';
	}

	public function getPriority(): int {
		return 10;
	}
}
