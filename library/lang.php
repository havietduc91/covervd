<?php
function site_strings($lang = '')
{
	if ($lang == '')
	{
		$lu = Zend_Registry::get('user');
		$lang = isset($lu['settings']['language']) ? $lu['settings']['language'] : LANGUAGE;
	}

	$site_strings = array(
			'en' => array(
					//common vocablary
					'feedback' => 'feedback',
					'login_signup' => 'Login/Signup',
					'lesson_timeline_exercise_introduction' => 'lets review the lesson so far with some little quizzes',
			),
			'vn' => array(
					'sign_up_account' => 'đăng ký tài khoản mới',
					'home_page_title' => 'truyện cười, ảnh vui, video clip hài, cười',
					'home_page_desc' => 'truyện cười, ảnh vui, video clip hài, câu đố vui, flash game, truyen cuoi, anh vui, video clip hai, cau do vui, flash game',
					'prev_fun' => "cười lại",
					'next_fun' => "cười tiếp",
					'fun_quiz' => 'đố vui',
					'quiz' => 'đố vui',
					'newest' => 'mới nhất',
					'best' => 'NÉT nhất',
					'upcoming' => 'đang luyện công',
					//common vocablary
					'login_signup' => 'Đăng nhập / Đăng ký',
					'nothing_found' => 'Ở đây tối như mực, không tìm thấy gì cả!',
					'quote' => 'phát ngôn vĩ đại',
					'joke' => 'truyện cười',
					'link' => 'link vui',
					'image' => 'ảnh vui, ảnh hài',
			        'post_image' => 'ảnh',
			        'post_video' => "youtube clip",
			        'post_joke' => "truyện cười",
					'video' => 'video clip hài, phim hài',
					//title for SEO
					'story_title' => 'truyện cười, truyện vui, truyện tiếu lâm',
					'link_title' => 'link vui, link hay',
					'video_title' => 'video clip hài, phim hài',
					'image_title' => 'ảnh hài , ảnh vui',
					'quote_title' => 'phát ngôn "vĩ đại"',
					'quiz_title' => "câu đố vui",
					'flash-game_title' => 'Flash games vui free'
					),
			);
    return isset($site_strings[$lang]) ? $site_strings[$lang] : array();
}