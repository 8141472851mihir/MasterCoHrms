<?php

include '../common/objectController.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
extract($_POST);
if (isset($_POST['Addfaq'])) {
	// print_r(($faq_attachment));exit;
	if (isset($_FILES['faq_attachment']['tmp_name']) && file_exists($_FILES['faq_attachment']['tmp_name'])) {
		$file_faq_attachment = $_FILES['faq_attachment']['tmp_name'];
		$acceptable = array("jpeg", "jpg", "png", "webp", "pdf");
		$ext = strtolower(pathinfo($_FILES['faq_attachment']['name'], PATHINFO_EXTENSION));
		$uploadDir = "../../img/";

		if (in_array($ext, $acceptable) && !empty($_FILES["faq_attachment"]["type"])) {
			$temp = explode(".", $_FILES["faq_attachment"]["name"]);
			$faq_attachment = 'FAQ_' . round(microtime(true)) . '.' . end($temp);
			$destinationPath = $uploadDir . $faq_attachment;

			if (in_array($ext, ['jpeg', 'jpg', 'png', 'webp'])) {
				$d->resizeImage($file_faq_attachment, $destinationPath, 800, 800, $ext); // use your resizeImage method
			} else {
				move_uploaded_file($file_faq_attachment, $destinationPath);
			}
		} else {
			$_SESSION['msg1'] = "Invalid file format!";
			header("location:../manageFaqs");
			exit();
		}
	} else {
		$faq_attachment = "";
	}

	$m->set_data("faq_question", $faq_question);
	$m->set_data("faq_answer", $faq_answer);
	$m->set_data("platform_type", $platform_type);
	$m->set_data("faq_attachment", $faq_attachment);
	$m->set_data("category_type", $category_type); 
	$m->set_data("language_id", $language_id); 

	$a = array(
		'faq_question' => $m->get_data('faq_question'),
		'faq_answer' => $m->get_data('faq_answer'),
		'platform_type' => $m->get_data('platform_type'),
		'faq_attachment' => $m->get_data('faq_attachment'),
		'category_type'  => $m->get_data('category_type'),
		'language_id'  => $m->get_data('language_id') 
	);

	$q = $d->insert("faq_question_master", $a);
	if ($q == TRUE) {
		$_SESSION['msg'] = 'FAQ added successfully!';
		header('location:../manageFaqs');
	} else {
		$_SESSION['msg1'] = 'Something wrong!!';
		header('location:../manageFaqs');
	}
} elseif (isset($_POST['Editfaq'])) {
	if (isset($_POST['remove_faq_attachment']) && $_POST['remove_faq_attachment'] == "1") {
        $uploadDir = "../../img/";
        if (!empty($_POST['faq_attachment_old']) && file_exists($uploadDir . $_POST['faq_attachment_old'])) {
            unlink($uploadDir . $_POST['faq_attachment_old']);
        }
        $faq_attachment = null;
    } else {
	if (isset($_FILES['faq_attachment']['tmp_name']) && file_exists($_FILES['faq_attachment']['tmp_name'])) {
		$file_faq_attachment = $_FILES['faq_attachment']['tmp_name'];
		$acceptable = array("jpeg", "jpg", "png", "webp", "pdf");
		$ext = strtolower(pathinfo($_FILES['faq_attachment']['name'], PATHINFO_EXTENSION));
		$uploadDir = "../../img/";

		if (in_array($ext, $acceptable) && !empty($_FILES["faq_attachment"]["type"])) {
			$temp = explode(".", $_FILES["faq_attachment"]["name"]);
			$faq_attachment = 'FAQ_' . round(microtime(true)) . '.' . end($temp);
			$destinationPath = $uploadDir . $faq_attachment;

			if (!empty($_POST['faq_attachment_old']) && file_exists($uploadDir . $_POST['faq_attachment_old'])) {
				unlink($uploadDir . $_POST['faq_attachment_old']);
			}

			if (in_array($ext, ['jpeg', 'jpg', 'png', 'webp'])) {
				$d->resizeImage($file_faq_attachment, $destinationPath, 800, 800, $ext); // Resize image before saving
			} else {
				move_uploaded_file($file_faq_attachment, $destinationPath);
			}
		} else {
			$_SESSION['msg1'] = "Invalid file format!";
			header("location:../manageFaqs");
			exit();
		}
	} else {
		$faq_attachment = $_POST['faq_attachment_old'] ?? '';
	}
	}
	$m->set_data('faq_answer', $faq_answer);
	$m->set_data('faq_question', $faq_question);
	$m->set_data("platform_type", $platform_type);
	$m->set_data("faq_attachment", $faq_attachment);
	$m->set_data("category_type", $category_type);
	$m->set_data("language_id", $language_id); 

	$a = array(
		'faq_question' => $m->get_data('faq_question'),
		'faq_answer' => $m->get_data('faq_answer'),
		'platform_type' => $m->get_data('platform_type'),
		'faq_attachment' => $m->get_data('faq_attachment'),
		'category_type'  => $m->get_data('category_type'),
		'language_id'  => $m->get_data('language_id') 
	);

	// Perform the update query
	$faq_sub_master_id = $d->sanitizeActionIdAsInt($faq_sub_master_id ?? ($_POST['faq_sub_master_id'] ?? 0));
	$q = $d->update("faq_question_master", $a, "faq_sub_master_id='$faq_sub_master_id'");
	if ($q == TRUE) {
		$_SESSION['msg'] = 'FAQ updated successfully!';
		header('location:../manageFaqs');
	} else {
		$_SESSION['msg1'] = 'Something went wrong!';
		header('location:../manageFaqs');
	}
} else {
	$_SESSION['msg1'] = "Something wrong. Try again after sometime";
	header("location:../welcome");
}

?>