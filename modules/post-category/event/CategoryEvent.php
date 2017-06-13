<?php
/**
 * Post tag events
 * @package post-category
 * @version 0.0.1
 * @upgrade false
 */

namespace PostCategory\Event;

class CategoryEvent{
    
    static function general($object, $old=null){
        $dis = \Phun::$dispatcher;
        $page = $dis->router->to('sitePostCategorySingle', ['slug'=>$object->slug]);
        $dis->cache->removeOutput($page);
    }
    
    static function created($object){
        self::general($object);
    }
    
    static function updated($object, $old=null){
        self::general($object, $old);
    }
    
    static function deleted($object){
        self::general($object);
    }
}