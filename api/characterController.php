<?php
include_once './domain/character.php';
include_once './service/characterService.php';


class CharacterController{

    private $connection;
    private $requestMethod;
    private $userId;
    private $keyWords;

    private $character;
    private $characterService;


    public function __construct($connection, $requestMethod, $userId, $keyWords, $page)
    {
        $this->connection = $connection;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;
        $this->page = $page;

        $this->keyWords = $keyWords;
        
        $this->character = new Character();
        $this->characterService = new CharacterService($this->connection);
    }

    public function processRequest(){

        switch ($this->requestMethod) {
            case 'GET':

                if($this->userId){
                    $res = $this->get_character($this->userId);
                } 
                else{
                    $res = $this->get_characters($this->keyWords);
                } 

                break;

            case 'POST':
                # code...
                break;

            case 'PUT':
                # code...
                break;

            case 'DELETE':
                # code...
                break;

            default:
                # code...
                break;
        }

        if($res['body']){
            echo $res['body'];
        }
    }

    /**
     * @OA\Get(
     *  path="/api_project/api/characters",
     *  tags={"characters"},
     *  summary="Get all characters",
     *  operationId="getAllCharacters",
     *  @OA\Parameter(
     *      name="s",
     *      in="query",
     *      description="keywords",
     *      required=false
     *  ),
     *  @OA\Response(response="200", description="There are many caracters"),
     *  @OA\Response(response="204", description="No character found")
     * )
     */ 
    private function get_characters($keyWords){


        if($keyWords){
            $stmt = $this->characterService->search($keyWords);
        }else{
            $stmt = $this->characterService->findAll();
        }

        $count = $stmt->rowCount();

        if($count > 0){

            $characters_arr['records'] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                extract($row);
                $character_item = array(
                    'character_id'=>$character_id,
                    'first_name'=>$first_name,
                    'last_name'=>$last_name,
                    'hero_name'=>$hero_name,
                    'age'=>$age,
                    'created'=>$created,
                    'modified'=>$modified
                );

                array_push($characters_arr['records'],$character_item);
            }

            http_response_code(200);
            
            $response['body'] = json_encode($characters_arr);

        }else{
            http_response_code(204);

            $response['body'] = json_encode(array('message'=>'No character found!'));

        }
        return $response;
    }

    /**
     * @OA\Get(
     * path="/api_project/api/characters/{characterId}",
     * tags={"characters"},
     * summary="Get character by Id",
     * operationId="getOneCharacters",
     * @OA\Parameter(
     *  name="characterId",
     *  in="path",
     *  description="ID of character to return",
     *  required=true,
     *  @OA\Schema(
     *      type="integer",
     *      format="int64"
     *      )
     *  ),
     *  @OA\Response(response="200", description="There is one caracter"),
     *  @OA\Response(response="204", description="No character found")
     *  )
     */
    private function get_character($id){

        $character = $this->characterService->find($id);

        if(!is_null($character)){
            http_response_code(200);
            
            $response['body'] = json_encode($character);
        }else{
            http_response_code(204);

            $response['body'] = json_encode(array('message'=>'No character found!'));
        }

        return $response;
    }
}