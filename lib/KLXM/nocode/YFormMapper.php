<?php
namespace KLXM\nocode;

class YFormMapper
{
    private const FIELD_TYPE_MAPPING = [
        'text' => 'text',
        'textarea' => 'textarea',
        'select' => 'select',
        'be_media' => 'media',
        'be_medialist' => 'medialist',
        'be_link' => 'link',
        'be_manager_relation' => 'relation',
        'choice' => 'select',
        'email' => 'text',
        'upload' => 'media',
        'html' => 'textarea',
        'datestamp' => 'date',
        'datetime' => 'datetime',
        'date' => 'date',
        'time' => 'time'
    ];

    public function getTableFields(string $tableName): array
    {
        $table = \rex_yform_manager_table::get($tableName);
        if (!$table) {
            throw new \InvalidArgumentException("YForm table not found: {$tableName}");
        }

        $fields = [];
        foreach ($table->getFields() as $field) {
            if ($field->getType() !== 'value') {
                continue;
            }

            $fieldType = $field->getTypeName();
            $fields[$field->getName()] = [
                'name' => $field->getName(),
                'label' => $field->getLabel() ?: $field->getName(),
                'type' => self::FIELD_TYPE_MAPPING[$fieldType] ?? 'text',
                'db_type' => $fieldType,
                'options' => $this->getFieldOptions($field)
            ];
        }

        return $fields;
    }

    private function getFieldOptions(\rex_yform_manager_field $field): array
    {
        $options = [];
        
        // Handle special field types
        switch ($field->getTypeName()) {
            case 'select':
            case 'choice':
                $options['choices'] = $this->parseOptions($field->getElement('options'));
                break;
            
            case 'be_manager_relation':
                $options['table'] = $field->getElement('table');
                $options['field'] = $field->getElement('field');
                break;
        }

        return $options;
    }

    private function parseOptions(?string $optionsString): array
    {
        if (empty($optionsString)) {
            return [];
        }

        $options = [];
        foreach (explode(',', $optionsString) as $option) {
            $parts = explode('=', $option);
            $options[trim($parts[0])] = trim($parts[1] ?? $parts[0]);
        }

        return $options;
    }

    public function getTableData(
        string $tableName,
        array $mapping,
        ?array $conditions = null,
        ?int $limit = null
    ): array {
        $table = \rex_yform_manager_table::get($tableName);
        if (!$table) {
            throw new \InvalidArgumentException("YForm table not found: {$tableName}");
        }

        $query = $table->query();

        // Apply conditions if provided
        if ($conditions) {
            foreach ($conditions as $field => $value) {
                $query->where($field, $value);
            }
        }

        // Apply limit if provided
        if ($limit !== null) {
            $query->limit($limit);
        }

        $results = [];
        foreach ($query->find() as $item) {
            $data = [];
            foreach ($mapping as $templateField => $yformField) {
                $data[$templateField] = $this->processFieldValue($item, $yformField);
            }
            $results[] = $data;
        }

        return $results;
    }

    private function processFieldValue(\rex_yform_manager_dataset $dataset, string $fieldName): mixed
    {
        $value = $dataset->getValue($fieldName);
        $field = $dataset->getTable()->getValueField($fieldName);

        if (!$field) {
            return $value;
        }

        // Special handling for certain field types
        switch ($field->getTypeName()) {
            case 'be_media':
            case 'upload':
                return \rex_media::get($value);

            case 'be_medialist':
                return array_map(
                    fn($media) => \rex_media::get($media),
                    explode(',', $value)
                );

            case 'be_manager_relation':
                if ($field->getElement('type') == 1) { // Multiple relations
                    return $dataset->getRelatedCollection($fieldName);
                }
                return $dataset->getRelatedDataset($fieldName);

            default:
                return $value;
        }
    }
}
