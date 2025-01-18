<?php

class rex_api_nocode_get_fields extends rex_api_function
{
    protected $published = true;

    function execute()
    {
        header('Content-Type: application/json');
        
        $tableName = rex_request('table', 'string');
        
        if (!$tableName) {
            throw new rex_api_exception('Keine Tabelle angegeben');
        }

        $mapper = new \KLXM\nocode\YFormMapper();
        
        try {
            $fields = $mapper->getTableFields($tableName);
            
            rex_response::cleanOutputBuffers();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'fields' => $fields
                ]
            ]);
            exit;
            
        } catch (\Exception $e) {
            rex_response::cleanOutputBuffers();
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
}
