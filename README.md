# 百度ocr识别sdk php的composer包
此包适用于各类支持composer的php框架

## 安装
```
composer require kylin987/baidu-ocr
```

## 使用
使用与官方文档一致

```
//本地图片识别
$client = new baidu\ocr\AipOcr(APP_ID, API_KEY, SECRET_KEY);
$image = file_get_contents(__DIR__ . '/test.png'); // 图片地址 (远程地址https可用这个)
$result = $client->basicGeneral($image);
```

```
//远程图片识别
$client = new baidu\ocr\AipOcr(APP_ID, API_KEY, SECRET_KEY);
$url = 'http://test.com'; // 只支持http地址(不支持https)图片地址 https可以用楼上方法
$result = $client->basicGeneral($url);
```
增加简单识别，并对结果进行简单处理
目前仅支持以下几种,后期可能再增加
```
1、idcard  身份证识别
2、bankcard 银行卡识别
3、drivingLicense 驾驶证识别
4、vehicleLicense 行驶证识别
5、licensePlate 车牌号识别
6、businessLicense 营业执照识别
```
使用方法
```
$config = [
    'app_id'  => 'app_id',
    'api_key' => 'api_key',
    'secret_key'=> 'secret_key'
];
$client = new \baidu\ocr\Ocr($config);
$image = file_get_contents($imgUrl);
$client->setSimpleResult(true);     //输出简易信息
$client->setImage($image);
$result = $client->idcard('front');
```
