<?php
/**
 * Robot provider
 * @package post-category
 * @version 0.0.1
 * @upgrade true
 */

namespace PostCategory\Library;
use PostCategory\Model\PostCategory as PCategory;
use Post\Model\Post;

class Robot
{
    static function _getCategories(){
        // get all categories that is updated last 2 days
        $last2days = date('Y-m-d H:i:s', strtotime('-2 days'));
        
        $categories = PCategory::get([
            'updated >= :updated',
            'bind' => [
                'updated' => $last2days
            ]
        ], true);
        
        if(!$categories)
            return false;
        
        return \Formatter::formatMany('post-category', $categories, false, ['user']);
    }
    
    static function feed(){
        $result = [];
        
        $categories = self::_getCategories();
        
        if(!$categories)
            return $result;
        
        foreach($categories as $category){
            $desc = $category->meta_description->safe;
            if(!$desc)
                $desc = $category->about->chars(160);
            
            $result[] = (object)[
                'author'      => hs($category->user->fullname),
                'description' => $desc,
                'page'        => $category->page,
                'published'   => $category->created->format('r'),
                'updated'     => $category->updated->format('c'),
                'title'       => $category->name->safe
            ];
        }
        
        return $result;
    }
    
    static function feedPost($category){
        $result = [];
        
        $last2days = date('Y-m-d H:i:s', strtotime('-2 days'));
        
        $posts = Post::getX([
            'category' => $category->id,
            'status'   => 4,
            'updated'  => ['__op', '>=', $last2days]
        ]);
        
        if(!$posts)
            return $result;
        
        $posts = \Formatter::formatMany('post', $posts, false, ['content', 'user', 'category']);
        
        foreach($posts as $post){
            $desc = $post->meta_description->safe;
            if(!$desc)
                $desc = $post->content->chars(160);
            
            $row = (object)[
                'author'      => hs($post->user->fullname),
                'description' => $desc,
                'page'        => $post->page,
                'published'   => $post->created->format('r'),
                'updated'     => $post->updated->format('c'),
                'title'       => $post->title->safe
            ];
            
            if($post->category){
                $row->categories = [];
                foreach($post->category as $cat)
                    $row->categories[] = $cat->name->safe;
            }
            
            $result[] = $row;
        }
        
        return $result;
    }
    
    static function sitemap(){
        $result = [];
        
        $categories = self::_getCategories();
        
        if(!$categories)
            return $result;
        
        $last_update = null;
        foreach($categories as $category){
            $result[] = (object)[
                'url'       => $category->page,
                'lastmod'   => $category->updated->format('Y-m-d'),
                'changefreq'=> 'daily',
                'priority'  => 0.4
            ];
            
            if(is_null($last_update))
                $last_update = $category->updated;
            elseif($last_update < $category->updated)
                $last_update = $category->updated;
        }
        
        $dis = \Phun::$dispatcher;
        if($dis->setting->post_category_index_enable){
            $result[] = (object)[
                'url'       => $dis->router->to('sitePostCategory'),
                'lastmod'   => $last_update->format('Y-m-d'),
                'changefreq'=> 'monthly',
                'priority'  => 0.3
            ];
        }
        
        return $result;
    }
}