<?php
/**
 * 验证码类
 *
 * @author hkf <876946649@qq.com>
 * @version 0.1.0
 */

/**
 * Class Captcha
 *
 * 生成验证码
 */

class Captcha
{
    /**
     * 图片的宽度
     *
     * @var int
     */
    private $width;

    /**
     * 图片的高度
     *
     * @var int
     */
    private $height;

    /**
     * Class construct
     *
     * @param int $width 图片宽度
     * @param int $height 图片高度
     */
    public function __construct($width = 0, $height = 0)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function createNumberCaptcha()
    {
        $this->isValidImage();

        $image = imagecreatetruecolor($this->width, $this->height);
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $backgroundColor);

        $x = (int)(($this->width) / 6);
        $y = (int)($this->height / 3);
        for ($i = 0; $i < 4; $i++) {
            $number = mt_rand(0, 9);
            $foreColor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            imagestring($image, 5, (mt_rand($x, $x * (1 + mt_rand(0, 4) / 10.0)) + $x * $i), mt_rand(mt_rand($y / 2, $y), $y * 2), $number, $foreColor);
        }

        for ($i = 0; $i < 400; $i++) {
            $pixelColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imagesetpixel($image, mt_rand(1, $this->width), mt_rand(1, $this->height), $pixelColor);
        }

        $w   = imagecolorallocate($image, 255, 255, 255);
        $red = imagecolorallocate($image, 255, 0, 0);
        for ($i = 0; $i < 3; $i ++) {
            $lineColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imagesetstyle($image, array($red, $red, $red, $red, $red, $w, $w, $w, $w, $w));
            imageline($image, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $lineColor);
        }

        header ('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);

    }

    public function createMixedCaptcha()
    {
        $this->isValidImage();
    }

    public function createImageCaptcha()
    {
        $this->isValidImage();
    }

    public function createZNCaptcha()
    {
        $this->isValidImage();
    }

    /**
     * 赋值
     *
     * @param string $name 属性名称
     * @param mixed $value 属性值
     */
    public function __set($name, $value)
    {
        $this->isExists($name);
        $this->$name = $value;
    }

    /**
     * 取值
     *
     * @param string $name 属性名称
     * @return mixed $value 属性值
     */
    public function __get($name)
    {
        $this->isExists($name);
        $value = empty($this->$name) ? 'The value of ' . $name . ' is empty.' : $this->$name;
        return $value;
    }

    /**
     * 判断属性在类中是否存在
     *
     * @param string $name 属性名称
     */
    protected function isExists($name)
    {
        $properties = get_class_vars(get_class($this));

        if (!array_key_exists($name, $properties)) {
            exit($name . 'is not exist in ' . __CLASS__);
        }
    }

    protected function isValidImage()
    {
        if (empty($this->width) || empty($this->height)) {
            exit('非法的图片，图片的宽度或高度不可为空或0.');
        }
    }

}

$captcha = new Captcha(120, 50);
$captcha->createNumberCaptcha();
