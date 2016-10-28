<?php
namespace app\models\doctor;
use app\models\base\BaseFileModel;
use app\models\user\User;
use yii\base\Exception;
use yii\imagine\Image;

/**
 * This is the model class for table "user_doctor_cert".
 *
 * The followings are the available columns in table 'user_doctor_cert':
 * @property integer $id
 * @property integer $user_id
 * @property integer $cert_type
 * @property string $uid
 * @property string $file_ext
 * @property string $mime_type
 * @property string $file_name
 * @property string $file_url
 * @property integer $file_size
 * @property string $thumbnail_name
 * @property string $thumbnail_url
 * @property string $base_url
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property User $user
 */

class UserDoctorCert extends BaseFileModel {

    public $file_upload_field = 'file'; // $_FILE['file'].   

    /**
     * @return string the associated database table name
     */

    public static function tableName() {
        return 'user_doctor_cert';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['user_id', 'uid', 'file_ext', 'file_name', 'file_url'], 'required'],
            [['user_id', 'cert_type', 'file_size', 'display_order', 'has_remote'], 'number', 'integerOnly' => true],
            [['uid'], 'string', 'max' => 32],
            [['file_ext'], 'string', 'max' => 20],
            [['mime_type'], 'string', 'max' => 20],
            [['file_name', 'thumbnail_name', 'remote_file_key'], 'string', 'max' => 40],
            [['file_url', 'thumbnail_url', 'base_url', 'remote_domain'], 'string', 'max' => 255],
            [['has_remote', 'remote_file_key', 'remote_domain', 'date_updated', 'date_deleted'], 'safe'],
        ];
    }


    public function getUdcUser() {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'cert_type' => 'Cert Type',
            'uid' => 'Uid',
            'file_ext' => 'File Ext',
            'mime_type' => 'Mime Type',
            'file_name' => 'File Name',
            'file_url' => 'File Url',
            'file_size' => 'File Size',
            'thumbnail_name' => 'Thumbnail Name',
            'thumbnail_url' => 'Thumbnail Url',
            'base_url' => 'Base Url',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }


    public function initModel($ownerId, $file) {
        $this->setUserId($ownerId);
        $this->setFileAttributes($file);
    }

    public function saveModel() {
        if ($this->validate()) {    // validates model attributes before saving file.
            try {
                $fileSysDir = $this->getFileSystemUploadPath();
                createDirectory($fileSysDir);
                $thumbImage = Image::thumbnail($this->file->tempName, $this->thumbnail_width, $this->thumbnail_height);
                if ($thumbImage->save($fileSysDir . '/' . $this->getThumbnailName()) === false) {
                    throw new Exception('Error saving file thumbnail.');
                }
                if ($this->file->saveAs($fileSysDir . '/' . $this->getFileName()) === false) {
                    throw new Exception('Error saving file.');
                }

                return $this->save();
            } catch (Exception $e) {
                $this->addError('file', $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    //查询医生文件
    public function getDoctorFilesByUserId($userId, $attrbutes = null, $with = null) {
        return $this->getAllByAttributes(array('t.user_id' => $userId), $with);
    }

    //Overwrites parent::getFileUploadRootPath().
    public function getFileUploadRootPath() {
        return \Yii::$app->params['doctorFilePath'];
    }

    public function getFileSystemUploadPath($folderName = null) {
        return parent::getFileSystemUploadPath($folderName);
    }

    public function getFileUploadPath($folderName = null) {
        return parent::getFileUploadPath($folderName);
    }

    public function deleteModel($absolute = true) {
        return parent::deleteModel($absolute);
    }

    public function getUser() {
        return $this->udcUser;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($v) {
        $this->user_id = $v;
    }

}
