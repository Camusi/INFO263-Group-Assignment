<?php 
$id = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
if ($id === '') {
    echo  'Missing a Query. Bad ID?';
};

if ($type === 'title') {
    $pagePath = '../title/' . $id . '.php';
} else if ($type === 'person') {
    $pagePath = '../person/' . $id . '.php';
} else {
    echo  'Invalid Type. Bad Request?';
}

echo 'Page Path: ' . $pagePath . '<br>';
echo 'Normally we would generate a page there now but missing the template.';

?>