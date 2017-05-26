<?php

class Debug {

    private $messages;

    CONST ERROR = 'error';
    CONST INFO = 'info';

    private function __construct() {}

    public static function getInstanse() {

		static $object;
		if (! isset($object)) {
			$object = new Debug();
		}
		return $object;
    }

    public static function message($message, $level = self::INFO) {

        if (is_array($message)) {
            ob_start();
            print_r($message);
            $message = sprintf("<pre>%s</pre>", ob_get_contents());
            ob_end_clean();
        }
        $debug = self::getInstanse();
        $ind = count($debug->messages) - 1;
        $debug->messages[$ind]['text'] = $message;
        $debug->messages[$ind]['level'] = $level;
    }

    public static function getMessages() {
        $debug = self::getInstanse();
        return $debug->messages;
    }

}