<?php

declare(strict_types=1);

namespace OCA\OvhAi\TextProcessing;

use OCA\OvhAi\Service\OvhAiAPIService;
use OCP\IL10N;
use OCP\TextProcessing\FreePromptTaskType;
use OCP\TextProcessing\IProviderWithExpectedRuntime;
use OCP\TextProcessing\IProviderWithUserId;
use Psr\Log\LoggerInterface;

/**
 * @implements IProviderWithExpectedRuntime<FreePromptTaskType>
 * @implements IProviderWithUserId<FreePromptTaskType>
 */
class FreePromptProvider implements IProviderWithExpectedRuntime, IProviderWithUserId {

	public function __construct(
		private OvhAiAPIService $ovhAiAPIService,
		private IL10N $l10n,
		private LoggerInterface $logger,
		private ?string $userId,
	) {
	}

	public function getName(): string {
		return $this->l10n->t('OVH AI integration');
	}

	public function process(string $prompt): string {
		try {
			$result = $this->ovhAiAPIService->queryLlm($prompt);

			if (!isset($result['choices'])) {
				throw new \RuntimeException('OVH AI\'s text generation failed: ' . ($result['error'] ?? 'unknown error'));
			}
			if (!is_array($result['choices']) || count($result['choices']) < 1 || !isset($result['choices'][0]['text'])) {
				throw new \RuntimeException('OVH AI\'s text generation failed: malformed OVH API response');
			}
			return trim($result['choices'][0]['text']);
		} catch(\Exception $e) {
			$this->logger->warning('OVH AI\'s text generation failed with: ' . $e->getMessage(), ['exception' => $e]);
			throw new \RuntimeException('OVH AI\'s text generation failed with: ' . $e->getMessage());
		}
	}

	public function getTaskType(): string {
		return FreePromptTaskType::class;
	}

	public function getExpectedRuntime(): int {
		return 20;
	}

	public function setUserId(?string $userId): void {
		$this->userId = $userId;
	}
}
