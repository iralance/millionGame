<?php
    function curl_request($url,$post='',$cookie='', $returnCookie=0){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://www.baidu.com");
        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
    }
    function get_question(){
        $resp = file_get_contents('http://htpmsg.jiecaojingxuan.com/msg/current');
        //$resp = '{"code":0,"msg":"成功","data":{"event":{"answerTime":10,"correctOption":1,"desc":"10.综艺《快乐大本营》是从哪一年开始播出的？  ","displayOrder":9,"liveId":97,"options":"[\"1995年\",\"1997年\",\"1990年\"]","questionId":1111,"showTime":1515676298455,"stats":[8556,31933,1829],"status":2,"type":"showAnswer"},"type":"showAnswer"}}';
        $resp_dict = json_decode($resp,true);
        if($resp_dict['msg'] == 'no data'){
            echo "................................\r\n";
        }else{
            $question = $resp_dict['data']['event']['desc'];
            $question = trim(substr($question,strpos($question,'.')+1));
            $answers = $resp_dict['data']['event']['options'];
            echo "******************************************\r\n";
            var_dump($question);
            var_dump($answers);
            echo "******************************************\r\n";
            $answer_array = explode(',',$answers);
            $answer_array = str_replace(['["','"]','"'],[''],$answer_array);
            $url = "https://www.baidu.com/s?wd=".$question;
            $query = curl_request($url);
            $matches = [];
            foreach($answer_array as $v){
                echo "{$v}匹配到的次数是：".preg_match_all('/'.$v.'/',$query,$matches)."\r\n";
            }
        }
    }

    while(1){
        echo "当前时间".date('Y-m-d H:i:s')."\r\n";
        get_question();
        sleep(1);
    }
    