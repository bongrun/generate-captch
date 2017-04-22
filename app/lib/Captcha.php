<?php

namespace Lib;

class Captcha
{
    private $text;
    private $textImage;
    private $backgroundImage;
    private $image;
    private $backgroundImageWidth = 200;
    private $backgroundImageHeight = 50;
    private $textImageWidth = 180;
    private $textImageHeight = 40;

    public function __construct()
    {
        define("BACKGROUND_IMAGE", APP_PATH . "/background/");
        define("FONTS", APP_PATH . "/fonts/");
        define("CAPTCHA", APP_PATH . "/captcha/");
    }

    private function generateText()
    {
        $chars = 'abdefhknrstyz23456789'; // Задаем символы, используемые в капче. Разделитель использовать не надо.
        $length = rand(4, 7); // Задаем длину капчи, в нашем случае - от 4 до 7
        $numChars = strlen($chars); // Узнаем, сколько у нас задано символов
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, rand(1, $numChars) - 1, 1);
        } // Генерируем код
        // Перемешиваем, на всякий случай
        $arrayMix = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
        srand((float)microtime() * 1000000);
        shuffle($arrayMix);
        // Возвращаем полученный код
        $this->text = implode("", $arrayMix);
    }

    private function generateBackgroundImage()
    {
        // Количество линий. Обратите внимание, что они накладываться будут дважды (за текстом и на текст). Поставим рандомное значение, от 3 до 7.
        $linenum = rand(3, 7);
        $backgroundImages = scandir(BACKGROUND_IMAGE);
        $backgroundImage = BACKGROUND_IMAGE . $backgroundImages[rand(2, count($backgroundImages) - 1)];
        $im = imagecreatefrompng($backgroundImage);
        // Рисуем линии на подстилке
        for ($i = 0; $i < $linenum; $i++) {
            $color = imagecolorallocate($im, rand(0, 150), rand(0, 100), rand(0, 150)); // Случайный цвет c изображения
            imageline($im, rand(0, 20), rand(1, 50), rand(150, 180), rand(1, 50), $color);
        }
        $this->backgroundImage = $im;
    }

    /**
     * Искажение текста
     */
    private function distortText()
    {
        $img = imagecreatetruecolor($this->textImageWidth, $this->textImageHeight);
        imagesavealpha($img, true);
        $trans_colour = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $trans_colour);
        $x = rand(0, 35);
        for($i = 0; $i < strlen($this->text); $i++) {
            $x+=15;
            $letter=substr($this->text, $i, 1);
            $color = imagecolorallocate($img, rand(20, 70), rand(20, 70), rand(20, 70));
            $fonts = scandir(FONTS);
            $font = FONTS . $fonts[rand(2, count($fonts) - 1)];
            imagettftext ($img, rand(20, 30), rand(-5, 5), $x, rand(30, 35), $color, $font, $letter);
        }
        $this->textImage = $img;
    }

    /**
     * Наложение
     */
    private function imposeTextToBackground()
    {
        $this->image = $this->backgroundImage;
        imagecopy(
            $this->backgroundImage, $this->textImage, 10, 5, 0, 0,
            $this->textImageWidth, $this->textImageHeight
        );
    }

    public function generate()
    {
        $this->generateText();
        $this->generateBackgroundImage();
        $this->distortText();
        $this->imposeTextToBackground();
    }

    public function getText()
    {
        return $this->text;
    }

    public function getImage()
    {
        $path = CAPTCHA . md5($this->text . '123') . time() . '.png';
        imagePng($this->image, $path);
        return $path;
    }
}
