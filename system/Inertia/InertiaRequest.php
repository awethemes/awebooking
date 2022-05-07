<?php

namespace AweBooking\System\Inertia;

final class InertiaRequest
{
	/**
	 * @var bool
	 */
	private $isInertia;

	/**
	 * @var string|null
	 */
	private $partialData;

	/**
	 * @var string|null
	 */
	private $partialComponent;

	/**
	 * @var string|null
	 */
	private $requestVersion;

	/**
	 * @return static
	 */
	public static function createFromGlobals(): self
	{
		return new self(
			(bool) ($_SERVER['HTTP_X_INERTIA'] ?? false),
			$_SERVER['HTTP_X_INERTIA_PARTIAL_DATA'] ?? '',
			$_SERVER['HTTP_X_INERTIA_PARTIAL_COMPONENT'] ?? '',
			$_SERVER['HTTP_X_INERTIA_VERSION'] ?? ''
		);
	}

	/**
	 * @param bool $isInertia
	 * @param string|null $partialData
	 * @param string|null $partialComponent
	 * @param string|null $requestVersion
	 */
	public function __construct(
		bool $isInertia,
		string $partialData = null,
		string $partialComponent = null,
		string $requestVersion = null
	) {
		$this->isInertia = $isInertia;
		$this->partialData = $partialData;
		$this->partialComponent = $partialComponent;
		$this->requestVersion = $requestVersion;
	}

	public function isInertiaRequest(): bool
	{
		return $this->isInertia;
	}

	public function getPartialData(): array
	{
		return array_filter(explode(',', $this->partialData ?? ''));
	}

	public function getPartialComponent(): string
	{
		return $this->partialComponent ?? '';
	}

	public function getRequestVersion(): ?string
	{
		return $this->requestVersion;
	}
}
