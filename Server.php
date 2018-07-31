<?php 



class AppServer{


		private $server;

		public function __construct(){
			$this->server = new swoole_http_server("127.0.0.1",9501);
			$this->server->on("start", array($this, "onStart"));
			$this->server->on("request", array($this, "onRequest"));
			$this->server->start();
		}


		function onStart(){
			echo "swoole statr";
		}


		function onRequest($request, $response){
			//请求过滤
            if ('/favicon.ico' == $request->server['path_info'] || '/favicon.ico' == $request->server['request_uri']) {
                return $response->end();
            }
			
			$routeArr = explode("/",$request->server['path_info']);
			$module = $routeArr[1];
			$method = $routeArr[2];
			
            
             ob_start();
             spl_autoload_register(array($this, 'autoload'));
            /*$file = __DIR__."/".$module."/".ucfirst($module)."Controller.php";
            if(file_exists($file)){
            	 require_once $file;
            } 
           */
            $obj = new $module();
            $result = $obj->$method(); 
            $filename=__DIR__."/".$result.".html";
            ob_end_clean();

            $content=file_get_contents($filename);
            $response->end($content);
		}


        function autoload($class){
        	$file = __DIR__."/".$class."/".ucfirst($class)."Controller.php";
        	if(file_exists($file)){
            	require_once $file;
            } 
        }











}

new AppServer();
?>