<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/
namespace baidu\ocr;


class Ocr {

    protected $client = null;

    /**
     * @var bool 是否检测图像朝向，默认不检测，即：false。朝向是指输入图像是正常方向、逆时针旋转90/180/270度。可选值包括:
     */
    protected $detect_direction = false;


    /**
     * @var string 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     */
    protected $image = null;

    /**
     * 简易结果
     * @var bool
     */
    protected $simple_result = true;

    /**
     * 设置简易结果
     * @param bool $simple_result
     */
    public function setSimpleResult(bool $simple_result)
    {
        $this->simple_result = $simple_result;
    }


    public function __construct($config = [])
    {
        $this->client = new AipOcr($config['app_id'], $config['api_key'], $config['secret_key']);
    }

    /**
     * @param bool $detect_direction
     */
    public function setDetectDirection(bool $detect_direction)
    {
        $this->detect_direction = $detect_direction;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * 身份证识别
     * @param $idCardSide // front：身份证含照片的一面；back：身份证带国徽的一面
     * @param bool $detect_risk // 是否开启身份证风险类型(身份证复印件、临时身份证、身份证翻拍、修改过的身份证)功能，默认不开启，即：false。可选值:true-开启；false-不开启
     * @return array
     */
    public function idcard($idCardSide, $detect_risk = false)
    {
        $options = [
            'detect_direction' => $this->detect_direction,
            'detect_risk'      => $detect_risk
        ];
        if ($this->simple_result){
            return $this->formatSimpleResult('idcard',$this->client->idcard($this->image, $idCardSide, $options));
        }
        return $this->client->idcard($this->image, $idCardSide, $options);
    }

    /**
     * 调用银行卡识别
     * @return array
     */
    public function bankcard()
    {
        if ($this->simple_result){
            return $this->formatSimpleResult('bankcard',$this->client->bankcard($this->image));
        }
        return $this->client->bankcard($this->image);
    }

    /**
     * 驾驶证识别
     * @return array
     */
    public function drivingLicense()
    {
        $options = [
            'detect_direction' => var_export($this->detect_direction, true)
        ];
        if ($this->simple_result){
            return $this->formatSimpleResult('drivingLicense',$this->client->drivingLicense($this->image, $options));
        }
        return $this->client->drivingLicense($this->image, $options);
    }

    /**
     * 行驶证识别
     * @param string $vehicle_license_side // front：识别行驶证主页  back：识别行驶证副页
     * @param bool $unified // false：不进行归一化处理  true：对输出字段进行归一化处理，将新/老版行驶证的“注册登记日期/注册日期”统一为”注册日期“进行输出
     * @return array
     */
    public function vehicleLicense($vehicle_license_side = 'front', $unified = false)
    {
        $options = [
            'detect_direction'     => var_export($this->detect_direction, true) ,
            'vehicle_license_side' => $vehicle_license_side,
            'unified'              => var_export($unified,true)
        ];
        if ($this->simple_result){
            return $this->formatSimpleResult('vehicleLicense',$this->client->vehicleLicense($this->image, $options));
        }
        return $this->client->vehicleLicense($this->image, $options);
    }

    /**
     * 车牌号识别
     * @return array
     */
    public function licensePlate($multi_detect = false)
    {
        $options = [
            'multi_detect'     => var_export($multi_detect, true)
        ];
        if ($this->simple_result){
            return $this->formatSimpleResult('licensePlate',$this->client->licensePlate($this->image, $options));
        }
        return $this->client->licensePlate($this->image, $options);
    }

    /**
     * 营业执照识别
     * @return array
     */
    public function businessLicense()
    {
        if ($this->simple_result){
            return $this->formatSimpleResult('businessLicense',$this->client->businessLicense($this->image));
        }
        return $this->client->businessLicense($this->image);
    }

    /**
     * 格式化简易结果
     * @param $action
     * @param $result
     * @return array|mixed
     */
    public function formatSimpleResult($action, $result)
    {
        if (isset($result['error_code'])){
            return $result;
        }
        $resData = [];
        switch ($action) {
            case 'idcard':
            case 'drivingLicense':
            case 'vehicleLicense':
            case 'businessLicense':
                foreach ($result['words_result'] as $k=>$v)
                {
                    $resData[$k] = $v['words'];
                }
                break;
            case 'bankcard':
                $resData = $result['result'];
                break;
            case 'licensePlate':
                $resData['number'] = $result['words_result']['number'];
                break;
            default:
                return $result;
        }
        return $resData;
    }

}