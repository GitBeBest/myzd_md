<?php
use yii\web\View;
use yii\helpers\Url;
$doctor = $data->results->doctor;
$honour = $doctor->honour;
$this->title = $doctor->name . '' . ($doctor->aTitle == '无' ? '' : $doctor->aTitle);
$urlResImage = $this->theme->baseUrl . "/images/";
$urlAddPatient = Url::to(['doctor/add-patient', 'id' => '']);
$this->params['show_footer'] = false;
?>
<footer id="viewDoctor_footer" class="bg-white">
    <button id="bookingDoc" class="btn btn-block bg-yellow">预约医师</button>
</footer>
<article id="viewDoctor_article" class="active " data-scroll="true"style="background:#fff;">
    <div>
        <div class="grid pt20 pb20 doctorInfo">
            <div class="col-0">
                <div class="imgDiv ml20">
                    <img class="imgDoc" src="<?= $doctor->imageUrl; ?>">
                </div>
            </div>
            <div class="col-1 ml20 font-s16 color-white">
                <div>
                    <?php
                    if ($doctor->hpDeptName == '') {
                        echo $doctor->mTitle;
                    } else {
                        echo $doctor->hpDeptName . '<span class="ml10">' . $doctor->mTitle . '</span>';
                    }
                    ?>
                </div>
                <div class="grid">
                    <div class="col-0">
                        <?= $doctor->hospitalName; ?>
                    </div> 
                    <div class="col-1"></div>
                </div>
            </div>
        </div>
        <div class="bg-white">
            <?php
            if (isset($doctor->description) && (trim($doctor->description) != '')) {
                ?>
                <div class="ml10 pt10 pr10 pb10 bb-gray3">
                    <div class="font-s16 color-black color-orange">擅长手术:</div>
                    <div class="color-black6"><?= $doctor->description; ?></div>
                </div>
                <?php
            }
            ?>
            <?php if (count($doctor->reasons) != 0) { ?>
                <div class="ml10 pt10 pr10 pb10  bb-gray3">
                    <div class="font-s16 color-orange">
                        推荐理由:
                    </div>
                    <?php
                    for ($i = 0; $i < count($doctor->reasons); $i++) {
                        ?>
                        <div class=" color-black6">
                            <?= $doctor->reasons[$i]; ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php } ?>
            <?php if (isset($honour) && !is_null($honour)) { ?>
                <div class="ml10 pt10 pr10 pb10  bb-gray3">
                    <div class="font-s16 color-orange">
                        获得荣誉:
                    </div>
                    <?php
                    for ($i = 0; $i < count($honour); $i++) {
                        ?>
                        <div class=" color-black6">
                            <?= $honour[$i]; ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php } ?>
            <?php if (isset($doctor->careerExp) && !is_null($doctor->careerExp)) { ?>
                <div class="ml10 pt10 pr10 pb10 ">
                    <div class="font-s16 color-black color-orange">执业经历:</div>
                    <div class="color-black6"><?= $doctor->careerExp; ?></div>
                </div>
            <?php } ?>


        </div>
        <div class="mb10"></div>
    </div>
</article>
<script>
    $(document).ready(function() {
        $('.cardSelect').click(function() {
            var dataCard = $(this).attr('data-card');
            $('.cardSelect').each(function() {
                if (dataCard == $(this).attr('data-card')) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });
            $('.pageCard').each(function() {
                if (dataCard == $(this).attr('data-card')) {
                    $(this).removeClass('hide');
                } else {
                    $(this).addClass('hide');
                }
            });
            $('#viewDoctor_article').scrollTop(0);
        });
        $('#bookingDoc').click(function() {
            sessionStorage.removeItem('travelType');
            sessionStorage.removeItem('detail');
            location.href = '<?= $urlAddPatient; ?><?= $doctor->id; ?>&addBackBtn=1';
        });
    });
</script>