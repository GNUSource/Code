<?php
require_once __DIR__ . '/vendor/autoload.php';

use \Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Response
{
    /**
     * 由客户端指定数据的返回格式
     *
     * @param int $code 状态码
     * @param string $message 提示信息
     * @param array $data 返回的数据
     * @return array|mixed|string 格式化后的数组
     */
    public static function show($code, $message = '', $data = [])
    {
        $format = isset($_GET['format']) ? $_GET['format'] : 'json';

        switch ($format) {
            case 'json':
                $response = self::json($code, $message, $data);
                break;
            case 'xml':
                $response = self::xml($code, $message, $data);
                break;
            case 'array':
                $response = print_r($data, true);
                break;
            default:
                $response = 'please specify the format which data will be returned.';
        }

        return $response;
    }

    /**
     * 封装通信数据接口,以json的形式
     *
     * @param int $code 状态码
     * @param string $message 提示消息
     * @param array $data 返回的数据
     * @return array|string json格式的数据
     */
    public static function json($code, $message = '', $data = [])
    {
        if (!is_integer($code)) {
            $e = new BadRequestHttpException('code must be an integer.');
            header("HTTP/1.1 {$e->getStatusCode()}");
            exit($e->getMessage());
        }

        $result =
            [
                'code' => $code,
                'message' => $message,
                'data' => $data
            ];

        $result = json_encode($result);

        return $result;
    }

    /**
     * 封装通信数据接口,以xml的形式
     *
     * @param int $code 状态码
     * @param string $message 提示信息
     * @param array $data 返回的数据
     * @return string xml数据
     */
    public static function xml($code, $message = '', $data = [])
    {
        header('Content-Type:text/xml;charset=utf-8');
        $xml = "<?xml version='1.0' encoding='utf-8'?>\n";
        $xml .= "<root>\n";
        $xml .= "<code>$code</code>\n";
        $xml .= "<message>$message</message>\n";
        $xml .= "<data>\n";
        $xml .= self::buildData($data);
        $xml .= "</data>\n";
        $xml .= "</root>";

        return $xml;
    }

    /**
     * 将数组构造成符合xml格式的形式
     *
     * @param array $data 数组数据
     * @return string xml格式数据
     */
    protected static function buildData($data = [])
    {
        $xml = $attr = '';
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (is_numeric($key)) {
                    $key = "item";
                }

                $xml .= "<{$key}{$attr}>\n";
                $xml .= is_array($value) ? self::buildData($value) : $value;
                $xml .= "</{$key}>\n";
            }
        }

        return $xml;
    }
}


//echo Response::json(200, 'ok!',
//    [
//        'id' => 3,
//        'name' => 'zhangsan',
//        'gender' => 'male'
//    ]
//);
//echo PHP_EOL;
//$data =
//    [
//        [
//            'id' => 34,
//            'name' => 'hkf',
//            'gender' => 'male',
//            'skills' =>
//                [
//                    'language' => 'php', 'mysql', 'linux'
//                ]
//        ],
//        [
//            'id' => 43,
//            'name' => 'hkf2',
//            'gender' => 'male',
//            'skills' =>
//                [
//                    'language' => 'php', 'db' => 'mysql', 'linux'
//                ]
//        ],
//        'test' => 'this is test'
//
//    ];
//echo Response::xml(200, 'ok', $data);

echo Response::show(200, 'ok', [
    'id' => 3,
    'name' => 'zhangsan',
    'gender' => 'male'
]);

