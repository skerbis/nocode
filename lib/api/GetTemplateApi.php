<?php

class rex_api_nocode_get_template extends rex_api_function
{
    protected $published = true;

    function execute()
    {
        $templateName = rex_request('template', 'string');
        
        if (!$templateName) {
            throw new rex_api_exception('Kein Template angegeben');
        }

        try {
            $template = \KLXM\nocode\Template::getTemplate($templateName);
            
            if (!$template) {
                throw new rex_api_exception('Template nicht gefunden');
            }

            // Extrahiere die Felder und Optionen
            $fields = [];
            $options = [];

            // Einfache Felder direkt übernehmen
            foreach ($template['fields'] as $key => $field) {
                if (!is_array($field) || !isset($field['type'])) {
                    continue;
                }
                if ($field['type'] !== 'array') {
                    $fields[$key] = $field;
                    continue;
                }
                
                // Bei Array-Feldern (z.B. items) die Unterfelder extrahieren
                if (isset($field['fields'])) {
                    foreach ($field['fields'] as $subKey => $subField) {
                        $fields[$key . '.' . $subKey] = $subField;
                    }
                }
            }

            // Optionen extrahieren
            if (isset($template['fields']['options']) && isset($template['fields']['options']['fields'])) {
                $options = $template['fields']['options']['fields'];
            }

            return self::createResult(true, [
                'fields' => $fields,
                'options' => $options
            ]);
            
        } catch (\Exception $e) {
            throw new rex_api_exception($e->getMessage());
        }
    }
}
