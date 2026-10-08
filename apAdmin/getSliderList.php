<?php 

include_once 'common/object.php';
session_start();

extract(array_map("test_input" , $_POST));
if(isset($slider_type)) {
    $alreadyAssignedArray = array();
    $alreadyAssignedSortedArray = array();
    $alreadyAssigned = $d->selectRow("app_slider_id","app_common_slider_master","society_id='$society_id' AND status=0");
    while ($data = $alreadyAssigned->fetch_assoc()) {
        array_push($alreadyAssignedArray, $data);
    }

    for ($j=0; $j < count($alreadyAssignedArray); $j++) { 
        array_push($alreadyAssignedSortedArray, $alreadyAssignedArray[$j]['app_slider_id']);
    }
    
    $alreadyAssignedString = implode("','", $alreadyAssignedSortedArray);
   
    $fq=$d->select("app_slider_master","slider_status = 0 AND app_slider_id NOT IN ('$alreadyAssignedString') AND slider_type!=1");
    $i=1;
    if(mysqli_num_rows($fq)>0) { ?>
        <div class="text-danger font-weight-bold" id="selectError"></div>
        <form id="formSubmit" method="POST" action="controller/assignSliderController.php">
            <input type="checkbox" checked="checked" class="chkparent" id="selectSlider">  <label for="option">Check/Uncheck All</label><br>
            <ul style="list-style: none;">
                <?php while ($sliderData=mysqli_fetch_array($fq)) { ?>
                    <li><label><input class="sliderListCheck" value="<?php echo $sliderData['app_slider_id']; ?>" id="app_slider_id<?php echo $i++; ?>"  checked="" type="checkbox" name="app_slider_id[]"> <img src="../img/sliders/<?php echo $sliderData['slider_image_name'] ?>" width="100">
                    </label></li>
                <?php } ?>
            </ul>
            <input type="hidden" name="csrf" value="<?php echo $_SESSION["token"]; ?>" />
            <input type="hidden" name="society_id" value="<?php echo $society_id; ?>" />
            <input type="hidden" name="assignSliders"/>
            <div class="form-footer text-center">
                <button id="submitBtn" type="submit" class="btn btn-success"><i class="fa fa-check-square-o"></i> SAVE</button>
            </div>
        </form>
    <?php } else {
        echo "No Banner Found";
    }

?>

<script>
    $("#submitBtn").click(function( event ) {
        event.preventDefault();
        var atLeastOneIsChecked = $('.sliderListCheck:checkbox:checked').length > 0;
        if (atLeastOneIsChecked==false) {
            $('#selectError').html('Please select atleast one Slider');
        } else{
            $('#formSubmit').submit();
        }
    });
</script>


<script>
    $("#selectSlider").click(function() {
        $(".sliderListCheck").prop("checked", $(this).prop("checked"));
    });

    $(".sliderListCheck").click(function() {
        if (!$(this).prop("checked")) {
            $("#selectSlider").prop("checked", false);
        }
    });
</script>
<?php } else{
    echo "Something Wrong. Try again after sometime.";
} ?>