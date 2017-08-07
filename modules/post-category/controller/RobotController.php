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

    public function feedAction(){
        if(!module_exists('robot'))
            return $this->show404();
        
        $feed_host   = $this->setting->post_category_index_enable ? 'sitePostCategory' : 'siteHome';
        
        $feed = (object)[
            'url'         => $this->router->to('sitePostCategoryFeed'),
            'description' => hs($this->setting->post_category_index_meta_description),
            'updated'     => null,
            'host'        => $this->router->to($feed_host),
            'title'       => hs($this->setting->post_category_index_meta_title)
        ];
        
        $pages = Robot::feed();
        $this->robot->feed($feed, $pages);
    }
    
    public function feedSingleAction(){
        if(!module_exists('robot'))
            return $this->show404();
        
        $slug = $this->param->slug;
        
        $category = PCategory::get(['slug'=>$slug], false);
        if(!$category)
            return $this->show404();
        
        $category = \Formatter::format('post-category', $category, false);
        
        $feed = (object)[
            'url'         => $this->router->to('sitePostCategorySingleFeed', ['slug'=>$category->slug]),
            'description' => hs($category->meta_description->value != '' ? $category->meta_description : $category->about),
            'updated'     => null,
            'host'        => $category->page,
            'title'       => hs($category->name)
        ];
        
        $pages = Robot::feedPost($category);
        $this->robot->feed($feed, $pages);
    }
}