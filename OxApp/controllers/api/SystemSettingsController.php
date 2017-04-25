<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 25.04.17
 * Time: 13:05
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\SystemSettings;

/**
 * Class SystemSettingsController
 * @package OxApp\controllers\api
 */
class SystemSettingsController extends App
{
    /**
     * @return string
     */
    public function get()
    {
        $settings = SystemSettings::find();
        return json_encode([
            'data' => $settings->rows
        ]);
    }

    public function post()
    {
        $settings = SystemSettings::data(
            ['name' => $this->request->request->get('name'),
            'value' => $this->request->request->get('value')]
        )->add();
        if ($settings->count === 1) {
        return json_encode([
            'name' => $this->request->request->get('name'),
            'status' => 'success',
            's' => $this->request->request->get('value')
        ]);
        }

        return json_encode([
            'name' => $this->request->request->get('name'),
            'status' => 'danger',
        ]);
    }

    /**
     * @return string
     */
    public function put()
    {

        $settings = SystemSettings::update(
            ['value' => $this->request->request->get('value')],
            ['id' => $this->request->request->get('id')]
        );
        if ($settings->count === 1) {
            return json_encode([
                'name' => $this->request->request->get('name'),
                'status' => 'success',
            ]);
        }

        return json_encode([
            'name' => $this->request->request->get('name'),
            'status' => 'danger'
        ]);
    }

    public function delete()
    {

    }
}