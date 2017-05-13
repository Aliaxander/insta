<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 04.04.17
 * Time: 11:52
 *
 * @category  GoodDevices
 * @package   OxApp\helpers
 * @author    Aliaxander
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://oxgroup.media/
 */

namespace OxApp\helpers;

class GoodDevices
{
    /*
   * LAST-UPDATED: MARCH 2017.
     * 
   */
    const DEVICES = [
        '24/7.0; 640dpi; 1440x2560; HUAWEI; LON-L29; HWLON; hi3660',
        '23/6.0.1; 640dpi; 1440x2560; samsung; SM-G935F; hero2lte; samsungexynos8890',
        '23/6.0.1; 640dpi; 1440x2560; samsung; SM-G930F; herolte; samsungexynos8890',
        '23/6.0.1; 480dpi; 1080x1920; samsung; SM-C5000; c5ltechn; qcom',
        '23/6.0.1; 640dpi; 1440x2560; samsung; SM-G925F; zerolte; samsungexynos7420',
        '23/6.0.1; 640dpi; 1440x2560; samsung; SAMSUNG-SM-G935A; hero2qlteatt; qcom',
        '21/5.0.1; 480dpi; 1080x1920; samsung; GT-I9505; jflte; qcom',
        '24/7.0; 480dpi; 1080x1920; samsung; SM-G930K; heroltektt; samsungexynos8890',
        '23/6.0.1; 480dpi; 1080x1920; samsung; SM-G900F; klte; qcom',
        '24/7.0; 640dpi; 1440x2560; samsung; SM-G920F; zeroflte; samsungexynos7420',
        '23/6.0.1; 480dpi; 1080x1920; ZTE; Z981; urd; qcom',
        '23/6.0.1; 640dpi; 1440x2560; ZTE; ZTE A2017U; ailsa_ii; qcom',
        '23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1',
        '23/6.0.1; 640dpi; 1440x2560; samsung; SM-G930P; heroqltespr; qcom',
        '24/7.0; 380dpi; 1080x1920; OnePlus; ONEPLUS A3010; OnePlus3T; qcom',
    ];
    
    /**
     * Retrieve the device string for a random good device.
     *
     * @return string
     */
    public static function getRandomGoodDevice()
    {
        $randomIdx = array_rand(self::DEVICES, 1);
        
        return self::DEVICES[$randomIdx];
    }
    
    /**
     * Retrieve all good devices.
     *
     * @return string[]
     */
    public static function getAllGoodDevices()
    {
        return self::DEVICES;
    }
    
    /**
     * Checks whether a device string is one of the good devices.
     *
     * @param string $deviceString
     *
     * @return bool
     */
    public static function isGoodDevice(
        $deviceString
    ) {
        return in_array($deviceString, self::DEVICES, true);
    }
}