<?php
namespace KLXM\nocode;

class Template
{
    private static array $registeredTemplates = [];
    private static array $registeredFrameworks = [];

    public static function registerTemplate(
        string $name,
        string $framework,
        array $fields,
        string $templateFile,
        array $options = []
    ): void {
        // Validate template file exists
        $templatePath = \rex_path::addon('nocode', 'templates/' . $framework . '/' . $templateFile);
        if (!file_exists($templatePath)) {
            throw new \InvalidArgumentException("Template file not found: {$templatePath}");
        }

        self::$registeredTemplates[$name] = [
            'framework' => $framework,
            'fields' => $fields,
            'template' => $templateFile,
            'options' => $options
        ];

        // Register framework if not already registered
        if (!in_array($framework, self::$registeredFrameworks)) {
            self::$registeredFrameworks[] = $framework;
        }
    }

    public static function getTemplate(string $name): ?array
    {
        return self::$registeredTemplates[$name] ?? null;
    }

    public static function getRegisteredTemplates(): array
    {
        return self::$registeredTemplates;
    }

    public static function getRegisteredFrameworks(): array
    {
        return self::$registeredFrameworks;
    }
    
    public static function validateFieldMapping(string $templateName, array $mapping): bool
    {
        $template = self::getTemplate($templateName);
        if (!$template) {
            return false;
        }

        // Check if all required fields are mapped
        foreach ($template['fields'] as $fieldName => $fieldConfig) {
            if (($fieldConfig['required'] ?? false) && !isset($mapping[$fieldName])) {
                return false;
            }
        }

        return true;
    }

    public static function renderTemplate(
        string $templateName, 
        array $data, 
        ?string $framework = null
    ): string {
        $template = self::getTemplate($templateName);
        if (!$template) {
            throw new \InvalidArgumentException("Template not found: {$templateName}");
        }

        // Use provided framework or template default
        $framework = $framework ?? $template['framework'];
        $templatePath = \rex_path::addon('nocode', 'templates/' . $framework . '/' . $template['template']);

        // Extract data to make it available in template
        extract($data);

        // Start output buffering
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}
