<?php

$items = ['device_id: 1874581'];

$URL = 'http://imagerest/api/auth';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);

curl_setopt($ch, CURLOPT_HTTPHEADER, $items);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);

$URL = 'http://imagerest/api/imagessync';

curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer',
    'token:'.$result,
    'imageIds:3,7,8,9'
));
$data = curl_exec($ch);
$data = json_decode($data);
curl_close($ch);



echo '<div> Data to delete';
if ($data->deletion) {
    foreach ($data->deletion as $item) {

        echo '<div>
                ' . $item . '
            </div>';
    }
}
echo '</div><br><br>';

echo '<div> Data to add';
if ($data->images) {
    foreach ($data->images as $img) {
        echo '<div>
                <a href="' . $img->url . '" download>' . $img->title . ' </a>
            </div>';
    }
}
echo '</div>';




