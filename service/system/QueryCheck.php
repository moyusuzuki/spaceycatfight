<?php

/**
 * 物件一覧画面に表示する物件情報を取得するためのクラス
 */
class QueryCheck 
{   
    //seedは敷金のあるなしフラグ、keeyは礼金のあるなしフラグ floor_planは1,1などのようにして入るnumとplanの情報混ぜて
    //クエリ作成時に使用するための連想配列（keyは情報が渡ってくるときの名前、valueはその際に使用するテーブルのカラム名
    static protected $query_name_arr = array(
            'freeword' => ['estate_name', 'description'],  //or検索
            'puse' => 'purchase_flg',
            'este' => array('estate_kbn'),  //or
            'city' => array('city_kbn'),  //or
            'feed' => 'fee_included_flg',
            'seed' => 'security_dep',  //敷金なしかどうか なしのとき「1」が入る
            'keey' => 'key_money',  //礼金なしかどうか なしの時「1」が入る
            'floor_plan' => ['floor_plan_num', 'floor_plan_kbn'],  //or
            'age' => 'age',
            'pang' => 'parking_flg',
            'bile' => 'bicycle_flg',
            'baed' => 'bath_separated_kbn',
            'auck' => 'autolock_flg',
            'pet' => 'pet_kbn',
            'fole' => 'forsale_flg',
            'inee' => 'internetfree_flg',
            'denk' => 'devidedsink_flg',
            'inry' => 'inlandry_flg',
            'frnt' => 'freelent_flg',
            'reed' => 'reformed_flg',
            'reon' => 'renovation_flg',
            'apce' => 'appliance_flg',
            'guor' => 'guarantor_flg',
            'shre' => 'share_flg',
            'ders' => 'designers_flg',
            'prht' => 'pre_under_ht_flg',
            'aion' => 'aircon',
            'flat' => 'floorheat_flg',
            'mone' => 'monitor_interphone_flg',
            'flng' => 'flooring_flg',
            'elor' => 'elevator_flg',
            'sera' => 'security_camera_flg',
            'deox' => 'deliver_box_flg',
            'coen' => 'counter_kitchen_flg',
            'elve' => 'electric_stove_flg',
            'muve' => 'multi_stove_flg',
            'syen' => 'system_kitchen_flg',
            'gale' => 'gas_stove_available_flg',
            'shet' => 'shower_toilet_flg',
            'reat' => 'reheat_flg',
            'bary' => 'bathroom_dry_flg',
            'sher' => 'shampoo_dresser_flg',
            'coom' => 'corner_room_flg',
            'roth' => 'room_tosouth_flg',
            'mate' => 'maisonette_flg',
            'loft' => 'loft_flg',
            'catv' => 'catv_flg',
            'csna' => 'cs_antenna_flg',
            'bsna' => 'bs_antenna_flg',
            'woly' => 'women_only_flg',
            'elok' => 'elder_ok_flg',
            'neke' => 'newlyweds_like_flg',
            'imle' => 'immediate_movavle_flg',
            'bree' => 'brokerage_free_flg',
            'maty' => 'managed_property_flg',
            'piup' => 'pickup_flg',
            'estate_priceLow' => 'estate_price',
            'estate_priceHigh' => 'estate_price',
            'land_areaLow' => 'land_area',
            'land_areaHigh' => 'land_area',
            'building_areaLow' => 'building_area',
            'building_areaHigh' => 'building_area',
            'age' => 'age',
            'transport_time' => 'transport_time',
        );

    static private $is_special_arr_info = 0;  //$special_arrのフラグが一つでも立ったら立つフラグ
    static private $special_arr = array(  //<=と>=を使うのはクエリ文が少し特殊なので通常の配列とは分ける クエリ文を作るときは「カラム名 演算時 値」の並びにする前提の演算子です
        'age' => array('special_arr_value' => null, 'flg' => 0, 'operator' => '<='),
        'transport_time' => array('special_arr_value' => null, 'flg' => 0, 'operator' => '<='),
        'estate_priceLow' => array('special_arr_value' => null, 'flg' => 0, 'operator' => '>='),
        'estate_priceHigh' => array('special_arr_value' => null, 'flg' => 0, 'operator' => '<='),
        'land_areaLow' => array('special_arr_value' => null, 'flg' => 0, 'operator' => '>='),
        'land_areaHigh' => array('special_arr_value' => null, 'flg' => 0, 'operator' => '<='),
        'building_areaLow' => array('special_arr_value' => null, 'flg' => 0, 'operator' => '>='),
        'building_areaHigh' => array('special_arr_value' => null, 'flg' => 0, 'operator' => '<=')
    );

