<?php
namespace App\Interfaces;

interface CrudAble {

    public function create(array $data);

    public function read($id);

    public function update($id, array $data);

    public function delete($id);
    
    public function ade(){
       echo "ade";
    }

}
