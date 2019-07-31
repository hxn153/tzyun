<?php


namespace App\Domain;


class Compute
{
        public function jisuan($number){
            $model=new \App\Model\Compute();
            return $model->jisuan($number);
        }
}