    static private $is_floor_info = 0;  //部屋の階数についての情報があった場合に立つフラグ
    static private $position_column_use = array('story_num', 'floor_num');
    static private $floor_num_a = 0;  //フラグ用のプロパティ いずれも選択時に1が入る 1 => 1階
    static private $floor_num_b = 0;  //フラグ用のプロパティ いずれも選択時に1が入る 1 => 2階以上
    static private $floor_num_c = 0;  //フラグ用のプロパティ いずれも選択時に1が入る 1 => 最上階
    static private $one_and_top = 0;  //1階と最上階が選択されたときに立つフラグ
        
    //初期状態のクエリ文。t_estateとt_estate_detailのテーブルを結合、条件を「表示されているもの」「削除されていないもの」で絞ったもの。
    static private $sql = "SELECT t_estate.estate_id FROM t_estate JOIN t_estate_detail ON t_estate.estate_id = t_estate_detail.estate_id WHERE display_flg = 1 AND delete_flg = 0 ";

    //初期状態のORDER BY情報
    static private $order_info = " ORDER BY t_estate_detail.registered_datetime DESC";

    //昇順降順の対象を決定する際に使うプロパティ
    static private $order_info_arr = array(  //$_GET[]の値はなんでもいいのでとりあえず1が入ってくる
        'pickup' => array('column_name' => 'pickup_flg', 'order_kind' => 'DESC'),  
        'high' => array('column_name' => 'estate_price', 'order_kind' => 'DESC'),
        'low' => array('column_name' => 'estate_price', 'order_kind' => 'ASC'),
        'new' => array('column_name' => 'registered_datetime', 'order_kind' => 'DESC')
    );

    //徒歩○分以内の物件が見つからなかった時に立つフラグ これが立っていたら該当する物件情報はないとする
    static private $walk_time_flg = 0;

    //初期状態のlimit情報
    static private $limit = 10;
    static private $limit_info = " LIMIT 10";

    //何ページ目が開かれているか
    static private $is_page = " OFFSET 0";  //デフォルトは1ページ目（$limit_info * $is_pageでoffsetを決めるため0が初期値）

    //コンストラクタでDB接続情報が代入される
    static private $dbh;  

    //最終的に呼び出し元に渡す配列（estate_id）これを表側で使用する
    static private $id_infos = null;  
    static $use_id_infos = array();  //上を加工したもの（こっちを使う）estate_idが[1, 2, 3, 4];のように入る
    static $use_id_count = null;  //上記のプロパティに何個要素があるか入れる（つまり検索に引っかかった物件が何個あるかわかる）

    static $total_house_count = null;  //上記のプロパティに何個要素があるか入れる（つまり検索に引っかかった物件が何個あるかわかる）

    //============================================================================================================================

