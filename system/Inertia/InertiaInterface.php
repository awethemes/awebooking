<?php

namespace AweBooking\System\Inertia;

interface InertiaInterface
{
	/***
	 * @param mixed $value
	 */
	public function share(string $key, $value = null): void;

	/**
	 * @return mixed
	 */
	public function getShared(string $key = null);

	/**
	 * @param string $version
	 * @return void
	 */
	public function version(string $version): void;

	/**
	 * @return string
	 */
	public function getVersion(): ?string;

	/**
	 * @param callable $viewCallback
	 */
	public function setViewCallback(callable $viewCallback): void;

	/**
	 * @return callable
	 */
	public function getViewCallback(): callable;

	/**
	 * @return InertiaRequest
	 */
	public function getInertiaRequest(): InertiaRequest;

	/**
	 * @param string $component
	 * @param array $props
	 */
	public function render(string $component, array $props = []): Response;
}
