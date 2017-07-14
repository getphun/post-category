<?php
/**
 * Category controller
 * @package post-category
 * @version 0.0.1
 * @upgrade false
 */

namespace PostCategory\Controller;
use PostCategory\Meta\Category;
use PostCategory\Model\PostCategory as PCategory;
use PostCategory\Model\PostCategoryChain as PTChain;
use Post\Model\Post;

class CategoryController extends \SiteController
{
    public function indexAction(){
        // serve only if it's allowed to be served
        if(!$this->setting->post_category_index_enable)
            return $this->show404();
        
        $page = $this->req->getQuery('page', 1);
        $rpp  = 12;
        
        $cache= 60*60*24*7;
        if($page > 1 || is_dev())
            $cache = null;
        
        $categories = PCategory::get([], $rpp, $page, 'created DESC');
        if(!$categories)
            return $this->show404();
        
        $categories = \Formatter::formatMany('post-category', $categories, false, ['user']);
        $params = [
            'categories' => $categories,
            'index' => new \stdClass(),
            'pagination' => [],
            'total' => PCategory::count()
        ];
        
        $params['index']->meta = Category::index();
        
        // pagination
        if($params['total'] > $rpp)
            $params['pagination'] = calculate_pagination($page, $rpp, $params['total']);
        
        $this->respond('post/category/index', $params, $cache);
    }
    
    public function singleAction(){
        $slug = $this->param->slug;
        
        $category = PCategory::get(['slug'=>$slug], false);
        if(!$category)
            return $this->show404();
            
        $page = $this->req->getQuery('page', 1);
        $rpp = 12;
        
        $cache = 60*60*24*7;
        if($page > 1 || is_dev())
            $cache = null;
        
        $category = \Formatter::format('post-category', $category, ['user']);
        $params = [
            'category' => $category,
            'posts' => [],
            'pagination' => [],
            'total' => Post::countX(['category'=>$category->id, 'status'=>4])
        ];
        
        // pagination
        if($params['total'] > $rpp)
            $params['pagination'] = calculate_pagination($page, $rpp, $params['total']);
        
        $posts = Post::getX(['category'=>$category->id, 'status'=>4], $rpp, $page, 'created DESC');
        if($posts)
            $params['posts'] = \Formatter::formatMany('post', $posts, false, false);
        
        $params['category']->meta = Category::single($category);
        
        $this->respond('post/category/single', $params, $cache);
    }
}