    /**
     * 
     */
    public function __construct($dbh) {
        self::$dbh = $dbh;
        //foreachで$_GETに入っているもの全て回す $item_key は puse など, $item_value は 3 など
        foreach($_GET as $item_key => $item_value) {
            if(self::$walk_time_flg) {  //もしフラグが立っていたら（この時、該当の物件がこの時点でないということになる
                break;  //ループを終了させる
            }
            $this->andConditions($item_key, $item_value);
        }

        //フラグが立っていたらメソッドを実行
        if(self::$is_floor_info) {
            $this->roomPositionQuery();
        }

        //フラグが立っていたらメソッドを実行 ここで物件情報があるかないかわかる
        if(self::$is_special_arr_info) {
            $this->specialArrQuery();
        }

        //DBから情報を取得する（全部で何個取得できるのかの確認、総数を知りたい）
        $count_stmt = self::$dbh->query(self::$sql);
        $total_house = $count_stmt->fetchall(PDO::FETCH_ASSOC);
        self::$total_house_count = count($total_house);  //物件情報がいくつあるか数える

        //フラグが立っていなかったら実行
        if(!self::$walk_time_flg) {
            //最後におすすめ、新着（登録日）、安い、高い、表示件数（limit）の設定をする
            $this->orderInfoQuery();
            self::$sql .= self::$order_info;
            self::$sql .= self::$limit_info;
            if(isset($_GET['page'])) {
                self::$is_page = " OFFSET " . self::$limit * $_GET['page'];  //表示件数 * pageの値(10とか)がOFFSETの値となる
            }
            self::$sql .= self::$is_page;
    
            //クエリ文の生成が終了となる
            self::$sql .= ";";  

            //DBから情報を取得する
            $stmt = self::$dbh->query(self::$sql);
            self::$id_infos = $stmt->fetchall(PDO::FETCH_ASSOC);

            //連想配列($id_infos)だと使いづらいので配列($use_id_infos)に加工する
            foreach(self::$id_infos as $value) {
                self::$use_id_infos[] = $value['estate_id'];
            }
            self::$use_id_count = count(self::$use_id_infos);  //物件が何件ヒットしたか登録する
        } 

    }


