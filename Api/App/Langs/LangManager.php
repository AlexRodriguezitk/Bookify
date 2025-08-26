<?php

namespace App\Langs;

class LangManager
{
    protected array $messages = [];
    protected string $locale;

    public function __construct(?string $locale = null)
    {
        // Si no se pasa locale, usar el definido en .env
        $config = require __DIR__ . '/../../App/config/config.php';
        $this->locale = $locale ?? $config['locale'];
        $file = __DIR__ . "/{$this->locale}.php";

        if (!file_exists($file)) {
            throw new \Exception("Idioma no encontrado: {$this->locale}");
        }

        $this->messages = require $file;
    }

    public function get(string $key, array $params = []): string
    {
        $message = $this->messages[$key] ?? $key;
        foreach ($params as $k => $v) {
            $message = str_replace('{' . $k . '}', $v, $message);
        }
        return $message;
    }
}
