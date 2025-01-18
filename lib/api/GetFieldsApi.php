<?php

class rex_api_nocode_get_fields extends rex_api_function
{
    protected $published = true;

    function execute()
    {
        $tableName = rex_request('table', 'string');
        
        if (!$tableName) {
            throw new rex_api_exception('Keine Tabelle angegeben');
        }

        $mapper = new \KLXM\nocode\YFormMapper();
        
        try {
            $fields = $mapper->getTableFields($tableName);
            
            return self::createResult(true, [
                'fields' => $fields
            ]);
            
        } catch (\Exception $e) {
            throw new rex_api_exception($e->getMessage());
        }
    }
}
