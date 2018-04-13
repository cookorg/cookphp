<?php

namespace library;

/**
 * 验证码
 * @author cookphp <admin@cookphp.org>
 */
class Captcha {

    /**
     * @var int Image Width
     */
    public $width = 160;

    /**
     * @var int Image Height
     */
    public $height = 50;

    /**
     * @var int Default font size
     */
    public $fontSize = 24;

    /**
     * @var array
     */
    public $customFonts = [
        '3dlet.ttf' => ['size' => 32, 'case' => 1],
        'baby_blocks.ttf' => ['size' => 16, 'case' => 0],
        'betsy_flanagan.ttf' => ['size' => 28, 'case' => 0],
        'granps.ttf' => ['size' => 26, 'case' => 2],
        'karmaticarcade.ttf' => ['size' => 20, 'case' => 0],
        'tonight.ttf' => ['size' => 28, 'case' => 0],
    ];

    /**
     * @var int
     */
    public $lenghtMin = 3;

    /**
     * @var int
     */
    public $lenghtMax = 5;

    /**
     * @var string
     */
    public $letters = '2356789ABCDEGHKMNPQSUVXYZabcdeghkmnpqsuvxyz';

    /**
     * 验证码生成
     * @return string
     */
    public function generateCode() {
        $lenght = mt_rand($this->lenghtMin, $this->lenghtMax);
        do {
            $code = substr(str_shuffle(str_repeat($this->letters, 3)), 0, $lenght);
        } while (preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp|ww/', $code));
        return $code;
    }

    /**
     * 验证码图像生成
     * @param string $string
     * @param bool $base64
     * @return mixed
     */
    public function generateImage($string, $base64 = true) {
        $font = $this->chooseFont();
        $captcha = $this->prepareString($string, $font);
        $image = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        $this->drawText($image, $captcha, $font);
        ob_start();
        imagepng($image);
        imagedestroy($image);
        return $base64 ? 'data:image/png;base64,' . base64_encode(ob_get_clean()) : ob_get_clean();
    }

    /**
     * 绘制图像上的文字
     * @param resource $image
     * @param array    $captcha
     * @param string   $font
     */
    private function drawText(&$image, array $captcha, $font) {
        $len = count($captcha);
        for ($i = 0; $i < $len; $i++) {
            $xPos = ($this->width - $this->fontSize) / $len * $i + ($this->fontSize / 2);
            $xPos = mt_rand($xPos, $xPos + 5);
            $yPos = $this->height - (($this->height - $this->fontSize) / 2);
            $capcolor = imagecolorallocate($image, rand(0, 150), rand(0, 150), rand(0, 150));
            $capangle = rand(-25, 25);
            imagettftext($image, $this->fontSize, $capangle, $xPos, $yPos, $capcolor, $font, $captcha[$i]);
        }
    }

    /**
     * 从可用列表中选择一个随机字体
     * @return string
     */
    private function chooseFont() {
        $dir = BASEPATH . 'fonts' . DS;
        $fontsList = glob($dir . '*.ttf');
        $font = basename($fontsList[mt_rand(0, count($fontsList) - 1)]);
        return $dir . $font;
    }

    /**
     * 设置字体大小
     * @param string $string
     * @param string $font
     * @return array
     */
    private function prepareString($string, $font) {
        $font = basename($font);
        if (isset($this->customFonts[$font])) {
            $args = $this->customFonts[$font];
            $this->fontSize = $args['size'];
            $string = $this->setCase($string, $args);
        }
        return str_split($string);
    }

    /**
     * 设置字体大小写
     * @param string $string
     * @param array  $args
     * @return string
     */
    private function setCase($string, array $args) {
        switch ($args['case']) {
            case 2:
                return strtoupper($string);
            case 1:
                return strtolower($string);
        }
        return $string;
    }

}
