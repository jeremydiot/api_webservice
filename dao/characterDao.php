<?php

class CharacterDao{

    private $connection;
    private $table_name = 'characters';

    private $column_character_id = 'character_id';
    private $column_first_name = 'first_name';
    private $column_last_name = 'last_name';
    private $column_hero_name = 'hero_name';
    private $column_age = 'age';
    private $column_created = 'created';
    private $column_modified = 'modified';

    function __construct($connection)
    {
        $this->connection = $connection;
    }

    function findAll(){
        $query = 'SELECT * FROM '.$this->table_name.' ORDER BY '.$this->column_created.' DESC;';

        $stmt = $this->connection->prepare($query);

        $stmt->execute();

        return $stmt;
    }


    function find($id){
        $query = 'SELECT * FROM '.$this->table_name.' WHERE '.$this->column_character_id.' = '.$id.';';

        $stmt = $this->connection->prepare($query);

        $stmt->execute();

        return $stmt;
    }


    function search($keywords){


        $query = 'SELECT * FROM '.$this->table_name.' WHERE '.$this->column_first_name.' LIKE ? OR '.$this->column_last_name.' LIKE ? OR '.$this->column_hero_name.' LIKE ? ORDER BY '.$this->column_created.' DESC ;';

        $stmt = $this->connection->prepare($query);

        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);

        $stmt->execute();

        return $stmt;
    }

    function findAllWithPaging($from_record_num, $records_per_page){
        $query = 'SELECT * FROM '.$this->table_name.' ORDER BY '.$this->column_created.' DESC LIMIT ?, ?;';

        $stmt = $this->connection->prepare($query);

        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt;
    }

    function count(){
        $query = 'SELECT COUNT(*) as total_rows FROM '.$this->table_name.';';

        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total_rows'];
    }

    function create(Character $character){

        $query = 'INSERT INTO '.$this->table_name.' ('
        .$this->column_first_name.', '
        .$this->column_last_name.', '
        .$this->column_hero_name.', '
        .$this->column_age.', '
        .$this->column_created.', '
        .$this->column_modified.')'
        .' VALUES (? ,? ,? ,? ,? ,?);';

        $stmt = $this->connection->prepare($query);

        $stmt->bindParam(1, $character->first_name);
        $stmt->bindParam(2, $character->last_name);
        $stmt->bindParam(3, $character->hero_name);
        $stmt->bindParam(4, $character->age);
        $stmt->bindParam(5, $character->created);
        $stmt->bindParam(6, $character->modified);

        return $stmt->execute();
    }

    function update(Character $character){

        if(!$character->character_id) return false;
        

        $query ='UPDATE '.$this->table_name.' SET ';

        if($character->first_name) $query = $query.$this->column_first_name.' = '.$character->first_name.', ';
        if($character->last_name) $query = $query.$this->column_last_name.' = '.$character->last_name.', ';
        if($character->hero_name) $query = $query.$this->column_hero_name.' = '.$character->hero_name.', ';
        if($character->age) $query = $query.$this->column_age.' = '.$character->age.', ';
        if($character->modified) $query = $query.$this->column_modified.' = '.$character->modified.', ';

        //     .' SET '.$this->column_first_name.' = ?,'
        //     .' SET '.$this->column_first_name.' = ?,'
        //     .' SET '.$this->column_first_name.' = ?,'
        //     .' SET '.$this->column_first_name.' = ?,'
        //     .' SET '.$this->column_first_name.' = ?,'
        //     .' SET '.$this->column_first_name.' = ?,'
        //     .' SET '.$this->column_first_name.' = ?,'
        //     .' SET '.$this->column_first_name.' = ?,'
            
            
            
            ;
    }

    function delete($id){

    }

}