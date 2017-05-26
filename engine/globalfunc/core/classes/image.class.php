<?php
class Image {

    private $fileName = null;

    private $ext;

    private $funcExt;

    private $width;

    private $height;

    private $im;

    public function __construct($fileName, $type = null) {
        $this->fileName = $fileName;
        $this->ext = is_null($type) ? strtolower(a(pathinfo($fileName), 'extension')) : $type;
        $this->funcExt = $this->ext;
        if ($this->ext == 'jpg') {
            $this->funcExt = 'jpeg';
        }
        if ($this->funcExt) {
            $func = 'imagecreatefrom' . $this->funcExt;
            $this->im = $func($fileName);
            $this->width = imagesx($this->im);
            $this->height = imagesy($this->im);
        }

    }

    public static function emptyImage($w, $h, $type = 'png') {
        $image = new Image('');
        $image->im = $type == 'gif' ? imagecreate($w, $h) : imagecreatetruecolor($w, $h);
        $image->width  = $w;
        $image->height = $h;
        $image->funcExt = $image->ext = $type;
        return $image;
    }

    public function color($r, $g, $b, $a = false) {
        if ($a === false) {
            return imagecolorallocate($this->im, $r, $g, $b);
        }
        return imagecolorallocatealpha($this->im, $r, $g, $b, $a);
    }

    public function setTransparent($color = false) {
        if ($color === false) {
            imagecolortransparent($this->im);
        } else {
            imagecolortransparent($this->im, $color);
        }
    }

    public function fill($x, $y, $color) {
        imagefill($this->im, $x, $y, $color);
    }

    public function display($sendContentTypeHeader = true) {
        // TODO: проверить, какой mime тип у JPEG по стандартам - image/jpeg, image/jpg или image/pjpeg
        // (не критично, ибо и без того работает)
        if ($sendContentTypeHeader) {
            header("Content-type: image/" . $this->funcExt);
        }
        echo $this->imageData();
    }

    public function imageData() {
        ob_start();
        $func = 'image' . $this->funcExt;
        $func($this->im);
        return ob_get_clean();
    }

    public function header() {
        header("Content-type: image/" . $this->funcExt);
    }

    public function getExt() {
        return $this->funcExt;
    }

    public function getImageBinary() {
        ob_start();
        $this->display(false);
	$ob = ob_get_contents();
	ob_end_clean();
	return $ob;
    }

    public function instance() {
        return $this->im;
    }

    public function height() {
        return $this->height;
    }

    public function width() {
        return $this->width;
    }

    public function copy($image, $dstX, $dstY) {
        imagecopy($this->im, $image->instance(), $dstX, $dstY, 0, 0, $image->width(), $image->height());
    }

    public function resize($w, $h, $params = array()) {
        $newHeight = $this->height;
        $newWidth = $this->width;
        if ($this->width > $w) {
            $scaleX = $w / $this->width;
            $newWidth = ceil($this->width * $scaleX);
            $newHeight = ceil($this->height * $scaleX);
        }
        if ($newHeight > $h) {
            $scaleY = $h / $newHeight;
            $newWidth = ceil($newWidth * $scaleY);
            $newHeight = ceil($newHeight * $scaleY);
        }
        $x = ceil(($w / 2) - ($newWidth / 2));
        $y = ceil(($h / 2) - ($newHeight / 2));
        $image = self::emptyImage($w, $h);
		if (empty($params)) {
			$color = $image->transparent();
		} elseif ($params['bg'] == 'white') {
			$color = $image->white();
		}
        $image->fill(0, 0, $color);
        imagecopyresampled($image->im, $this->im, $x, $y, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
        imagesavealpha($image->im, true);
        $this->im = $image->instance();
        $this->width = $image->width;
        $this->height = $image->height;
        $image->funcExt = $image->ext = 'png';
    }
    
    public function decrease($w, $h, $type = 'gif') {
        $newHeight = $this->height;
        $newWidth = $this->width;
        $ratio = 1;
        if ($this->width > $w) {
            $ratio = $w / $this->width;
            $newHeight = $this->height * $ratio;
        }
        if ($newHeight > $h) {
            $ratio = $h / $this->height;
        }
        $newWidth = $this->width * $ratio;
        $newHeight = $this->height * $ratio;

        $image = self::emptyImage($newWidth, $newHeight, $type);
        imagealphablending($image->im, false);
        imagesavealpha($image->im, true);

        imagecopyresampled($image->im, $this->im, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);

        $this->im = $image->instance();
        $this->width = $image->width;
        $this->height = $image->height;
        $image->funcExt = $image->ext = $type;
    }

    public function __destruct() {
        $this->destroy();
    }

    public function white() {
        return $this->color(255, 255, 255);
    }

    public function black() {
        return $this->color(0, 0, 0);
    }

    public function transparent($alpha = 127) {
        return $this->color(0, 0, 0, $alpha);
    }

    public function destroy() {
        //imagedestroy($this->im);
    }

    //TODO: разобряться, что с imagerotate
    public function rotate($angle, $bgd_color, $ignore_transparent = 0) {
    	$tmp = imagerotate($this->im, $angle, $bgd_color, $ignore_transparent);
    	imagedestroy($this->im);
    	$this->im = $tmp;
    }
}
