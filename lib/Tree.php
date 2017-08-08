<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\util;

/**
 * 树形结构类
 * 树的排序和深度的计算
 * 树结点的结构 array('结点编号', '父结点编号', ... )
 * 
 * 术语：
 *   tree       树
 *   subTree    子树
 *   node       结点
 *   degree     结点的度
 *   leaf       叶子，度为0的结点
 *   child      孩子，结点子树的根
 *   parent     结点的上层结点
 *   level      结点的层次
 *   depth      数的深度，树中结点最大层次数
 *   ancestor   祖先，树叶的顶层父结点
 *   descendant 树的子孙，结点的祖先和子孙不包含结点 本身
 *   path       路径，一个结点到达另一个结点经过的结点
 *   
 *   我们这个类没用上sibling、forest
 * 
 * @package     wf.util
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class Tree
{
	/**
	 * 要处理的结点的数组
	 *
	 * @var array
	 */
	protected $inNodes = [];
	
	/**
	 * 处理后的结点的数组
	 *
	 * @var array
	 */
	protected $outNodes = [];
	
	/**
	 * 结点中表示该结点的父结点id的属性名（下标）
	 *
	 * @var string
	 */
	protected $nodeParentIdKey = 'parent_id';
	
	/**
	 * 结点编号属性名（下标）
	 *
	 * @var int
	 */
	protected $nodeIdKey = 'id';
	
	/**
	 * 树的复杂度
	 *
	 * @var int
	 */
	public $step = 0;
	
	/**
	 * 树的深度，树中结点的最大层次数
	 * 
	 * @var int
	 */
	public $depth = 0;
	
	/**
	 * 在构造设置要计算的数组
	 *
	 * @param array $var
	 * @param string $id
	 * @param string $parentId
	 */
	public function __construct(array $vars, $id = 'id', $parentId = 'parent_id')
	{		
		foreach ($vars as $var) {
			$this->inNodes[$var[$id]] = $var;
		}
		
		$this->nodeIdKey = $id;
		$this->nodeParentIdKey = $parentId;
	}

	/**
	 * 获取子结点,子结点将按父结点id和结点id来排序
	 *
	 * @param int $id 结点的id
	 * @param bool $isReturnSelf 是否返回自己
	 * @param array $unReturnFields 设置不返回的属性，可设置：[isLeaf, ancestorIdArr, descendantIdArr, descendantId, childArr, isLastNode, icon]
	 * @return array
	 */
	public function get($id = 0, $isReturnSelf = true, array $unReturnFields = [])
	{
		// 层次排序
		$this->parse(0);
		
		// 设置缩进图标/形状
		if(empty($unReturnFields['icon'])) {
		    $this->parseLevelIcon();
		}
		
		$cats = $this->outNodes;
		if($id && isset($cats[$id])) {
			$cat = $cats[$id];
						
			if (!$isReturnSelf) {
				unset($cats[$id]);
			}
			
			foreach ($cats as $key => $_tmp) {
				if(!in_array($key, $cat['descendantIdArr'])){
					unset($cats[$key]);
				}
			}
		}
		
		if ($unReturnFields) {
		    foreach ($cats as $key => $_tmp) {
		        foreach ($unReturnFields as $field) {
		            unset($cats[$key][$field]);
		        }
		    }
		}
		
		return $cats;
	}
	
	/**
	 * 提取树的层次缩进图标/形状
	 * 
	 */
	private function parseLevelIcon()
	{
	    $nodes  = $this->outNodes;
	    $fidName = $this->nodeParentIdKey;
	    
		// 设置结点在该层是否是最后一个结点
		foreach ($nodes as $k=>$v) {						
			// 修改同父分结点的结点为非最后结点
			foreach ($nodes as $k2 => $v2) {
				// 把和当前遍历到的结点同parent的结点设为非最后结点
				if (isset($nodes[$k2]['isLastNode']) && $nodes[$k2]['isLastNode'] == true && $v2[$fidName] == $v[$fidName]) {					
					$nodes[$k2]['isLastNode'] = false;					
				}
			}
			
			// 设置当前结点为最后结点
			$nodes[$k]['isLastNode'] = true;			
		}
		
		// 设置icon
		foreach ($nodes as $kIcon => $vIcon) {
			if (!$vIcon['level']) {
				continue;
			}
			
			//$icon = $vIcon['isLastNode'] ? '　\--' : '　|--';
			$icon = '&nbsp;|-';
			
			if ($vIcon['level'] == 1) {
				$picon = '';
			} else {
				if ($nodes[$vIcon[$fidName]]['isLastNode']) {
					$picon = substr($nodes[$vIcon[$fidName]]['icon'], 0, -3) . '&nbsp;';
				} else {
					$picon = substr($nodes[$vIcon[$fidName]]['icon'], 0, -3) . '|&nbsp;';
				}				
			}
			
			$nodes[$kIcon]['icon'] = $picon . $icon;
		}
		
		$this->outNodes = $nodes;
	}

	/**
	 * 预排序遍历树
	 *
	 * @param int|string $id 结点id
	 */
	protected function parse($id)
	{
		foreach($this->inNodes as $node) {
			$nodeId   = $node[$this->nodeIdKey];  // 结点ID
			$nodeFid  = $node[$this->nodeParentIdKey];  // 结点的父结点的id 		
			$this->step ++ ;
			
			// 防止死循环（开发的时候需要检查是否把子分类作为分类的父分类）
			if (!empty($this->outNodes[$nodeId])) {
				continue;
			}
			
			if($nodeFid == $id) {
				$this->outNodes[$nodeId] = $node;
				$this->outNodes[$nodeId]['isLeaf'] = true;
				
				// 如果是顶级节点
				if (!$nodeFid) {	
					$this->outNodes[$nodeId]['isTop']             = true;
					$this->outNodes[$nodeId]['level']             = 1;  // 结点的层次，顶层结点层次为1	
					$this->outNodes[$nodeId]['ancestorIdArr'][]   = $nodeId;  // 从结点到根的所有结点id
					$this->outNodes[$nodeId]['descendantIdArr'][] = $nodeId;
					$this->outNodes[$nodeId]['descendantId']      = $nodeId;
					$this->outNodes[$nodeId]['topLevelId']        = $nodeId;  // 结点的顶层祖先结点的id	
					$this->outNodes[$nodeId]['childArr']          = array();  // 子结点id
				} else {
					$this->outNodes[$nodeId]['isTop']             = false;
					$this->outNodes[$nodeId]['level']             = @$this->outNodes[$nodeFid]['level'] + 1;					
					$this->outNodes[$nodeId]['ancestorIdArr']     = array_merge($this->outNodes[$nodeFid]['ancestorIdArr'], array($nodeId));
					$this->outNodes[$nodeId]['topLevelId']        = $this->outNodes[$nodeFid]['topLevelId'];
					$this->outNodes[$nodeId]['childArr']          = array();
					$this->outNodes[$nodeFid]['childArr'][]   = $nodeId;
					$this->outNodes[$nodeFid]['isLeaf']       = false; // 设置父结点为非叶子（子树）	
						
					// 将结点id添加到该结点祖先结点的子孙id列表
					foreach ($this->outNodes[$nodeId]['ancestorIdArr'] as $_nid) {
						$this->outNodes[$_nid]['descendantIdArr'][] = $nodeId;
						$this->outNodes[$_nid]['descendantIdArr'] = array_unique($this->outNodes[$_nid]['descendantIdArr']);
						$this->outNodes[$_nid]['descendantId'] = join(',', $this->outNodes[$_nid]['descendantIdArr']);
					}					
				}
				
				// 把当前分类的id添加到父分类的child
				//$this->addChildMark($nodeId, $nodeFid);
				$this->depth = ($this->depth > $this->outNodes[$nodeId]['level']) ? $this->depth : $this->outNodes[$nodeId]['level'];
				if($nodeId) {
					$this->parse($nodeId);				
				}
				//unset($this->inNodes[$nodeId]);
			}
		}
	}
}

