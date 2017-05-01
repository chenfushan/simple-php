<?php
/**
 * ThinkPHP 控制器基类 抽象类
 */
abstract class Controller {

    /**
     * 视图实例对象
     * @var view
     * @access protected
     */    
    protected $view     =  null;

    /**
     * 控制器参数
     * @var config
     * @access protected
     */      
    protected $config   =   array();

   /**
     * 架构函数 取得模板对象实例
     * @access public
     */
    public function __construct() {
    }



        /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    public function ajaxReturn($data, $result = true, $err_code = 0, $type='',$json_option=0) {
        if(empty($type)) $type  =   'JSON';
        if (!$result) {
            $err_code = $err_code == 0 ? -1 : $err_code;
        }
        $data_array = array('result' => $result, 'data' => $data, 'err_code' => $err_code);
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                // header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data_array,JSON_UNESCAPED_UNICODE));
                // exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                // header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data_array));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                // header('Content-Type:application/json; charset=utf-8');
                $handler  =   "jsonpReturn";
                exit($handler.'('.json_encode($data_array,$json_option).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                // header('Content-Type:text/html; charset=utf-8');
                exit($data_array);            
            default     :
                // 用于扩展其他返回格式数据
        }
    }

   /**
     * 析构方法
     * @access public
     */
    public function __destruct() {
    }
}
