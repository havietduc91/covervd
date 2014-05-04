<?php
function rage_pack()
{
    if(get_conf('ragemaker_language') == 'vn')
    {
    	$data = ('{
    			"Người nổi tiếng" :
    			{
    				"famous1"  : "Người nổi tiếng 1",
    	            "famous2"  : "Người nổi tiếng 2",
    				"football"  : "Cầu thủ bóng đá"
    			},
    			"Biểu cảm" :
    			{
    		
    				"angry" : "Tức giận",
    				"angry2" : "Tức giận 2",
    				"determined" : "Xác cmn định",
    				"focused" : "Tập trung",
    				"foreveralone" : "FA guys",
    				"happy" : "Hạnh phúc",
    				"laugh" : "Vui cười",
    				"neutral" : "Trung lập",
    				"neutralpeople" : "Người trung lập",
    				"neutralwords" : "Những từ trung lập",
    				"pleased" : "Làm ơn",
    				"rage" : "Thịnh nộ",
    				"sad" : "Buồn",
    				"scared" : "Lo sợ",
    				"seriously" : "Nguy hiểm?",
    				"lust" : "Quyến rũ",
    				"surprised" : "Ngạc nhiên"
    			},
    			"Trolls" :
    			{
    				"troll"  : "Phần 1",
    				"troll2" : "Phần 2"
    			},
    			"Chuyên ngành":
    			{
    				"fyea" : "Quá tuyệt!",
    				"milk" : "Trầm trồ",
    				"cereal" : "Đọc báo/tranh luận",
    				"beard" : "Chảy máu mũi",
    				"tf" : "Team Fortress",
    				"various" : "Tổng hợp",
    				"various2" : "Tổng hợp 2",
    				"transitions" : "Biến đổi"
    			},
    			"Đối tượng":
    			{
    				"hair" : "Tóc",
    				"accessories" : "Trang sức",
    				"text" : "Bảng hội thoại"
    			},
    			"Memefriends":
    			{
    				"memefriends" : "Tổng hợp",
    				"fruitmen" : "Mặt trái cây",
    				"dolansfriends" : "Vịt Donan"
    			}
    		}');
    }
    else if(get_conf('ragemaker_language') == 'tf')
    {
    	$data = ('{
    			"คนที่มีชื่อเสียง" :
    			{
    				"football"  : "นักเตะ"
    			},
    			"อารมณ์ความรู้สึก" :
    			{
    		
    				"angry" : "โกรธ",
    				"angry2" : "โกรธ 2",
    				"determined" : "แน่นอน",
    				"focused" : "ที่มุ่งเน้น",
    				"foreveralone" : "FA guys",
    				"happy" : "ยินดี",
    				"laugh" : "หัวเราะ",
    				"neutral" : "เป็นกลาง",
    				"neutralpeople" : "คนที่เป็นกลาง",
    				"neutralwords" : "คำกลาง",
    				"pleased" : "ยินดี",
    				"rage" : "ความโกรธ",
    				"sad" : "เสียใจ",
    				"scared" : "ตกใจ",
    				"seriously" : "อย่างจริงจัง?",
    				"lust" : "เวลาเซ็กซี่",
    				"surprised" : "ประหลาดใจ"
    			},
    			"โทรลล์" :
    			{
    				"troll"  : ส่วนหนึ่ง 1",
    				"troll2" : "ส่วนหนึ่ง 2"
    			},
    			"เฉพาะ":
    			{
    				"fyea" : "F*** Yea!",
    				"milk" : "นม",
    				"cereal" : "ธัญพืช / หนังสือพิมพ์",
    				"beard" : "มหากาพย์เครา",
    				"tf" : "ป้อมทีม",
    				"various" : "ต่างๆ",
    				"various2" : "ต่างๆ 2",
    				"transitions" : "การเปลี่ยน"
    			},
    			"Objects":
    			{
    				"hair" : "ผม",
    				"accessories" : "อุปกรณ์",
    				"text" : "ลูกโป่งคำพูด"
    			},
    			"Memefriends":
    			{
    				"memefriends" : "เพื่อน",
    				"fruitmen" : "ผลไม้",
    				"dolansfriends" : "Donan Duck"
    			}
    		}');
    }
    else if(get_conf('ragemaker_language') == 'en' || get_conf('ragemaker_language') == '' || get_conf('ragemaker_language') == null)
    {
    	$data = ('{
    			"The famous" :
    			{
    				"football"  : "Football players"
    			},
    			"Emotions" :
    			{
    		
    				"angry" : "Angry",
    				"angry2" : "Angry 2",
    				"determined" : "Determined",
    				"focused" : "Focused",
    				"foreveralone" : "Forever Alone",
    				"happy" : "Happy",
    				"laugh" : "Laugh",
    				"neutral" : "Neutral",
    				"neutralpeople" : "Neutral People",
    				"neutralwords" : "Neutral Words",
    				"pleased" : "Pleased",
    				"rage" : "Rage",
    				"sad" : "Sad",
    				"scared" : "Scared",
    				"seriously" : "Seriously?",
    				"lust" : "Sexy time",
    				"surprised" : "Surprised"
    			},
    			"Trolls" :
    			{
    				"troll"  : "Part 1",
    				"troll2" : "Part 2"
    			},
    			"Specialized":
    			{
    				"fyea" : "F*** Yea!",
    				"milk" : "Milk",
    				"cereal" : "Cereal/Newspaper Guy",
    				"beard" : "Epic Beard Guy",
    				"tf" : "Team Fortress",
    				"various" : "Various",
    				"various2" : "Various 2",
    				"transitions" : "Transitions"
    			},
    			"Objects":
    			{
    				"hair" : "Hair",
    				"accessories" : "Accessories",
    				"text" : "Speech Balloons"
    			},
    			"Memefriends":
    			{
    				"memefriends" : "All The Friends ",
    				"fruitmen" : "Fruit Men",
    				"dolansfriends" : "Dolan and Friends"
    			}
    		}');
    }
    $data = json_decode($data,true);
    return $data;
}
