<?php
/**
 * post_category model
 * @package post-category
 * @version 0.0.1
 * @upgrade true
 */

namespace PostCategory\Model;

class PostCategory extends \Model
{
    public $table = 'post_category';
    public $q_field = 'name';
}