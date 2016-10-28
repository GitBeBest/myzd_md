<?php
namespace app\models;

use yii\db\Query;

class AdminBookingManager
{
    public function setDockingCase($dockingCase, $refNo)
    {
        return \Yii::$app->db->createCommand()->update('admin_booking', array('docking_case' => $dockingCase), 'ref_no = :refNo', array(':refNo' => $refNo));
    }
    
    public function getAdminBookingByBookingId($bookingId)
    {
        $query = new Query();
        return $query->select(['date_invalid'])->from('admin_booking')->where('booking_id = :bookingId and booking_type = 2', array(':bookingId' => $bookingId))->one();
    }
       
    public function getAdminBookingByBookingRefNo($refNo)
    {
        $query = new Query();
        return $query->select(['date_invalid'])->from('admin_booking')->where('ref_no = :refNo', array(':refNo' => $refNo))->one();
    }
}