<?php

class CategoryjoinClass extends ObjectModelCore{

    public $title;
    public $id;
    public $id_category_A;
    public $id_category_B;
    public static $definition=array(
    'table'=>'categoryjoin',
    'primary' =>'id',
    'multilang' => false,
    'fields' =>array(
        'id' =>  array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId','required' => true),
        'title' => array('type' => self::TYPE_STRING,'validate'=>'isGenericName','required' => true),
        'id_category_A' =>  array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId','required' => true),
        'id_category_B' =>  array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId','required' => true),
        )
    );

}