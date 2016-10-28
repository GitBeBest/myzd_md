<?php
namespace app\models;
use app\models\patient\AppVersion;

class AppManager {

    public function loadAppVersionJson($inputs) {
        $appVersion = $this->loadAppVersion($inputs);
        $output = array('appversion' => $appVersion);
        return $output;
    }

    public function loadAppVersion($inputs) {
        $output = array();
        $errors = $this->validateAppVersionInputs($inputs);
        if (empty($errors) === false) {
            // has error, so return error.
            $output['errors'] = $errors;
            return $output;
        }
        $appVersionNo = $inputs['app_version'];
        $os = $inputs['os'];
        $app_name = isset($inputs['app_name']) ? $inputs['app_name'] : StatCode::APP_NAME_MYZD;
        $modelAppVersion = AppVersion::model()->getLastestVersionByOSAndAppName($os, $app_name);
        if (isset($modelAppVersion) === false) {
            $errors['app_version'] = 'No data.';
            $output['errors'] = $errors;
            return $output;
        }

        $appObj = new \stdClass();
        $appObj->app_version = $appVersionNo;
        $appObj->cur_app_version = $modelAppVersion->getAppVersion();
        $appObj->cur_app_dl_url = $modelAppVersion->getAppDownloadUrl();
        $appObj->force_update = $modelAppVersion->getIsForceUpdate();
        $appObj->change_log = $modelAppVersion->getChangeLog();

        return $appObj;
    }

    private function validateAppVersionInputs($inputs) {
        $errors = array();
        // Compulsory fields.
        $fields = array('os', 'os_version', 'device', 'app_version');
        foreach ($fields as $field) {
            if (isset($inputs[$field]) === false) {
                $errors[$field] = 'Missing ' . $field;
            }
        }
        if (empty($errors) === false) {
            return $errors;
        }

        // OS
        if ($inputs['os'] != 'ios' && $inputs['os'] != 'android') {
            $errors['os'] = 'Unknown os';
        }

        return $errors;
    }

}
