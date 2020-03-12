<?php
include_once './domain/character.php';
include_once './service/characterService.php';
include_once './shared/pagination.php';


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
                    $res = $this->get_characters($this->keyWords, $this->page);
                } 

                break;

            case 'POST':
                $body = json_decode(file_get_contents('php://input'));

                $character = new Character();
                $character->first_name = $body->first_name;
                $character->last_name = $body->last_name;
                $character->hero_name = $body->hero_name;
                $character->age = $body->age;
                
                $res = $this->set_character($character);

                break;

            case 'PUT':
                $body = json_decode(file_get_contents('php://input'));

                $character = new Character();
                $character->first_name = $body->first_name;
                $character->last_name = $body->last_name;
                $character->hero_name = $body->hero_name;
                $character->age = $body->age;
                
                $res = $this->update_character($this->userId,$character);
                break;

            case 'DELETE':
                $res = $this->delete_character($this->userId);
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
     * @OA\Delete(
     *  path="/api_project/api/characters/{characterId}",
     *  tags={"characters"},
     *  summary="Get all characters",
     *  operationId="getAllCharacters",
     * @OA\Parameter(
     *  name="characterId",
     *  in="path",
     *  description="ID of character",
     *  required=true,
     *  @OA\Schema(
     *      type="integer",
     *      format="int64"
     *      )
     *  ),
     *  @OA\Response(response="200", description="character was deleted"),
     *  @OA\Response(response="503", description="SQL error"),
     *  @OA\Response(response="400", description="wrong data idenfier")
     * )
     */ 
    function delete_character($userId){
        if($userId){

            $res = $this->characterService->delete($userId);

            if($res){
                http_response_code(200);
                $response['body'] = json_encode(array('message'=>'success !'));

            }else{
                http_response_code(503);
                $response['body'] = json_encode(array('message'=>'SQL error'));
            }

        }else{
            http_response_code(400);
            $response['body'] = json_encode(array('message'=>'missing identification data'));
        }

        return $response;
    }

    /**
     * @OA\Put(
     *  path="/api_project/api/characters/{characterId}",
     *  tags={"characters"},
     *  summary="Get all characters",
     *  operationId="getAllCharacters",
     * @OA\Parameter(
     *  name="characterId",
     *  in="path",
     *  description="ID of character",
     *  required=true,
     *  @OA\Schema(
     *      type="integer",
     *      format="int64"
     *      )
     *  ),
     * @OA\RequestBody(
     *      description="Input data format for character",
     *      @OA\MediaType(mediaType="application/json",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="first_name",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="last_name",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="hero_name",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="age",
     *                  type="string"
     *              ),
     *              example={"first_name":"Bruce", "last_name":"Banner", "hero_name":"Hulk", "age":"38"}
     *          )
     *      )
     * ),
     *  @OA\Response(response="200", description="character was updated"),
     *  @OA\Response(response="503", description="SQL error"),
     *  @OA\Response(response="400", description="wrong data identifier")
     * )
     */ 
    function update_character($userId,$character){


        if($userId){

            $res = $this->characterService->update($userId,$character);

            if($res){
                http_response_code(200);
                $response['body'] = json_encode(array('message'=>'success !'));

            }else{
                http_response_code(503);
                $response['body'] = json_encode(array('message'=>'SQL error'));
            }


        }else{
            http_response_code(400);
            $response['body'] = json_encode(array('message'=>'missing identification data'));
        }
        return $response;
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
     *  @OA\Parameter(
     *      name="page",
     *      in="query",
     *      description="number of requested page",
     *      required=false
     *  ),
     *  @OA\Response(response="200", description="There are many caracters"),
     *  @OA\Response(response="204", description="No character found")
     * )
     */ 
    private function get_characters($keyWords, $page){

        if($keyWords){
            $stmt = $this->characterService->search($keyWords);
        }else if($page){
            $rows_per_page = 2;
            $first_row = ($rows_per_page * $page)-$rows_per_page;
            $rows_count = $this->characterService->count();
            $pages = ceil($rows_count/$rows_per_page);
            $url = $_SERVER['SERVER_NAME'].'/api_project/api/characters';

            $stmt = $this->characterService->findAllWithPaging($first_row,$rows_per_page);

            $characters_arr['paging'] = Pagination::getPaging($page,$rows_count,$rows_per_page,$url);
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


    /**
     * @OA\Post(
     * path="/api_project/api/characters",
     * tags={"characters"},
     * summary="Create new character",
     * operationId="setOneCharacter",
     * @OA\RequestBody(
     *      description="Input data format for character",
     *      @OA\MediaType(mediaType="application/json",
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="first_name",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="last_name",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="hero_name",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="age",
     *                  type="string"
     *              ),
     *              example={"first_name":"Bruce", "last_name":"Banner", "hero_name":"Hulk", "age":"38"}
     *          )
     *      )
     * ),
     * @OA\Response(response="201", description="character was created"),
     * @OA\Response(response="503", description="SQL error"),
     * @OA\Response(response="400", description="wrong character format")
     * )
     */
    private function set_character(Character $character){
        if( $character->first_name && 
            $character->last_name && 
            $character->hero_name && 
            $character->age){

                $res = $this->characterService->create($character);

                if($res){
                    http_response_code(201);
                    $response['body'] = json_encode(array('message'=>'success !'));

                }else{
                    http_response_code(503);
                    $response['body'] = json_encode(array('message'=>'SQL error'));

                }

            }else{
                http_response_code(400);
                $response['body'] = json_encode(array('message'=>'wrong character format'));
            }


        return $response;
        
    }
}