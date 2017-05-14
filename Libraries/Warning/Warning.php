<?php

	class InternalWarning{

		protected $attr = [];

		protected $config = [];

		public function set(){

			$par = func_get_args();

			$config = $par[count($par)-1];

			$this->config = isset($config) && !empty($config) ? (getType($config)=="array" ? $config : []) : [];

			$this->setConfig();

			if(getType($par[0])=="array")
				$this->arraySet($par[0],$par[1]);
			else
				$this->stringSet($par,$config);

			if($this->config['redirect'])
				$this->redirect();
			else
				$this->get();

		}

		public function get($variable = false){

			$config = $this->getConfig();
			$attr = $this->getAttr();

			if($attr){

				$write = '<div ';
				foreach($config[$attr["type"]] as $key=>$val)
					$write .= " $key='$val' ";
				$write .= '>'.$attr["message"]."</div>";

				$this->clearWarning();

				if($variable)
					echo $write;
				else
					return $write;

			}

		}

		protected function clearWarning(){

			Session::delete("WarningMessage");
			Session::delete("WarningMessageConfig");

		}

		protected function redirect(){

			$config = $this->config;

			if(isset($_SERVER['HTTP_REFERER'])){

				if(empty($this->attr['link']) || $this->attr['link']==$config['link'])
					redirect($_SERVER['HTTP_REFERER'],$config["timer"]);
				else
					redirect($config['redirectType']($url),$config["timer"]);

			}else{

				redirect($config['redirectType']($this->config['link']),$config["timer"]);

			}

		}

		protected function arraySet($attr=[]){


			$attrs['message'] = isset($attr["message"]) && !empty($attr["message"]) ? $attr["message"] : null;
			$attrs['type'] = isset($attr["type"]) && !empty($attr["type"]) ? $attr["type"] : null;
			$attrs['link'] = isset($attr["link"]) && !empty($attr["link"]) ? $attr["link"] : null;
			$attrs['redirect'] = isset($attr["redirect"]) && !empty($attr["redirect"]) ? $attr["redirect"] : true;

			$this->attr = $attrs;
			return $this->setAttr();

		}

		protected function stringSet($attr=[]){

			$attrs['message'] = isset($attr[0]) ? $attr[0] : null;
			$attrs['type'] = isset($attr[1]) ? $attr[1] : null;
			$attrs['link'] = isset($attr[2]) ? $attr[2] : null;
			$attrs['redirect'] = isset($attr[3]) ? $attr[3] : true;

			$this->attr = $attrs;
			return $this->setAttr();

		}

		protected function setAttr(){

			return Session::insert('WarningMessage',$this->attr);

		}

		protected function getAttr(){

			return Session::select("WarningMessage");

		}

		protected function getConfig(){

			return Session::select("WarningMessageConfig");

		}

		protected function setConfig(){

			$config = $this->config;
			$pre = Config::get("ViewObjects","warning");
			$this->config["link"] = isset($config['link']) && !empty($config['link']) ? $config['link'] : $pre['link'];
			$this->config["redirect"] = isset($config['redirect']) && !empty($config['redirect']) ? $config['redirect'] : $pre['redirect'];
			$this->config["preType"] = isset($config['preType']) && !empty($config['preType']) ? $config['preType'] : $pre['preType'];
			$this->config["redirectType"] = isset($config['redirectType']) && !empty($config['redirectType']) ? $config['redirectType'] : $pre['redirectType'];
			$this->config["success"] = isset($config['success']) && !empty($config['success']) ? $config['success'] : $pre['success'];
			$this->config["warning"] = isset($config['warning']) && !empty($config['warning']) ? $config['warning'] : $pre['warning'];
			$this->config["danger"] = isset($config['danger']) && !empty($config['danger']) ? $config['danger'] : $pre['danger'];
			$this->config["info"] = isset($config['info']) && !empty($config['info']) ? $config['info'] : $pre['info'];
			$this->config["timer"] = isset($config['timer']) && !empty($config['timer']) ? $config['timer'] : $pre['timer'];
			Session::insert('WarningMessageConfig',$this->config);
			return $this->config;

		}

		protected function success($message){

			return $this;

		}

		protected function info($message){

			return $this;

		}

		protected function warning($message){

			return $this;

		}

		protected function danger($message){

			return $this;

		}


	}

?>
