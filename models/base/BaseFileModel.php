<?php

/* Change apache\bin\php.ini settings to allow larger files.
 * post_max_size = 16m
 * upload_max_filesize = 16M
 */

namespace app\models\base;
use yii\base\Exception;
use app\models\base\BaseActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * This is the model class for base file model.
 *
 * @property integer $id
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
 */
abstract class BaseFileModel extends BaseActiveRecord {

    /**
     * @var UploadedFile $file
     */
    public $file;
    public $file_upload_field = 'files';
    protected $thumbnail_width = 90;
    protected $thumbnail_height = 127;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['uid, report_type, file_name, file_url'], 'required'],
            [['file_size', 'display_order'], 'number', 'integerOnly' => true],
            [['uid'], 'string', 'max' => 32, 'min' => 32],
            [['file_name', 'thumbnail_name'], 'string', 'max' => 40],
            [['file_ext'], 'string', 'max'=>10],
            [['mime_type'], 'string', 'max' => 20],
            [['file_url', 'thumbnail_url', 'base_url'], 'string', 'max' => 255],
            [['date_created', 'date_updated', 'date_deleted'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'uid' => 'UID',
            'file_ext' => 'File Extention',
            'mime_type' => 'MIME Type',
            'file_name' => 'File Name',
            'file_url' => 'File Url',
            'file_size' => 'File Size',
            'thumbnail_name' => 'Thumbnail Name',
            'thumbnail_url' => 'Thumbnail Url',
            'display_order' => 'Display Order',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    /**
     * @param UploadedFile $file
     */
    public function setFileAttributes($file) {
        $this->createUID();
        $this->file = $file;
        $this->setFileExtension($file->extension);
        $this->setFileSize($file->size);
        $this->setMimeType($file->type); //Since this MIME type is not checked on the server side, do not take this value for granted. Instead, use CFileHelper::getMimeType to determine the exact MIME type.
        $this->setFileName($this->uid . '.' . $this->getFileExtension());
        $this->setThumbnailName($this->uid . 'tn.' . $this->getFileExtension());
        //URL Path.
        $file_upload_dir = $this->getFileUploadPath();
        $this->setFileUrl($file_upload_dir . '/' . $this->getFileName());
        $this->setThumbnailUrl($file_upload_dir . '/' . $this->getThumbnailName());
        $this->setBaseUrl(\Yii::$app->request->baseUrl);
    }

    /**
     * is_writable(): open_basedir restriction in effect. File(/tmp) is not within the allowed path(s): (/var/php-fpm/5.4/superbeta/tmp:/virtualhost/superbeta
     */
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

                // validation is done before hand, so skip validation when saving into db.
                return $this->save(false);
            } catch (Exception $e) {
                $this->addError('file', $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * gets the relative file upload root path.
     * @return string
     */
    public function getFileUploadRootPath() {
        return '';
    }

    /**
     * gets the file upload path of given folder name.
     * @param string $folderName
     * @return string
     */
    public function getFileUploadPath($folderName = null) {
        if ($folderName === null) {
            return $this->getFileUploadRootPath();
        } else {
            return ($this->getFileUploadRootPath() . $folderName);
        }
    }

    /**
     * get File System Path
     *
     * @param string        	
     * @return string
     */
    public function getFileSystemUploadPath($folderName = null) {
        return \Yii::getAlias('webroot'). DIRECTORY_SEPARATOR . $this->getFileUploadPath($folderName);
    }

    public function getFileLocation() {
        return $this->getFileSystemUploadPath() . DIRECTORY_SEPARATOR . $this->getFileName();
    }

    public function getThumbnailLocation() {
        return $this->getFileSystemUploadPath() . DIRECTORY_SEPARATOR . $this->getThumbnailName();
    }

    protected function createUID() {
        $this->uid = strRandomLong(32);
    }

    public function deleteModel($absolute = false) {
        if ($absolute) {
            if ($this->delete()) {
                try {
                    $fileSysDir = $this->getFileSystemUploadPath();
                    deleteFile($this->getFileLocation());
                    deleteFile($this->getThumbnailLocation());
                    return true;
                } catch (Exception $e) {
                    return false;
                }
            }
        } else {
            $this->date_deleted = new Expression("NOW()");
            return $this->update('date_deleted');
        }
    }

    public function getByUID($uid) {
        return $this->getAttribute(['uid' => $uid]);
    }

    public function getAbsFileUrl() {
        $url = $this->getFileUrl();
        if (strStartsWith($url, 'http')) {
            return $url;
        } else {
            return $this->getRootUrl() . '/' . $url;
        }
    }

    public function getAbsThumbnailUrl() {
        $url = $this->getThumbnailUrl();
        if (strStartsWith($url, 'http')) {
            return $url;
        } else {
            return $this->getRootUrl() . '/' . $url;
        }
    }

    public function getRootUrl() {
        if (isset($this->base_url) && ($this->base_url != '')) {
            return $this->base_url;
        } else {
            return \Yii::$app->request->getBaseUrl();
        }
    }

    public function getUID() {
        return $this->uid;
    }

    public function setUID($v) {
        $this->uid = $v;
    }

    public function getFileUrl() {
        return $this->file_url;
    }

    public function setFileUrl($v) {
        $this->file_url = $v;
    }

    public function getFileExtension() {
        return $this->file_ext;
    }

    public function setFileExtension($v) {
        $this->file_ext = $v;
    }

    public function getFileSize() {
        return $this->file_size;
    }

    public function setFileSize($v) {
        $this->file_size = $v;
    }

    public function getMimeType() {
        return $this->mime_type;
    }

    public function setMimeType($v) {
        $this->mime_type = $v;
    }

    public function getFileName() {
        return $this->file_name;
    }

    public function setFileName($v) {
        $this->file_name = $v;
    }

    public function getThumbnailName() {
        return $this->thumbnail_name;
    }

    public function setThumbnailName($v) {
        $this->thumbnail_name = $v;
    }

    public function getThumbnailUrl() {
        return $this->thumbnail_url;
    }

    public function setThumbnailUrl($v) {
        $this->thumbnail_url = $v;
    }

    public function getBaseUrl() {
        return $this->base_url;
    }

    public function setBaseUrl($v) {
        $this->base_url = $v;
    }

    public function getDisplayOrder() {
        return $this->display_order;
    }

    public function setDisplayOrder($v) {
        $this->display_order = $v;
    }

}
