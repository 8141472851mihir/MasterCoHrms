<?php
include_once 'common/object.php';

$text = $_POST['text'];
$languageNames = json_decode(urldecode($_POST['language_names']), true);
$key_name = $_POST['key_name'];
$maxCharacters = $d->language_translate_monthly_limit();
list($currentMonthYear, $currentTotal) = $d->language_translate_get_monthly_total();

$codeMap = $d->language_translate_code_map();
$dbLanguages = $d->language_translate_languages_from_db();
$dbLanguageByName = [];
foreach ($dbLanguages as $dbLanguage) {
    $dbLanguageByName[$dbLanguage['language_name']] = $dbLanguage;
}

$languages = [];
foreach ((array) $languageNames as $languageName) {
    $name = trim($languageName);
    if (isset($dbLanguageByName[$name])) {
        $languages[] = $dbLanguageByName[$name];
        continue;
    }

    $languages[] = [
        'language_name' => $name,
        'language_code' => $codeMap[$name] ?? '',
        'is_english_language' => (($codeMap[$name] ?? '') === 'en') ? 1 : 0,
    ];
}

$potentialCharacters = 0;
foreach ($languages as $language) {
    if ($d->language_translate_is_english($language)) {
        continue;
    }
    $targetLang = $d->language_translate_resolve_code(
        $language['language_name'],
        $language['language_code'] ?? ''
    );
    if ($targetLang !== '') {
        $potentialCharacters += strlen($text);
    }
}

if ($currentTotal >= $maxCharacters) {
    $response = [
        'status' => 'error',
        'message' => 'Monthly character limit (4 lakh) already exceeded',
        'translations' => [],
        'translation_errors' => [],
        'current_total' => $currentTotal,
        'month_year' => $currentMonthYear
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} elseif (($currentTotal + $potentialCharacters) > $maxCharacters) {
    $response = [
        'status' => 'error',
        'message' => 'This operation would exceed monthly character limit (4 lakh)',
        'current_total' => $currentTotal,
        'attempted_addition' => $potentialCharacters,
        'month_year' => $currentMonthYear,
        'translations' => [],
        'translation_errors' => []
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$translationErrors = [];
$translationResults = $d->language_translate_all($text, $languages, $translationErrors);
list(, $newTotal) = $d->language_translate_get_monthly_total();

$response = [
    'status' => count($translationErrors) === 0 ? 'success' : 'partial_success',
    'message' => count($translationErrors) === 0
        ? 'All translations completed successfully'
        : 'Some translations failed',
    'translations' => $translationResults,
    'translation_errors' => $translationErrors,
    'translated_characters' => max(0, $newTotal - $currentTotal),
    'current_total' => $newTotal,
    'original_text' => $text,
    'key_name' => $key_name,
    'month_year' => $currentMonthYear,
    'all_languages' => $languageNames,
    'limit' => $maxCharacters
];

header('Content-Type: application/json');
echo json_encode($response);
?>
