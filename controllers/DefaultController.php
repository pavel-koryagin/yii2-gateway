<?php

namespace gateway\controllers;

use gateway\GatewayModule;
use gateway\models\Request;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class DefaultController extends Controller {

    public $enableCsrfValidation = false; // @todo Only for first time..

    public function actionStart($gatewayName, $id, $amount, $description = '', $params = []) {
        $process = GatewayModule::getInstance()->start($gatewayName, $id, $amount, $description, $params);
        if ($process->request->method === 'get') {
            $this->redirect((string) $process->request);
        } else {
            // @todo post redirect
        }
    }

    public function actionCheck($gatewayName) {
        $process = GatewayModule::getInstance()->check($gatewayName, $this->getRequest());
        echo $process->responseText;
    }

    public function actionSuccess($gatewayName) {
        GatewayModule::getInstance()->end($gatewayName, true, $this->getRequest());
    }

    public function actionFailure($gatewayName) {
        GatewayModule::getInstance()->end($gatewayName, false, $this->getRequest());
    }

    /**
     * @return Request
     */
    private function getRequest() {
        /** @var \yii\web\Request $request */
        $request = \Yii::$app->request;

        $port = $request->port && $request->port !== 80 ? ':' . $request->port : '';
        return new Request([
            'method' => $request->method,
            'url' => $request->hostInfo . $port . str_replace('?' . $request->queryString, '', $request->url),
            'params' => ArrayHelper::merge($request->get(), $request->post()),
        ]);
    }
}
