<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use OxApp\helpers\Resize;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommands
 *
 * @package Acme\Console\Command
 */
class UploadPhoto extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('upload:photo')
            ->setDescription('Cron jobs');
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require(__DIR__ . "/../../config.php");
        $status = true;
        $dir = scandir('/home/irina/feedPhoto/');
        unset($dir[array_search('.', $dir)]);
        unset($dir[array_search('..', $dir)]);
        $dir = array_values($dir);
        for ($i = 0; $i < 3; $i++) {
            $file = rand(0, count($dir) - 1);
    
            $resize = new Resize();
            $photo = $resize->check('/home/irina/feedPhoto/' . $dir[$file]);//$photo
            unset($dir[$file]);
            $dir = array_values($dir);
    
            $magic = new \Imagick();
            $magic->readimage($photo);
            $magic->flopImage();
            $magic->blurImage(rand(0, 50), rand(6, 10));
            $magic->rotateImage('rgba(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0,
                    255) . ')', rand(0, 5));
            // Создать новый шаблон
            $draw = new \ImagickDraw();
    
            // Свойства шрифта
            //$draw->setFont('Arial');
            $draw->setFontSize(rand(40, 120));
            $draw->setFillColor('rgba(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0,
                    255) . ')');
    
            // Положение текста
            $draw->setGravity(\Imagick::GRAVITY_SOUTHEAST);
    
            // Нарисовать текст на изображении
          //  $magic->annotateImage($draw, rand(10, 150), rand(12, 160), 0, $domain);
    
            $magic->writeimage($photo);
        }
     
        
        //EditProfile
        return $output->writeln("Complite");
    }
    
   public static function randomColor()
    {
        $result = array('rgb' => '', 'hex' => '');
        foreach (array('r', 'b', 'g') as $col) {
            $rand = mt_rand(0, 255);
            $result['rgb'][$col] = $rand;
            $dechex = dechex($rand);
            if (strlen($dechex) < 2) {
                $dechex = '0' . $dechex;
            }
            $result['hex'] .= $dechex;
        }
        
        return $result;
    }
}
