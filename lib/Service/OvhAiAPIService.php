<?php
/**
 * Nextcloud - OvhAi
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier
 * @copyright Julien Veyssier 2022
 */

namespace OCA\OvhAi\Service;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use OCA\OvhAi\AppInfo\Application;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use Throwable;

class OvhAiAPIService {

	private IClient $client;

	public function __construct(
		string $appName,
		private LoggerInterface $logger,
		private IL10N $l10n,
		private IConfig $config,
		IClientService $clientService
	) {
		$this->client = $clientService->newClient();
	}

	/**
	 * @param string $prompt
	 * @return array|string[]
	 */
	public function queryLlm(string $prompt): array {
		$urlPrefix = $this->config->getAppValue(Application::APP_ID, 'llm_url_prefix');
		if ($urlPrefix === '') {
			return ['error' => 'No URL prefix'];
		}

		$modelName = $this->config->getAppValue(Application::APP_ID, 'llm_model_name', Application::DEFAULT_LLM_NAME) ?: Application::DEFAULT_LLM_NAME;
		$params = [
			'prompt' => $prompt,
			'model' => $modelName,
			'stream' => false,
			'n' => 1,
		];
		$modelExtraParams = $this->getExtraParams('llm_extra_params');
		if ($modelExtraParams !== null) {
			$params = array_merge($modelExtraParams, $params);
		}

		return $this->request($urlPrefix, 'v1/completions', $params, 'POST');
	}

	/**
	 * @param string $configKey
	 * @return array|null
	 */
	private function getExtraParams(string $configKey): ?array {
		$stringValue = $this->config->getAppValue(Application::APP_ID, $configKey);
		if ($stringValue === '') {
			return null;
		}
		$arrayValue = json_decode($stringValue, true);
		if ($arrayValue === null) {
			return null;
		}
		return $arrayValue;
	}

	/**
	 * Make an HTTP request to the API
	 *
	 * @param string $urlPrefix
	 * @param string $endPoint The path to reach
	 * @param array $params Query parameters (key/val pairs)
	 * @param string $method HTTP query method
	 * @return array decoded request result or error
	 */
	public function request(string $urlPrefix, string $endPoint, array $params = [], string $method = 'GET'): array {
		try {
			$apiKey = $this->config->getAppValue(Application::APP_ID, 'api_key');
			if ($apiKey === '') {
				return ['error' => 'No API key'];
			}

			$url = 'https://' . $urlPrefix . '.app.kepler.ai.cloud.ovh.net:443/' . $endPoint;
			$options = [
				'headers' => [
					'User-Agent' => 'Nextcloud OVH AI integration',
					'Content-Type' => 'application/json',
					'Authorization' => 'Bearer ' . $apiKey,
				],
			];

			if (count($params) > 0) {
				if ($method === 'GET') {
					$paramsContent = http_build_query($params);
					$url .= '?' . $paramsContent;
				} else {
					$options['body'] = json_encode($params);
				}
			}

			if ($method === 'GET') {
				$response = $this->client->get($url, $options);
			} elseif ($method === 'POST') {
				$response = $this->client->post($url, $options);
			} elseif ($method === 'PUT') {
				$response = $this->client->put($url, $options);
			} elseif ($method === 'DELETE') {
				$response = $this->client->delete($url, $options);
			} else {
				return ['error' => $this->l10n->t('Bad HTTP method')];
			}
			$body = $response->getBody();
			$respCode = $response->getStatusCode();

			if ($respCode >= 400) {
				return ['error' => $this->l10n->t('Bad credentials')];
			} else {
				return json_decode($body, true) ?: [];
			}
		} catch (ClientException | ServerException $e) {
			$responseBody = $e->getResponse()->getBody();
			$parsedResponseBody = json_decode($responseBody, true);
			if ($e->getResponse()->getStatusCode() === 404) {
				$this->logger->debug('OVH API error : ' . $e->getMessage(), ['response_body' => $responseBody, 'app' => Application::APP_ID]);
			} else {
				$this->logger->warning('OVH API error : ' . $e->getMessage(), ['response_body' => $responseBody, 'app' => Application::APP_ID]);
			}
			return [
				'error' => $e->getMessage(),
				'body' => $parsedResponseBody,
			];
		} catch (Exception | Throwable $e) {
			$this->logger->warning('OVH API error : ' . $e->getMessage(), ['app' => Application::APP_ID]);
			return ['error' => $e->getMessage()];
		}
	}
}
