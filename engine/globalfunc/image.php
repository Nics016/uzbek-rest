<?php
/**
 * Масштабирование изображения
 *
 * @param string $fileName полный путь к файлу
 * @param array $params параметры масштабирования
 * - width integer
 * - height integer
 * - bgcolor array('r' => 255, 'g' => 255, 'b' => 255) цвет подложки
 * - border integer толщина рамки
 * - brdcolor array('r' => 255, 'g' => 255, 'b' => 255) цвет рамки
 * @return string изображение
 */
function scaleImage($fileName, $params) {
    ob_start();
    if (! file_exists($fileName)) {
        return;
    }

    $padding = 0;
    if (isset($params['padding'])) {
        //FIXME не правильно работает паддинг, растягивает картинки
        //    $padding = $params['padding'];
    }

    $image = imagecreatefromjpeg($fileName);
    if (! $image) {
        $image = imagecreatefromgif($fileName);
        if (! $image) {
            $image = imagecreatefrompng($fileName);
            if (! $image) {
                throw new Exception('Не могу открыть изображение ' . $fileName);
            }
        }
    }
    $imgInfo = getimagesize($fileName);
    if (! isset($params['height'])) {
        $params['height'] = $imgInfo[1];
    }
    if (! isset($params['width'])) {
        $params['width'] = $imgInfo[0];
    }

    $scaledImage = imagecreatetruecolor($params['width'], $params['height']);
    if (! $scaledImage) {
        throw new Exception('Не могу создать изображение');
    }
    $imageW = imagesx($image);
    $imageH  = imagesy($image);
    $bgcolor['r'] = 255;
    $bgcolor['g'] = 255;
    $bgcolor['b'] = 255;
    if (isset($params['bgcolor'])) {
        $bgcolor = $params['bgcolor'];
    }
    imagefilledrectangle($scaledImage, 0, 0, $params['width'], $params['height'], imagecolorallocate($scaledImage, $bgcolor['r'], $bgcolor['g'], $bgcolor['b']));
    $scaledImageW = $imageW;
    $scaledImageH = $imageH;
    // определяем новые размеры изображения
    if ($imageW > $params['width']) {
        $scaleX = $params['width'] / $imageW;
        $scaledImageW = ceil($imageW * $scaleX);
        $scaledImageH = ceil($imageH * $scaleX);
    }
    if ($scaledImageH > $params['height']) {
        $scaleY = $params['height'] / $scaledImageH;
        $scaledImageW = ceil($scaledImageW * $scaleY);
        $scaledImageH = ceil($scaledImageH * $scaleY);
    }
    $scaledImageX = ceil(($params['width'] / 2) - ($scaledImageW / 2));
    $scaledImageY = ceil(($params['height'] / 2) - ($scaledImageH / 2));
    imagecopyresampled($scaledImage, $image, $scaledImageX + $padding, $scaledImageY + $padding, 0, 0, $scaledImageW - 2 * $padding, $scaledImageH - 2 * $padding, $imageW, $imageH);
    // добавление рамки
    if (! empty($params['border'])) {
        $brdcolor['r'] = 100;
        $brdcolor['g'] = 100;
        $brdcolor['b'] = 100;
        if (isset($params['brdcolor'])) {
            $brdcolor = $params['brdcolor'];
        }
        $borderWidth = $params['border'];
        $borderColor = imagecolorallocate($scaledImage, $brdcolor['r'], $brdcolor['g'], $brdcolor['b']);
        imagelinethick($scaledImage, 0, 0, 0, ($params['height'] - 1), $borderColor, $borderWidth);
        imagelinethick($scaledImage, 0, 0, ($params['width'] - 1), 0, $borderColor, $borderWidth);
        imagelinethick($scaledImage, ($params['width'] - 1), ($params['height'] - 1), ($params['width'] - 1), 0, $borderColor, $borderWidth);
        imagelinethick($scaledImage, ($params['width'] - 1), ($params['height'] - 1), 0, ($params['height'] - 1), $borderColor, $borderWidth);
    }
    imagejpeg($scaledImage);
    $img = ob_get_contents();
    ob_end_clean();
    return $img;
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1) {
    /* this way it works well only for orthogonal lines
    imagesetthickness($image, $thick);
    return imageline($image, $x1, $y1, $x2, $y2, $color);
    */
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
    round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
    round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
    round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
    round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}