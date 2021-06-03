<?php 

    class GetData {

        //物件区分ごとにタグを生成するメソッド（背景色あり、丸い）
        public function getTagDark($value, $tag_arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['estate_kbn']) {
                    case 0:
                        $tag_arr[] = "<el-tag style='border-radius: 15px;' effect='dark' type='success'>土地</el-tag>";
                    break;
                    case 1:
                        $tag_arr[] = "<el-tag style='border-radius: 15px;' effect='dark' type='info'>事務所</el-tag>";
                    break;
                    case 2:
                        $tag_arr[] = "<el-tag style='border-radius: 15px;' effect='dark' type='warning'>マンション</el-tag>";
                    break;
                    case 3:
                        $tag_arr[] = "<el-tag style='border-radius: 15px;' effect='dark'>中古戸建</el-tag>";
                    break;
                    case 4:
                        $tag_arr[] = "<el-tag style='border-radius: 15px;' effect='dark' type='danger'>新築戸建</el-tag>";
                    break;
                }
            }
            return $tag_arr;
        }

        //物件区分ごとにタグを生成するメソッド（背景色なし、四角）
        public function getTagPlain($value, $tag_arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['estate_kbn']) {
                    case 0:
                        $tag_arr[] = "<el-tag style='border-radius: 0px;' effect='plain' type='success'>土地</el-tag>";
                    break;
                    case 1:
                        $tag_arr[] = "<el-tag style='border-radius: 0px;' effect='plain' type='info'>事務所</el-tag>";
                    break;
                    case 2:
                        $tag_arr[] = "<el-tag style='border-radius: 0px;' effect='plain' type='warning'>マンション</el-tag>";
                    break;
                    case 3:
                        $tag_arr[] = "<el-tag style='border-radius: 0px;' effect='plain'>中古戸建</el-tag>";
                    break;
                    case 4:
                        $tag_arr[] = "<el-tag style='border-radius: 0px;' effect='plain' type='danger'>新築戸建</el-tag>";
                    break;
                }
            }
            return $tag_arr;
        }

        //5  物件区分
        public function getEstateKbn($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['estate_kbn']) {
                    case 0:
                        $value[$i]['estate_kbn'] = "土地";
                    break;
                    case 1:
                        $value[$i]['estate_kbn'] = "事務所";
                    break;
                    case 2:
                        $value[$i]['estate_kbn'] = "マンション";
                    break;
                    case 3:
                        $value[$i]['estate_kbn'] = "中古戸建";
                    break;
                    case 4:
                        $value[$i]['estate_kbn'] = "新築戸建";
                    break;
                }
            }
            return $value;
        }

        //6  所在市区分
        public function getCityKbn($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['city_kbn']) {
                    case 0:
                        $value[$i]['city_kbn'] = "船橋市";
                    break;
                    case 1:
                        $value[$i]['city_kbn'] = "鎌ヶ谷市";
                    break;
                    case 2:
                        $value[$i]['city_kbn'] = "市川市";
                    break;
                    case 3:
                        $value[$i]['city_kbn'] = "白井市";
                    break;
                    case 4:
                        $value[$i]['city_kbn'] = "松戸市";
                    break;
                }
            }
            return $value;
        }

        //8  管理費
        public function getManagementFee($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['management_fee']) {
                    case null:
                        $value[$i]['management_fee'] = "-";
                        $arr[] = "管理費なし";
                    break;
                    default:
                        $value[$i]['management_fee'] = number_format($value[$i]['management_fee']) . "円";
                }
            }
            return array($value, $arr);
        }

        //9  共益費
        public function getCondoFee($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['condo_fee']) {
                    case null:
                        $value[$i]['condo_fee'] = "-";
                        $arr[] = "共益費なし";
                    break;
                    default:
                        $value[$i]['condo_fee'] = number_format($value[$i]['condo_fee']) . "円";
                }
            }
            return array($value, $arr);
        }

        //10 敷金
        public function getSecurityDep($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['security_dep']) {
                    case null:
                        $value[$i]['security_dep'] = "-";
                        $arr[] = "敷金なし";
                    break;
                    default:
                        $value[$i]['security_dep'] = number_format($value[$i]['security_dep']) . "円";
                }
            }
            return array($value, $arr);
        }

        //11 礼金
        public function getKeyMoney($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['key_money']) {
                    case null:
                        $value[$i]['key_money'] = "-";
                        $arr[] = "礼金なし";
                    break;
                    default:
                        $value[$i]['key_money'] = number_format($value[$i]['key_money']) . "円";
                }
            }
            return array($value, $arr);
        }

        //12 築年数
        public function getAge($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['age']) {
                    default:
                        if($value[$i]['estate_kbn'] == '土地'){
                            $value[$i]['age'] = '-';
                        } else {
                            $value[$i]['age'] = $value[$i]['age'] . "年";
                        }
                }
            }
            return $value;
        }

        //13 入居可能日
        public function getRoomAvailableDay($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['room_available_day']) {
                    case null:
                        $value[$i]['room_available_day'] = '-';
                    break;
                    default:
                        $time_value = $value[$i]['room_available_day'];
                        $value[$i]['room_available_day'] = date('Y年m月',strtotime($time_value));
                }
            }
            return $value;
        }

        //16 取引態様区分
        public function getConditionsKbn($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['conditions_kbn']) {
                    case 0:
                        $value[$i]['conditions_kbn'] = "その他";
                    break;
                    case 1:
                        $value[$i]['conditions_kbn'] = "貸主";
                    break;
                    case 2:
                        $value[$i]['conditions_kbn'] = "代理";
                    break;
                    case 3:
                        $value[$i]['conditions_kbn'] = "仲介（媒介）";
                    break;
                }
            }
            return $value;
        }

        //17 間取り（部屋数）
        public function getFloorPlanNum($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['floor_plan_num']) {
                    case null:
                        $value[$i]['floor_plan_num'] = "-";
                    break;
                    default:
                        //何もしない;
                }
            }
            return $value;
        }

        //18 間取り（タイプ）
        public function getFloorPlanKbn($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['floor_plan_kbn']) {
                    case 0:
                        $value[$i]['floor_plan_kbn'] = "";
                    break;
                    case 1:
                        $value[$i]['floor_plan_kbn'] = "R";
                    break;
                    case 2:
                        $value[$i]['floor_plan_kbn'] = "K";
                    break;
                    case 3:
                        $value[$i]['floor_plan_kbn'] = "DK";
                    break;
                    case 4:
                        $value[$i]['floor_plan_kbn'] = "LDK";
                    break;
                }
            }
            return $value;
        }

        //19 土地面積
        public function getLandArea($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['land_area']) {
                    case null:
                        $value[$i]['land_area'] = "-";
                    break;
                    default:
                        $value[$i]['land_area'] = $value[$i]['land_area'] . "m2公簿";
                }
            }
            return $value;
        }
        
        //20 建物面積（占有面積）
        public function getBuildingArea($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['building_area']) {
                    case null:
                        $value[$i]['building_area'] = "-";
                    break;
                    default:
                        $value[$i]['building_area'] = $value[$i]['building_area'] . "m2公簿";
                }
            }
            return $value;
        }


        //21 駐車場フラグ
        public function getParkingFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['parking_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "駐車場あり";
                    break;
                }
            }
            return array($value, $arr);
        }

        //22 駐車場月額
        public function getParkingFee($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['parking_fee']) {
                    case null:
                        $value[$i]['parking_fee'] = "-";
                    break;
                    default:
                    $value[$i]['parking_fee'] = number_format($value[$i]['parking_fee']) . "円";
                    break;
                }
            }
            return $value;
        }

        //23 駐輪場フラグ
        public function getBicycleFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['bicycle_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "駐輪場あり";
                    break;
                }
            }
            return array($value, $arr);
        }

        //24 駐輪場月額
        public function getBicycleFee($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['bicycle_fee']) {
                    case null:
                        $value[$i]['bicycle_fee'] = "-";
                    break;
                    default:
                    $value[$i]['bicycle_fee'] = number_format($value[$i]['bicycle_fee']) . "円";
                    break;
                }
            }
            return $value;
        }

        //25 バス・トイレ区分（バス・トイレ共用、ユニットバス、バス・トイレ別）
        public function getBathSeparatedKbn($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['bath_separated_kbn']) {
                    case 0:
                        $arr[] = "バス・トイレ共用";
                    break;
                    case 1:
                        $arr[] = "ユニットバス";
                    break;
                    case 2:
                        $arr[] = "バス・トイレ別";
                    break;
                }
            }
            return array($value, $arr);
        }

        //26 オートロックフラグ
        public function getAutolockFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['autolock_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "オートロック";
                    break;
                }
            }
            return array($value, $arr);
        }

        //27 ペット区分
        public function getPetKbn($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['pet_kbn']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "ペット応相談";
                    break;
                    case 2:
                        $arr[] = "ペット可";
                    break;
                }
            }
            return array($value, $arr);
        }
        
        //29 インターネット無料フラグ
        public function getInternetfreeFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['internetfree_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "インターネット無料";
                    break;
                }
            }
            return array($value, $arr);
        }

        //30 独立洗面台フラグ
        public function getDevidedsinkFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['devidedsink_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "独立洗面台";
                    break;
                }
            }
            return array($value, $arr);
        }

        //31 室内洗濯機置き場フラグ
        public function getInlandryFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['inlandry_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "室内洗濯機置き場";
                    break;
                }
            }
            return array($value, $arr);
        }

        //32 フリーレントフラグ
        public function getFreelentFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['freelent_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "フリーレント";
                    break;
                }
            }
            return array($value, $arr);
        }

        //33 リフォーム済みフラグ
        public function getReformedFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['reformed_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "リフォーム済み";
                    break;
                }
            }
            return array($value, $arr);
        }

        //34 リノベーションフラグ
        public function getRenovationFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['renovation_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "リノベーション物件";
                    break;
                }
            }
            return array($value, $arr);
        }

        //35 家電付きフラグ
        public function getApplianceFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['appliance_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "家電付き";
                    break;
                }
            }
            return array($value, $arr);
        }

        //36 保証人フラグ
        public function getGuarantorFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['guarantor_flg']) {
                    case 0:
                        $value[$i]['guarantor_flg'] = "-";
                        $arr[] = "保証人不要";
                    break;
                    case 1:
                        $value[$i]['guarantor_flg'] = "必要";
                    break;
                }
            }
            return array($value, $arr);
        }

        //37 ルームシェアフラグ
        public function getShareFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['share_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "ルームシェア物件";
                    break;
                }
            }
            return array($value, $arr);
        }

        //38 デザイナーズフラグ
        public function getDesignersFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['designers_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "デザイナーズ物件";
                    break;
                }
            }
            return array($value, $arr);
        }

        //39 初期費用10万円以下フラグ
        public function getPreUnderHtFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['pre_under_ht_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "初期費用10万円以下";
                    break;
                }
            }
            return array($value, $arr);
        }

        //40 階数
        public function getStoryNum($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['story_num']) {
                    case null:
                        $value[$i]['story_num'] = "-";
                    break;
                    default:
                    $value[$i]['story_num'] = $value[$i]['story_num'] . "階建";
                }
            }
            return $value;
        }

        //41 部屋位置
        public function getFloorNum($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['floor_num']) {
                    case null:
                        $value[$i]['floor_num'] = "";
                    break;
                    default:
                    $value[$i]['floor_num'] = $value[$i]['floor_num'] . "階";
                }
            }
            return $value;
        }

        //42 エアコンフラグ
        public function getAirconFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['aircon_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "エアコン";
                    break;
                }
            }
            return array($value, $arr);
        }

        //43 床暖房フラグ
        public function getFloorheatFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['floorheat_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "床暖房";
                    break;
                }
            }
            return array($value, $arr);
        }

        //44 TVモニター付インターホンフラグ
        public function getMonitorInterphoneFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['monitor_interphone_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "TVモニター付インターホン";
                    break;
                }
            }
            return array($value, $arr);
        }

        //45 フローリングフラグ
        public function getFlooringFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['flooring_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "フローリング";
                    break;
                }
            }
            return array($value, $arr);
        }

        //46 エレベーターフラグ
        public function getElevatorFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['elevator_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "エレベーター";
                    break;
                }
            }
            return array($value, $arr);
        }

        //47 防犯カメラフラグ
        public function getSecurityCameraFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['security_camera_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "防犯カメラ";
                    break;
                }
            }
            return array($value, $arr);
        }

        //48 宅配ボックスフラグ
        public function getDeliverBoxFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['deliver_box_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "宅配ボックス";
                    break;
                }
            }
            return array($value, $arr);
        }

        //49 カウンターキッチンフラグ
        public function getCounterKitchenFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['counter_kitchen_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "カウンターキッチン";
                    break;
                }
            }
            return array($value, $arr);
        }

        //50 IHコンロ
        public function getElectricStoveFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['electric_stove_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "IHコンロ";
                    break;
                }
            }
            return array($value, $arr);
        }

        //51 コンロ2口以上フラグ
        public function getMultiStoveFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['multi_stove_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "コンロ2口以上";
                    break;
                }
            }
            return array($value, $arr);
        }

        //52 システムキッチンフラグ
        public function getSystemKitchenFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['system_kitchen_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "システムキッチン";
                    break;
                }
            }
            return array($value, $arr);
        }

        //53 ガスコンロ対応フラグ
        public function getGasStoveAvailableFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['gas_stove_available_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "ガスコンロ対応";
                    break;
                }
            }
            return array($value, $arr);
        }

        //54 シャワートイレフラグ
        public function getShowerToiletFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['shower_toilet_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "シャワートイレ";
                    break;
                }
            }
            return array($value, $arr);
        }

        //55 追い焚きフラグ
        public function getReheatFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['reheat_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "追焚機能浴室";
                    break;
                }
            }
            return array($value, $arr);
        }

        //56 浴室乾燥機フラグ
        public function getBathRoomDryFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['bathroom_dry_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "浴室乾燥機";
                    break;
                }
            }
            return array($value, $arr);
        }

        //57 シャンプードレッサーフラグ
        public function getShampooDresserFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['shampoo_dresser_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "シャンプードレッサー";
                    break;
                }
            }
            return array($value, $arr);
        }

        //58 角部屋フラグ
        public function getCornerRoomFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['corner_room_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "角部屋";
                    break;
                }
            }
            return array($value, $arr);
        }

        //59 南向きフラグ
        public function getRoomTosouthFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['room_tosouth_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "南向き";
                    break;
                }
            }
            return array($value, $arr);
        }

        //60 メゾネットフラグ
        public function getMaisonetteFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['maisonette_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "メゾネット";
                    break;
                }
            }
            return array($value, $arr);
        }

        //61 ロフトフラグ
        public function getLoftFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['loft_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "ロフト";
                    break;
                }
            }
            return array($value, $arr);
        }

        //62 CATVフラグ
        public function getCatvFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['catv_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "CATV";
                    break;
                }
            }
            return array($value, $arr);
        }

        //63 CSアンテナフラグ
        public function getCsAntennaFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['cs_antenna_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "CSアンテナ";
                    break;
                }
            }
            return array($value, $arr);
        }

        //64 BSアンテナフラグ
        public function getBsAntennaFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['bs_antenna_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "BSアンテナ";
                    break;
                }
            }
            return array($value, $arr);
        }

        //65 女性オンリーフラグ
        public function getWomenOnlyFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['women_only_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "女性オンリー";
                    break;
                }
            }
            return array($value, $arr);
        }

        //66 高齢者相談フラグ
        public function getElderOkFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['elder_ok_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "高齢者相談";
                    break;
                }
            }
            return array($value, $arr);
        }

        //67 新婚向けフラグ
        public function getNewlywedsLikeFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['newlyweds_like_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "新婚向け";
                    break;
                }
            }
            return array($value, $arr);
        }

        //68 即入居可フラグ
        public function getImmediateMovavleFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['immediate_movavle_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "即入居可";
                    break;
                }
            }
            return array($value, $arr);
        }

        //69 仲介手数料ゼロフラグ
        public function getBrokerageFreeFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['brokerage_free_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "仲介手数料ゼロ";
                    break;
                }
            }
            return array($value, $arr);
        }

        //70 管理物件フラグ
        public function getManagedPropertyFlg($value, $arr) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['managed_property_flg']) {
                    case 0:
                        //何もしない
                    break;
                    case 1:
                        $arr[] = "管理物件";
                    break;
                }
            }
            return array($value, $arr);
        }

        //74 更新日時（最終更新日時）
        public function getUpdateDatetime($value) {
            for($i = 0; $i < count($value); $i++) {
                switch($value[$i]['update_datetime']) {
                    default:
                        $time_value = $value[$i]['update_datetime'];
                        $value[$i]['update_datetime'] = date('Y/m/d',strtotime($time_value));
                }
            }
            return $value;
        }

    }

    //クラス全てを通すための関数

    function useClass($value, $arr) {

        $get_data = new GetData();

        //5  物件区分
        $value = $get_data->getEstateKbn($value);

        //6  所在市区分
        $value = $get_data->getCityKbn($value);

        //8  管理費
        list($value, $arr) = $get_data->getManagementFee($value, $arr);

        //9  共益費
        list($value, $arr) = $get_data->getCondoFee($value, $arr);

        //10 敷金
        list($value, $arr) = $get_data->getSecurityDep($value, $arr);

        //11 礼金
        list($value, $arr) = $get_data->getKeyMoney($value, $arr);

        //12 築年数
        $value = $get_data->getAge($value);

        //13 入居可能日
        $value = $get_data->getRoomAvailableDay($value);

        //16 取引態様区分
        $value = $get_data->getConditionsKbn($value);

        //17 間取り（部屋数）
        $value = $get_data->getFloorPlanNum($value);

        //18 間取り（タイプ）
        $value = $get_data->getFloorPlanKbn($value);

        //19 土地面積
        $value = $get_data->getLandArea($value);

        //20 建物面積（占有面積）
        $value = $get_data->getBuildingArea($value);

        //21 駐車場フラグ
        list($value, $arr) = $get_data->getParkingFlg($value, $arr);

        //22 駐車場月額
        $value = $get_data->getParkingFee($value);

        //23 駐輪場フラグ
        list($value, $arr) = $get_data->getBicycleFlg($value, $arr);
        
        //24 駐輪場月額
        $value = $get_data->getBicycleFee($value);
        
        //25 バス・トイレ区分（バス・トイレ共用、ユニットバス、バス・トイレ別）
        list($value, $arr) = $get_data->getBathSeparatedKbn($value, $arr);
        
        //26 オートロックフラグ
        list($value, $arr) = $get_data->getAutolockFlg($value, $arr);
        
        //27 ペット区分
        list($value, $arr) = $get_data->getPetKbn($value, $arr);
        
        //29 インターネット無料フラグ
        list($value, $arr) = $get_data->getInternetfreeFlg($value, $arr);
        
        //30 独立洗面台フラグ
        list($value, $arr) = $get_data->getDevidedsinkFlg($value, $arr);
        
        //31 室内洗濯機置き場フラグ
        list($value, $arr) = $get_data->getInlandryFlg($value, $arr);
        
        //32 フリーレントフラグ
        list($value, $arr) = $get_data->getFreelentFlg($value, $arr);
        
        //33 リフォーム済みフラグ
        list($value, $arr) = $get_data->getReformedFlg($value, $arr);
        
        //34 リノベーションフラグ
        list($value, $arr) = $get_data->getRenovationFlg($value, $arr);
        
        //35 家電付きフラグ
        list($value, $arr) = $get_data->getApplianceFlg($value, $arr);
        
        //36 保証人フラグ
        list($value, $arr) = $get_data->getGuarantorFlg($value, $arr);
        
        //37 ルームシェアフラグ
        list($value, $arr) = $get_data->getShareFlg($value, $arr);
        
        //38 デザイナーズフラグ
        list($value, $arr) = $get_data->getDesignersFlg($value, $arr);
        
        //39 初期費用10万円以下フラグ
        list($value, $arr) = $get_data->getPreUnderHtFlg($value, $arr);

        //40 階数
        $value = $get_data->getStoryNum($value);

        //41 部屋位置
        $value = $get_data->getFloorNum($value);

        //42 エアコンフラグ
        list($value, $arr) = $get_data->getAirconFlg($value, $arr);

        //43 床暖房フラグ
        list($value, $arr) = $get_data->getFloorheatFlg($value, $arr);

        //44 TVモニター付インターホンフラグ
        list($value, $arr) = $get_data->getMonitorInterphoneFlg($value, $arr);

        //45 フローリングフラグ
        list($value, $arr) = $get_data->getFlooringFlg($value, $arr);

        //46 エレベーターフラグ
        list($value, $arr) = $get_data->getElevatorFlg($value, $arr);

        //47 防犯カメラフラグ
        list($value, $arr) = $get_data->getSecurityCameraFlg($value, $arr);

        //48 宅配ボックスフラグ
        list($value, $arr) = $get_data->getDeliverBoxFlg($value, $arr);

        //49 カウンターキッチンフラグ
        list($value, $arr) = $get_data->getCounterKitchenFlg($value, $arr);

        //50 IHコンロ
        list($value, $arr) = $get_data->getElectricStoveFlg($value, $arr);

        //51 コンロ2口以上フラグ
        list($value, $arr) = $get_data->getMultiStoveFlg($value, $arr);

        //52 システムキッチンフラグ
        list($value, $arr) = $get_data->getSystemKitchenFlg($value, $arr);

        //53 ガスコンロ対応フラグ
        list($value, $arr) = $get_data->getGasStoveAvailableFlg($value, $arr);

        //54 シャワートイレフラグ
        list($value, $arr) = $get_data->getShowerToiletFlg($value, $arr);

        //55 追い焚きフラグ
        list($value, $arr) = $get_data->getReheatFlg($value, $arr);

        //56 浴室乾燥機フラグ
        list($value, $arr) = $get_data->getBathRoomDryFlg($value, $arr);

        //57 シャンプードレッサーフラグ
        list($value, $arr) = $get_data->getShampooDresserFlg($value, $arr);

        //58 角部屋フラグ
        list($value, $arr) = $get_data->getCornerRoomFlg($value, $arr);

        //59 南向きフラグ
        list($value, $arr) = $get_data->getRoomTosouthFlg($value, $arr);

        //60 メゾネットフラグ
        list($value, $arr) = $get_data->getMaisonetteFlg($value, $arr);

        //61 ロフトフラグ
        list($value, $arr) = $get_data->getLoftFlg($value, $arr);

        //62 CATVフラグ
        list($value, $arr) = $get_data->getCatvFlg($value, $arr);

        //63 CSアンテナフラグ
        list($value, $arr) = $get_data->getCsAntennaFlg($value, $arr);

        //64 BSアンテナフラグ
        list($value, $arr) = $get_data->getBsAntennaFlg($value, $arr);

        //65 女性オンリーフラグ
        list($value, $arr) = $get_data->getWomenOnlyFlg($value, $arr);

        //66 高齢者相談フラグ
        list($value, $arr) = $get_data->getElderOkFlg($value, $arr);

        //67 新婚向けフラグ
        list($value, $arr) = $get_data->getNewlywedsLikeFlg($value, $arr);

        //68 即入居可フラグ
        list($value, $arr) = $get_data->getImmediateMovavleFlg($value, $arr);

        //69 仲介手数料ゼロフラグ
        list($value, $arr) = $get_data->getBrokerageFreeFlg($value, $arr);

        //70 管理物件フラグ
        list($value, $arr) = $get_data->getManagedPropertyFlg($value, $arr);

        //74 更新日時（最終更新日時）
        $value = $get_data->getUpdateDatetime($value);


        //最終的に呼び出し元に返す配列（連想配列）、配列
        return array($value, $arr);
    }

