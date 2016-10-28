<?php
namespace app\apiServices;

use app\models\base\ErrorList;
use app\models\core\CoreRasConfig;
use app\util\RsaEncrypt;
use yii\db\Exception;
use yii\helpers\Json;

abstract class EApiViewService {

    const RESPONSE_NO = 'no';
    const RESPONSE_OK = 'ok';   //200
    const RESPONSE_NO_DATA = 'No data'; //400
    const RESPONSE_NOT_FOUND = 'Not found'; //404
    const RESPONSE_VALIDATION_ERRORS = 'Validation errors'; //400

    protected $results;
    public $output; // used for output data.
    public function __construct($value1 = null, $value2 = null, $value3 = null, $value4 = null) {
        $this->results = new \stdClass();
    }

    public function loadApiViewData($pwd = false) {
        try {
            $this->loadData();
            $this->createOutput();
        } catch (Exception $dex) {
            \Yii::error($dex->getMessage(), __METHOD__);
            $this->output = array('status' => self::RESPONSE_NO, 'error' => '数据错误', 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => '数据错误', 'results' => null);
        } catch (\yii\base\Exception $cex) {
            \Yii::error($cex->getMessage(), __METHOD__);
            $this->output = array('status' => self::RESPONSE_NO, 'error' => $cex->getMessage(), 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => $cex->getMessage(), 'results' => null);
        }

        // Converts array to stdClass object.
        if (is_array($this->output)) {
            $this->output = (object) $this->output;
        }
        //数据加密
        if ($pwd) {
            $rasConfig = (new CoreRasConfig())->getByClient("app");
            $str_output = Json::encode($this->output);
            $encrypt = new RsaEncrypt($rasConfig->public_key, $rasConfig->private_key);
            $sign = $encrypt->sign($str_output); //base64 字符串加密
            $encrypt->verify($str_output, $sign);
            $this->output = $encrypt->encrypt($str_output);
        }
        return $this->output;
    }

    /**
     * @abstract method.     
     */
    protected abstract function loadData();

    /**
     * @abstract method     .
     */
    protected abstract function createOutput();

    protected function createErrorOutput($errorMsg = "", $errorCode = 400) {
        $this->output = array(
            'status' => self::RESPONSE_NO,
            'errorCode' => $errorCode,
            'errorMsg' => $errorMsg
        );
    }

    protected function getTextAttribute($value, $nText = true) {
        if ($nText) {
           return \Yii::$app->formatter->format($value, 'Ntext');
        } else {
            return $value;
        }
    }

    protected function throwNoDataException($msg = self::RESPONSE_NO_DATA) {
        throw new Exception($msg);
    }

}
