<?php

namespace AweBooking\System\Inertia;

class Inertia implements InertiaInterface
{
    /**
     * @var InertiaRequest
     */
    private $request;

    /**
     * @var array
     */
    protected $sharedProps = [];

    /**
     * @var string
     */
    protected $version;

    /**
     * @var callable|null
     */
    protected $viewCallback;

    /**
     * @param InertiaRequest $request
     * @param string $version
     * @param callable|null $viewCallback
     */
    public function __construct(InertiaRequest $request, string $version, callable $viewCallback = null)
    {
        $this->request = $request;
        $this->version = $version;
        $this->viewCallback = $viewCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function share(string $key, $value = null): void
    {
        $this->sharedProps[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getShared(string $key = null)
    {
        if (is_null($key)) {
            return $this->sharedProps;
        }

        return $this->sharedProps[$key] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function version(string $version): void
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param callable $viewCallback
     */
    public function setViewCallback(callable $viewCallback): void
    {
        $this->viewCallback = $viewCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewCallback(): callable
    {
        return $this->viewCallback
            ?? static function (array $data) {
                return '';
            };
    }

    /**
     * {@inheritdoc}
     */
    public function getInertiaRequest(): InertiaRequest
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $component, array $props = []): Response
    {
        return new Response(
            $this->getInertiaRequest(),
            $component,
            array_merge($this->getShared(), $props),
            $this->getVersion(),
            $this->getViewCallback()
        );
    }
}
