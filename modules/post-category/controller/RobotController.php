<?php
/**
 * Post category robot provider
 * @package post-category
 * @version 1.0.0
 */

namespace PostCategory\Controller;
use PostCategory\Library\Robot;
use PostCategory\Model\PostCategory as PCategory;

class RobotController extends \SiteController
{
    private function feed($type='xml'){
        if(!module_exists('robot'))
            return $this->show404();
        
        if($type === 'json' && !$this->config->robot['json'])
            return $this->show404();
        
        $feed_router = $type === 'xml' ? 'sitePostCategoryFeedXML' : 'sitePostCategoryFeedJSON';
        $feed_host   = $this->setting->post_category_index_enable ? 'sitePostCategory' : 'siteHome';
        
        $feed = (object)[
            'url'         => $this->router->to($feed_router),
            'description' => hs($this->setting->post_category_index_meta_description),
            'updated'     => null,
            'host'        => $this->router->to($feed_host),
            'title'       => hs($this->setting->post_category_index_meta_title)
        ];
        
        $pages = Robot::feed();
        $this->robot->feed($feed, $pages, $type);
    }
    
    private function feedSingle($slug, $type='xml'){
        if(!module_exists('robot'))
            return $this->show404();
        
        if($type === 'json' && !$this->config->robot['json'])
            return $this->show404();
        
        $category = PCategory::get(['slug'=>$slug], false);
        if(!$category)
            return $this->show404();
        
        $category = \Formatter::format('post-category', $category, false);
        
        $feed_router = $type === 'xml' ? 'sitePostCategorySingleFeedXML' : 'sitePostCategorySingleFeedJSON';
        
        $feed = (object)[
            'url'         => $this->router->to($feed_router, ['slug'=>$category->slug]),
            'description' => hs($category->meta_description->value != '' ? $category->meta_description : $category->about),
            'updated'     => null,
            'host'        => $category->page,
            'title'       => hs($category->name)
        ];
        
        $pages = Robot::feedPost($category);
        $this->robot->feed($feed, $pages, $type);
    }
    
    public function feedXmlAction(){
        $this->feed('xml');
    }
    
    public function feedJsonAction(){
        $this->feed('json');
    }
    
    public function feedSingleXmlAction(){
        $this->feedSingle($this->param->slug, 'xml');
    }
    
    public function feedSingleJsonAction(){
        $this->feedSingle($this->param->slug, 'json');
    }
}