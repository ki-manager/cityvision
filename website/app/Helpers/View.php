<?php

declare(strict_types=1);

/**
 * Minimaler View-Renderer mit Layout-Support.
 */
final class View
{
    public function __construct(
        private readonly string $template,
        private readonly array $data = []
    ) {
    }

    public static function make(string $template, array $data = []): self
    {
        return new self($template, $data);
    }

    public function render(): string
    {
        $file = ROOT_PATH . '/app/Views/' . $this->template . '.php';
        if (!is_file($file)) {
            throw new RuntimeException('View nicht gefunden: ' . $this->template);
        }

        $data = $this->data;
        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return (string) ob_get_clean();
    }

    /**
     * Rendert den View innerhalb des öffentlichen Layouts (Header/Footer).
     */
    public function renderWithLayout(array $layoutData = []): void
    {
        $content = $this->render();
        $data = array_merge($this->data, $layoutData, ['content' => $content]);
        $layout = new self('layouts/default', $data);
        echo $layout->render();
    }

    /**
     * Rendert den View innerhalb des Admin-Layouts.
     */
    public function renderWithAdminLayout(array $layoutData = []): void
    {
        $content = $this->render();
        $data = array_merge($this->data, $layoutData, ['content' => $content]);
        $layout = new self('layouts/admin', $data);
        echo $layout->render();
    }
}