    /**
     * 
     */
    public function andConditions($item_key, $item_value) {

        //limit情報があった場合、プロパティの初期値を変更する
        if($item_key == 'limit') {
            self::$limit = $item_value;
            self::$limit_info = " LIMIT {$item_value}";
            return;
        }
        //order情報があった場合無視する
        if($item_key == 'order') {return;}

        //page情報があった場合無視する
        if($item_key == 'page') {return;}

        //floor_num_○ の情報の時の処理 終わり次第returnする
        switch($item_key) {
            case 'floor_num_a':
                self::$is_floor_info = 1;  //フラグを立てる
                self::$floor_num_a = $item_value;  //1が代入される
                return;
            case 'floor_num_b':
                self::$is_floor_info = 1;  //フラグを立てる
                self::$floor_num_b = $item_value;
                return;
            case 'floor_num_c':
                self::$is_floor_info = 1;  //フラグを立てる
                self::$floor_num_c = $item_value;
                return;
        }

        //self::$special_arr[$item_key]で値が取得できたら処理をする
        if(isset(self::$special_arr[$item_key])) {
            self::$is_special_arr_info = 1;  //フラグを立てる
            self::$special_arr[$item_key]['special_arr_value'] = $item_value;  //該当する配列に送信されたデータをいれる
            self::$special_arr[$item_key]['flg'] = 1;  //あとで処理をするのでフラグを立てる
            return;
        }

        //基本的なクエリ作成処理
        if(!is_array(self::$query_name_arr[$item_key])) {  //$query_name_arrでvalueが配列(array)情報じゃないのときの処理

            self::$sql .= " AND " . self::$query_name_arr[$item_key] . " = " . $item_value;  //通常のAND文作成

        } else {  //$query_name_arrでvalueが配列情報のときの処理（使用するカラムが複数のため処理を分ける）

            switch($item_key) {  //$_GETのkeyの名前（puseとかpiupとか）
                case 'freeword':  //0番目はestate_name, 1はdescriptionが入る
                    $item_value = str_replace('　', ' ', $item_value);  //全角スペースがあったら半角スペースに置き換える
                    
                    $space_item = explode(' ', $item_value);  //上記の処理により、複数単語がある場合全て半角スペースになっているので、半角スペース区切りで配列へ格納
                    
                    $space_item_num = count($space_item);  //forループで何回回すか決めるための変数（継続処理条件に関数を書くと遅くなる）

                    for($i = 0; $i < $space_item_num; $i++) { 
                        if($i == 0) {  //初回のみの処理
                            self::$sql .= " AND ( " ;
                        }

                        if($i == count($space_item) - 1) {  //forループの最後の処理 次もある場合はANDを末尾につけるが、最後であれば付けたくない
                            self::$sql .= self::$query_name_arr[$item_key][0] . " LIKE " . "'%{$space_item[$i]}%'" . " OR " . self::$query_name_arr[$item_key][1] . " LIKE " . "'%{$space_item[$i]}%'";
                        } else {
                            self::$sql .= self::$query_name_arr[$item_key][0] . " LIKE " . "'%{$space_item[$i]}%'" . " OR " . self::$query_name_arr[$item_key][1] . " LIKE " . "'%{$space_item[$i]}%'" . " OR ";
                        }
                    }

                    self::$sql .= " ) ";
                    break;

                case 'floor_plan':  //0番目はfloor_plan_num, 1はfloor_plan_kbnが入る
                    $commas_item = explode(',', $item_value);  //$_GETで送られたfloor_planの値を「,」区切りで配列にいれる 例→[1,1,2,3]のように 部屋数,間取り,部屋数,間取り...のような並び
                    
                    //最初に「(」で囲むための処理
                    self::$sql .= " AND ( ";

                    $commas_item_num = count($commas_item);  //forループで何回回すか決めるための変数（継続処理条件に関数を書くと遅くなる）

                    for($i = 0; $i < $commas_item_num; $i += 2) {  //ひたすら条件を足していく 上記のコメントのように、2つで一つの物件情報のため、2飛ばしで回す
                        if($i == 0) {  //初回のみの処理
                            if($commas_item[$i] == 5) {  //5K以上が選ばれたら
                                self::$sql .= self::$query_name_arr[$item_key][0] . " >= " . $commas_item[$i];
                            } else {
                                self::$sql .= " ( " . self::$query_name_arr[$item_key][0] . " = " . $commas_item[$i] . " AND " . self::$query_name_arr[$item_key][1] . " = " . $commas_item[$i + 1] . " ) ";
                            }
                            
                        } else {
                            if($commas_item[$i] == 5) {  //5K以上が選ばれたら
                                self::$sql .= " OR ( " . self::$query_name_arr[$item_key][0] . " >= " . $commas_item[$i] . " ) ";
                            } else {
                                self::$sql .= " OR ( " . self::$query_name_arr[$item_key][0] . " = " . $commas_item[$i] . " AND " . self::$query_name_arr[$item_key][1] . " = " . $commas_item[$i + 1] . " ) ";
                            }
                        }
                    }

                    //最後に「)」で閉じてあげる
                    self::$sql .= " ) ";
                    break; //これで select ~~~ where ~~~ AND ((floor_plan_num = 1 AND floor_plan_kbn = 2) OR (floor_plan_num = 2 AND floor_plan_kbn = 2)) のようなクエリ文が作れる

                case 'city':
                    $this->commonCreateQuery($item_key, $item_value);
                    break;  //右のようになる AND ( city_kbn = 2 OR city_kbn = 3 OR city_kbn = 4 )

                case 'este':
                    $this->commonCreateQuery($item_key, $item_value);
                    break;  //右のようになる AND ( estate_kbn = 4 OR estate_kbn = 3 )
            }
            
        }

    }

    /**
     * @param arr $item_kye, @param arr $item_value
     * 現段階ではesteとcity情報が入ったときに使われるメソッド（重複した処理のためまとめた）
     */
    public function commonCreateQuery($item_key, $item_value) {
        $commas_item = explode(',', $item_value);  //カンマ区切りにする

        //最初に「(」で囲むための処理
        self::$sql .= " AND ( ";

        $commas_item_num = count($commas_item);

        for($i = 0; $i < $commas_item_num; $i++) {
            if($i == 0) {
                self::$sql .= self::$query_name_arr[$item_key][0] . " = " . $commas_item[$i];
            } else {
                self::$sql .= " OR " . self::$query_name_arr[$item_key][0] . " = " . $commas_item[$i];
            }
        }

        //最後に「)」で閉じてあげる
        self::$sql .= " ) ";
    }
    

