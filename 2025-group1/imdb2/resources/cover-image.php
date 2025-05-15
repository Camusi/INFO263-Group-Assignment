<?php 
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($query === '') {
    echo json_encode(['error' => 'No search query provided.']);
    exit;
}

$url = "https://www.imdb.com/title/$query";
$data = file_get_contents($url);

if ($data === false) {
    echo json_encode(['error' => 'Failed to fetch IMDb page.']);
    exit;
}

$start = strpos($data, 'https://m.media-amazon.com/images/M/');
if ($start === false) {
    echo json_encode(['error' => 'Cover image not found.']);
    exit;
}
$end = strpos($data, '"', $start);
if ($end === false) {
    echo json_encode(['error' => 'Cover image not found.']);
    exit;
}
$image_url = substr($data, $start, $end - $start);
echo json_encode(['cover_image' => $image_url]);
// TODO: Ca
?>