<?php

class Article extends \Phalcon\Mvc\Model
{
	public function initialize($ctime = false){
		//$this->hasOne('id', 'Category', 'division_category_id');
		$this->hasOne("division_category_id", "Category", "id");//{0}主表关联子表的ID，{1}关联的子表，{2}子表ID
		$this->skipAttributes($ctime ?  array('from', 'mtime') : array('from', 'ctime', 'mtime'));
	}
	
	/**
	 * 获取文章列表数据
	 * @param unknown_type $modelsManager
	 * @param unknown_type $where
	 * @param unknown_type $appendix
	 */
	static function getList($modelsManager , $where , $appendix = array()){
		$num = isset($appendix['pageSize'])  ? $appendix['pageSize'] : 10;
		$page = isset($appendix['page']) ? $appendix['page'] : 1;
		
		$builder = $modelsManager->createBuilder()
					->columns("*")//->columns("article.*,category.name cname")//连接查询的表中有相同名称的字段不能使用as别名的方式，否则用相同名称的字段进行条件筛选时会报错
					->from("article");
		$strWhere = null;
		if($where){
			$divisionCategoryId = array();
			if(isset($where['division_category_id']) && $where['division_category_id']){
				/*$phql = "SELECT category.id FROM category where category.parent_id={$where['division_category_id']}";
				$categoryId = $modelsManager->executeQuery($phql);
				//echo '<pre>';
				foreach ($categoryId as $category){
					$divisionCategoryId[] = $category->id;
				}*/
				//print_r($divisionCategoryId);exit;
				//$divisionCategoryId = Category::selectCategoryId($modelsManager, $where['division_category_id']);
				Category::getSubIds($where['division_category_id'], $divisionCategoryId);
			}
			foreach($where as $k=>$v){
				if($k=='title'){
					$strWhere[]  =  "article.{$k} LIKE  '%{$v}%'";
				}
				elseif($k=='bctime'){
					$strWhere[] = "article.ctime >= '{$v} 00:00:00'";
				}
				elseif($k=='ectime'){
					$strWhere[] = "article.ctime <= '{$v} 23:59:59'";
				}
				elseif($k=='division_category_id'){
					$categoryIdStr = implode(',', $divisionCategoryId);
					$str = "article.division_category_id={$v}";
					if($categoryIdStr){
						$str = "({$str} or article.division_category_id in ({$categoryIdStr}))";
					}
					$strWhere[] = $str;
				}
				else{
					$strWhere[]  =  "article.{$k} = '{$v}'";
				}
			}
			$strWhere = implode(' AND ', $strWhere);
		}
		$builder = $builder->where($strWhere)
					//->leftjoin("category", "category.id = article.division_category_id")
					->orderby($appendix['order']);
		//echo '<pre>';print_r($builder);exit;
		return $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
			    "builder" => $builder,
			    "limit"=> $num,
				"page" => $page
			));
	}
	
	/**
	 * 获取单条文章数据
	 * @param unknown_type $id
	 */
	static function getOne($id){
		if($id){
			$conditions = " id= :id: ";
			$parameters = array("id"=>$id);
			$articles = Article::find(array($conditions, 'bind' => $parameters));
			//echo '<pre>';print_r($article);exit;
			$article = array();
			foreach ($articles as $article){
				$article = (array)$article;
			}
			return (object)$article;
		}
		return null;
	}
	
	/**
	 * 删除文章
	 * @param unknown_type $modelsManager
	 * @param unknown_type $ids
	 */
	static function deleteArticle($modelsManager, $ids, $act){
		if(!empty($ids)){
			//$parameters = array('id'=>$ids);
			$phql = '';
			if($act=='hidden'){
				$phql = "UPDATE article SET article.visibility='Hidden' WHERE article.id in ({$ids})";
			}
			elseif($act=='show'){
				$phql = "UPDATE article SET article.visibility='Visible' WHERE article.id in ({$ids})";
			}
			elseif($act=='delete'){
				$phql = "UPDATE article SET article.status='Disabled' WHERE article.id in ({$ids})";
			}
			$status = $modelsManager->executeQuery($phql);
		    return $status;
		}
		return false;
	}
	
	/**
	 * 更新文章
	 * @param unknown_type $modelsManager
	 * @param unknown_type $new
	 * @param unknown_type $where
	 */
	static function modifyArticle($modelsManager, $new = array(), $where = array()){
		$newVal = null;
		if($new){
			foreach($new as $k => $v){
				$newVal[] = "article.{$k} = '{$v}'";
			}
			$newVal = implode(',', $newVal);
		}
		$strWhere = null;
		if($where){
			foreach ($where as $k => $v){
				$strWhere[] = "article.{$k} = '{$v}'";
			}
			$strWhere = implode(' AND ', $strWhere);
		}
		if($newVal && $strWhere){
			$phql = "UPDATE article SET {$newVal} WHERE {$strWhere}";
			$status = $modelsManager->executeQuery($phql);
		    //print_r($status);exit;
		    return $status;
		}
		return false;
	}

	/*public function beforeSave(){
    	if($this->title==''){
    		$text = "标题不能为空";
	        $field = "title";
	        $type = "InvalidValue";
	        $code = 103;
	        $message = new Message($text, $field, $type, $code);
	        $this->appendMessage($message);
    	}
    	if($this->division_category_id==''){
    		$text = "请选择分类";
	        $field = "division_category_id";
	        $type = "InvalidValue";
	        $code = 103;
	        $message = new Message($text, $field, $type, $code);
	        $this->appendMessage($message);
    	}
    	if($this->language==''){
    		$text = "请选择语言";
	        $field = "language";
	        $type = "InvalidValue";
	        $code = 103;
	        $message = new Message($text, $field, $type, $code);
	        $this->appendMessage($message);
    	}
    	if($this->share==''){
    		$text = "请设置是否分享";
	        $field = "share";
	        $type = "InvalidValue";
	        $code = 103;
	        $message = new Message($text, $field, $type, $code);
	        $this->appendMessage($message);
    	}
    	if($this->visibility==''){
    		$text = "请设置是否可见";
	        $field = "visibility";
	        $type = "InvalidValue";
	        $code = 103;
	        $message = new Message($text, $field, $type, $code);
	        $this->appendMessage($message);
    	}
    	if($this->content==''){
    		$text = "文章内容不能为空";
	        $field = "content";
	        $type = "InvalidValue";
	        $code = 103;
	        $message = new Message($text, $field, $type, $code);
	        $this->appendMessage($message);
    	}
    }*/
}
?>