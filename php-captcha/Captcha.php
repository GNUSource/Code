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

    /**
     * 生成数字验证码
     */
    public function createNumberCaptcha()
    {
        $this->isValidImage();
        //  创建底图并填充背景色
        $image = imagecreatetruecolor($this->width, $this->height);
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $backgroundColor);
        //  绘制噪点
        for ($i = 0; $i < 400; $i++) {
            $pixelColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imagesetpixel($image, mt_rand(1, $this->width), mt_rand(1, $this->height), $pixelColor);
        }
        //  绘制干扰线
        $w   = imagecolorallocate($image, 255, 255, 255);
        $red = imagecolorallocate($image, 255, 0, 0);
        for ($i = 0; $i < 4; $i ++) {
            $lineColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imagesetstyle($image, array($red, $red, $red, $red, $red, $w, $w, $w, $w, $w));
            imageline($image, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $lineColor);
        }

        //  生成验证内容
        $x = (int)(($this->width) / 6);
        $y = (int)($this->height / 3);
        for ($i = 0; $i < 4; $i++) {
            $number = mt_rand(0, 9);
            $foreColor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            imagestring($image, 5, (mt_rand($x, $x * (1 + mt_rand(0, 4) / 10.0)) + $x * $i), mt_rand(mt_rand($y / 2, $y), $y * 2), $number, $foreColor);
        }

        //  输出图片
        header ('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    /**
     * 生成字母+数字混合型的验证码
     */
    public function createMixedCaptcha()
    {
        $this->isValidImage();

        //  生成底图，并设置背景色
        $image = imagecreatetruecolor($this->width, $this->height);
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $backgroundColor);
        //  增加噪点
        $pixelColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
        for ($i = 0; $i < 400; $i++) {
            imagesetpixel($image, mt_rand(1, $this->width), mt_rand(1, $this->height), $pixelColor);
        }
        //  绘制干扰线
        for ($i = 0; $i < 4; $i++) {
            $lineColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imageline($image, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $lineColor);
        }

        //  生成验证内容
        $str = '3456789abcdefghijkmnpqrstuvwxy';
        $length = strlen($str) - 1;
        $x = (int)(($this->width) / 6);
        $y = (int)($this->height / 3);
        for ($i = 0; $i < 4; $i++) {
            $char = substr($str, mt_rand(0, $length), 1);
            $foreColor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            imagestring($image, 5, (mt_rand($x, $x * (1 + mt_rand(0, 4) / 10.0)) + $x * $i), mt_rand(mt_rand($y / 2, $y), $y * 2), $char, $foreColor);
        }

        //  展示图片
        header ('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    /**
     * 图片验证码
     */
    public function createImageCaptcha()
    {
        $resources =
            [
                'narutou.png' => '鸣人',
                'zuozhu.png' => '佐助',
                'you.png' => '鼬',
                'ban.png' => '斑'
            ];

        $image = array_rand($resources);

        $file = __DIR__ . '/' . $image;
        $content = file_get_contents($file);

        header ('Content-Type: image/png');
        echo $content;
    }

    /**
     * 中文验证码
     */
    public function createZNCaptcha()
    {
        $this->isValidImage();

        //  生成底图，并设置背景色
        $image = imagecreatetruecolor($this->width, $this->height);
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $backgroundColor);
        //  增加噪点
        $pixelColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
        for ($i = 0; $i < 400; $i++) {
            imagesetpixel($image, mt_rand(1, $this->width), mt_rand(1, $this->height), $pixelColor);
        }
        //  绘制干扰线
        for ($i = 0; $i < 4; $i++) {
            $lineColor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
            imageline($image, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $lineColor);
        }

        //  生成验证内容
        $fontTTF = __DIR__ . '/simhei.ttf';
        if (!is_file($fontTTF)) {
            exit('请先设置ttf文件');
        }
        $str = '三毛在opfw文章a中ds对人物';
//        $array = str_split($str, 3);    //  将字符串切割为数组，注意一个中文汉字占据3个字符。
//        $length = count($array);
        $length = mb_strlen($str) - 1;
        $x = (int)(($this->width) / 6);
        $y = (int)($this->height / 3);
        for ($i = 0; $i < 4; $i++) {
//            $char = $array[mt_rand(0, $length)];
            $char = mb_substr($str, mt_rand(0, $length), 1);
            $foreColor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
            imagettftext($image, mt_rand(20, 24), mt_rand(0, 45), mt_rand($x, (int)($x * (1 + mt_rand(0, 4) / 10.0))) + $x * $i, mt_rand(mt_rand((int)($y * 1.5), 2 * $y), $y * 3), $foreColor, $fontTTF, $char);
        }

        //  展示图片
        header ('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
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

    /**
     * 验证图像是否符合规范
     */
    protected function isValidImage()
    {
        if (empty($this->width) || empty($this->height)) {
            exit('非法的图片，图片的宽度或高度不可为空或0.');
        }
    }

}

$captcha = new Captcha(120, 50);
//$captcha->createNumberCaptcha();
//$captcha->createMixedCaptcha();
$captcha->width = 200;
$captcha->height = 60;
$captcha->createZNCaptcha();
