<?php
namespace app\model;
/**
 * @property mixed IdTrajet
 */
class Annotation extends \Illuminate\Database\Eloquent\Model
{
    //Attributs
    protected $table = 'annotation';
    public $timestamps = false;
    protected $primaryKey = 'idAnnotation';


}