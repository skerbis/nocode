<?php
namespace KLXM\nocode;

class NoCodeGetFields extends \rex_api_function
{
    protected $published = true;

    function execute()
    {
        $tableName = \rex_request('table', 'string');
        
        if (!$tableName) {
            throw new \rex_api_exception('Keine Tabelle angegeben');
        }

        $mapper = new YFormMapper();
        
        try {
            $fields = $mapper->getTableFields($tableName);
            
            return \rex_api_result::factory(true, [
                'fields' => $fields
            ]);
            
        } catch (\Exception $e) {
            throw new \rex_api_exception($e->getMessage());
        }
    }
}