    /**
     * 1階、2階以上、最上階のクエリ文作成において、メソッド使用する時の引数を決めるメソッド
     */
    public function roomPositionQuery() {
        if(self::$floor_num_a && self::$floor_num_b && self::$floor_num_c) {  //全部の条件が選択された場合クエリ文は追加しない 0はfalse、1はtrueになる
            return;  //何もしない
        } else if(self::$floor_num_b && self::$floor_num_c) {  //2階以上、最上階どちらかが選択された場合2階以上で検索する
            $this->positionQueryCreate('>', null, self::$position_column_use[1]);  

        } else if(self::$floor_num_a && self::$floor_num_c) {  //1階と最上階が選択されたときはOR検索をする
            $this->positionQueryCreate('=', null, self::$position_column_use[1]);
            self::$one_and_top = 1;  //1階と最上階が選択されたのでここでフラグを立てる（上のメソッド呼び出しより上だと2回同じメソッドが呼ばれてしまう）
            $this->positionQueryCreate('=', self::$position_column_use[0], self::$position_column_use[1]);

        } else if(self::$floor_num_a) {  //1階が選択されたとき
            $this->positionQueryCreate('=', null, self::$position_column_use[1]);  //=, null, floor_numを渡す

        } else if(self::$floor_num_b) {  //2階以上が選択されたとき
            $this->positionQueryCreate('>', null, self::$position_column_use[1]);

        } else if(self::$floor_num_c) {  //最上階が選択されたとき
            $this->positionQueryCreate('=', self::$position_column_use[0], self::$position_column_use[1]);
        }
    }


    /**
     * @param $operator 演算子, @param string or null, @param string
     * 
     */
    public function positionQueryCreate($operator, $story_use, $floor_use) {  //$story_useがnullにならないのは最上階が選択された時だけ
        if(self::$one_and_top) {  //1階と最上階のクエリ文
            self::$sql .= " OR " . $floor_use . " {$operator} " . $story_use;
            //例）AND floor_num = 1 OR floor_num = story_num;
    
        } else if(is_null($story_use)) {  //1階、または2階以上の時のクエリ文
            self::$sql .= " AND " . $floor_use . " {$operator} " . 1 ;
            //例 1階の時）AND floor_num = 1;    例 2階以上の時）AND floor_num > 1;

        } else {  //最上階の時のクエリ文
            self::$sql .= " AND " . $floor_use . " {$operator} " . $story_use;
            //例）AND floor_num = story_num;
        }
    }

    
    /**
     * 
     */
    public function specialArrQuery() {
        foreach(self::$special_arr as $key => $value) {
            if($value['flg'] && $key == 'transport_time') {  //フラグが立っているかつ、transport_timeだったら
                //transport_timeは中間テーブルで複数条件があるため、ここで徒歩○分以内に該当する物件のestate_idを取得する。その上で重複するestate_idは取得しない
                $tt_sql = "SELECT distinct estate_id FROM t_estate_transport WHERE transport_time <= {$value['special_arr_value']} AND transport_delete_flg = 0;";
                $est = self::$dbh->query($tt_sql);
                $estate_ids = $est->fetchall(PDO::FETCH_ASSOC);
                
                if(empty(!$estate_ids)) {  //もしも上記のselectで該当する物件があったら
                    $estate_ids_count = count($estate_ids);
                    self::$sql .= " AND ( ";
                    for($i = 0; $i < $estate_ids_count; $i++) {
                        if($i != 0) {
                            self::$sql .= " OR ";
                        }
                        self::$sql .= "t_estate.estate_id = {$estate_ids[$i]['estate_id']}";
                    }
                    self::$sql .= " ) ";
                } else {  //なかったら
                    self::$walk_time_flg = 1;  //このフラグたった場合、この段階で該当の物件がないことになるので終了
                }
                
            } else if($value['flg']) {  //フラグが立ってたら実行
                self::$sql .= " AND " . self::$query_name_arr[$key] . " {$value['operator']} " . $value['special_arr_value'];
            }
        }
    }


    /**
     * @return string $order_info
     * 物件情報取得時の降順、昇順の対象を決めるメソッド
     */
    public function orderInfoQuery() {
            if(isset($_GET['order'])) {
                self::$order_info =  " ORDER BY t_estate_detail." . self::$order_info_arr[$_GET['order']]['column_name'] . " " . self::$order_info_arr[$_GET['order']]['order_kind'];
            }
    }


}
