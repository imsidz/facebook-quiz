<?php

return function() {
    $sqlPatchQuery = file_get_contents(__DIR__ . '/db-patch.sql');

    try{
        // Perform the query
        DB::unprepared($sqlPatchQuery);
    } catch (\PDOException $e) {
        die("Update failed: Error running query : \"" . substr($sqlPatchQuery, 0, 120) . '...' . "\". " . $e->getMessage());
    }

    $languagesDataRow = SiteConfig::where('name', 'languages')->first();
    $languagesData = json_decode($languagesDataRow->value, true);
    $languages = $languagesData['languages'];
    $languagesSchemaObj = new Schemas\LanguagesSchema();
    $languagesSchema = json_decode($languagesSchemaObj->getSchema(), true);

    $defaultStrings = [];
    foreach($languagesSchema['items']['properties']['strings']['properties'] as $key => $data) {
        $defaultStrings[$key] = $data['default'];
    }

    foreach($languages as $key => $language) {
        $languages[$key]['strings'] = array_merge($defaultStrings, $language['strings']);
    }

    $languagesData['languages'] =   $languages;

    $languagesDataRow->value = json_encode($languagesData);
    $languagesDataRow->save();

    die("Update successful");
};