<?php
namespace PhpSlackBot\Command;

class TranslateCommand extends BaseCommand {



    protected function configure() {
        $this->setName('trans');
    }

    protected function execute($message, $context) {

        if (!isset($data['type']) || !isset($data['user']) || !isset($data['text'])) {
            return;
        }
        if ($message['type'] == 'message') {
            if ($message['user'] == $context['self']['id']) {
                return;
            }
        }

        $translateWatsonResult = $this->translateWatson($message['text']);

        $this->send($this->getCurrentChannel(), null, $translateWatsonResult);

    }


    protected function translateWatson($text){
        $url = "https://gateway.watsonplatform.net/language-translator/api/v2/translate";
        $post_args = array(
            'model_id' => 'ko-en',
            'text' => $text
        );
        $headers = array(
            'Content-Type: application/json','Accept: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, "93610f38-7262-45c1-aed1-1ada56d80153:PxJuCDTjp8fc")  ;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_args));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        echo var_dump(get_object_vars(get_object_vars(json_decode($result))['translations'][0])['translation']);
    }


}