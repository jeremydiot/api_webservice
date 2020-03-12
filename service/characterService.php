<?php

include_once './dao/characterDao.php';

class CharacterService{
    protected $characterDao;

    public function __construct($connection)
    {
        $this->characterDao = new CharacterDao($connection);
    }

    function findAll(){
        return $this->characterDao->findAll();
    }

    function find($id){

        $stmt = $this->characterDao->find($id);
        $rowCount = $stmt->rowCount();

        if($rowCount > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            $character = new Character();
            $character->character_id = $character_id;
            $character->first_name = $first_name;
            $character->last_name = $last_name;
            $character->hero_name = $hero_name;
            $character->age = $age;
            $character->created = $created;
            $character->modified = $modified;

        }else{
            return null;
        }

        return $character;
    }

    function findAllWithPaging($from_record_num, $records_per_page){
        return $this->characterDao->findAllWithPaging($from_record_num, $records_per_page);
    }

    function count(){
        return $this->characterDao->count();
    }

    function create(Character $character){
        $character->created = date('Y-m-d H:i:s');
        $character->modified = date('Y-m-d H:i:s');

        return $this->characterDao->create($character);
    }

    function update($character){
        $character->modified = date('Y-m-d H:i:s');

        return $this->characterDao->update($character);
    }

    function delete($id){
        return $this->characterDao->delete($id);
    }

    function search($keywords){
        return $this->characterDao->search($keywords);
    }

}