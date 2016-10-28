<?php
namespace app\apiServices;
use app\apiServices\EApiViewService;
use app\models\UserManager;

/**
 * Description of ApiViewBankCandList
 *
 * @author shuming
 */
class ApiViewBankCardList extends EApiViewService {

    private $userId;
    private $cards;
    private $userMgr;

    public function __construct($userId) {
        parent::__construct();
        $this->userId = $userId;
        $this->userMgr = new UserManager();
        $this->cards = array();
    }

    protected function createOutput() {
        $this->output = array(
            'status' => self::RESPONSE_OK,
            'errorCode' => 0,
            'errorMsg' => 'success',
            'results' => $this->results,
        );
    }

    protected function loadData() {
        $this->loadCardList();
    }

    private function loadCardList() {
        $models = $this->userMgr->loadCardsByUserId($this->userId);
        if (arrayNotEmpty($models)) {
            $this->setCards($models);
        }
        $this->results->cards = $this->cards;
    }

    private function setCards(array $models) {
        foreach ($models as $v) {
            $data = new \stdClass();
            $data->id = $v->id;
            $data->bank = $v->bank;
            $data->cardNo = $this->setBankNo($v->card_no);
            $data->name = $this->setName($v->name);
            $data->isDefault = $v->is_default;
            $data->actionUrl = \Yii::$app->urlManager->createAbsoluteUrl('/apimd/bankcardinfo/' . $v->id);
            $this->cards[] = $data;
        }
    }

    private function setName($name) {
        if (strIsEmpty($name)) {
            return $name;
        }
        $len = mb_strlen($name, 'utf-8');
        return mb_substr($name, 0, 1, 'utf-8') . str_repeat('*', ($len - 1));
    }

    private function setBankNo($no) {
        if (strIsEmpty($no) && strlen($no) < 8) {
            return $no;
        }
        $len = strlen($no);
        return substr($no, 0, 3) . str_repeat('*', ($len - 7)) . substr($no, (-4));
    }

}
