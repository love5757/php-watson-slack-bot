<?php
/**
 * Created by PhpStorm.
 * User: 최중영
 * Date: 2017-08-04
 * Time: 오후 5:04
 */

require 'vendor/autoload.php';
use PhpSlackBot\Bot;

// Custom command
class MyCommand extends PhpSlackBot\Command\BaseCommand {

    protected function configure() {
        $this->setName('mycommand');
    }

    protected function execute($message, $context) {
        $this->send($this->getCurrentChannel(), null, 'Hello !');
    }
}

class EchoCommand extends PhpSlackBot\Command\BaseCommand {

    protected function configure() {
    }

    protected function execute($data, $context) {

        if (!isset($data['type']) || !isset($data['user']) || !isset($data['text'])) {
            return;
        }

        if ($data['type'] == 'message') {
            if ($data['user'] == $context['self']['id']) {
                return;
            }
            $mention_self = '<@' . $context['self']['id'] . '>';
            $mention_self_text_position = strpos($data['text'], $mention_self);
            $channel = $this->getChannelNameFromChannelId($data['channel']);
            if ($mention_self_text_position === false && $channel) {
                return;
            }
            if (!isset($data['thread_ts'])) {
                $data['thread_ts'] = null;
            }
            $text = str_replace($mention_self, '', $data['text']);
            $text = preg_replace('/(^|\s)[\p{C}]*($|\s)/', ' ', $text);
            $text = trim($text);

            $message = $text;

            $this->send($data['channel'], $data['user'], $message, $data['thread_ts']);
            }
    }

}


$bot = new Bot();
$bot->setToken(''); // Get your token here https://my.slack.com/services/new/bot
$bot->loadCommand(new MyCommand());
$bot->loadCommand(new \PhpSlackBot\Command\TranslateCommand());
/*$simsimi = new \PhpSlackBot\Command\SimsimiCommand('kr');
$bot->loadCatchAllCommand($simsimi);*/

$bot->loadCatchAllCommand(new EchoCommand());

$bot->loadInternalCommands(); // This loads example commands

$bot->loadPushNotifier(function () {
    return [
        'channel' => '#bot_test',
        'username' => '@jychoi03',
        'message' => "Test First Message."
    ];
});

/*$bot->loadPushNotifier(function () {
    return [
        'channel' => '#bot_test',
        'username' => null,
        'message' => "Current timestamp is: " . time()
    ];
}, 3);*/

$bot->run();

