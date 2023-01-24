<?php

namespace core\base\models;

use core\base\controllers\BaseMethods;
use core\base\controllers\Singleton;
use core\base\exceptions\AuthExceptions;

class UserModel extends BaseModel
{

    use Singleton;

    use BaseMethods;

    private $cookieName = 'indetifier';

    private $cookieAdminName = 'WQEngineCache';

    private $userData = [];

    private $error;

    private $userTable = 'visitors';

    private $adminTable = 'users';

    private $blockedTable = 'blocked_access';

    public function getAdminTable(){

        return $this->adminTable;

    }

    public function getBlockedTable(){

        return $this->blockedTable;

    }

    public function getLastError(){

        return $this->error;

    }

    public function setAdmin(){

        $this->cookieName = $this->cookieAdminName;

        $this->userTable = $this->adminTable;

        if(!in_array($this->userTable , $this->showTables())){

            $query = 'create table ' . $this->userTable . '
                (
                    id integer auto_increment primary key,
                    name varchar(255) null,
                    login varchar(255) null,
                    password varchar(32) null,
                    credentials text null
                )
                charset = utf8
            ';

            if(!$this->query($query , 'u')){

                exit('Ошибка создания таблицы ' . $this->userTable);

            }

            $this->add($this->userTable , [
                'fields' => ['name' => 'admin' , 'login' =>'admin' , 'password' =>md5('123')]
            ]);

        }



        if(!in_array($this->blockedTable , $this->showTables())){

            $query = 'create table ' . $this->blockedTable . '
                (
                    id integer auto_increment primary key,                
                    login varchar(255) null,
                    ip varchar(255) null,
                    trying tinyint(1) null,
                    time datetime null
                )
                charset = utf8
            ';

            if(!$this->query($query , 'u')){

                exit('Ошибка создания таблицы ' . $this->blockedTable);

            }

        }

    }

    public function checkUser($id = false ,$admin = false ){

        $admin && $this->userTable !== $this->adminTable && $this->setAdmin();

        $method = 'unPackage';

        if($id){

            $this->userData['id'] = $id;

            $method = 'set';

        }

        try{

            $this->$method();

        }catch (AuthExceptions $e){

            $this->error = $e->getMessage();

            !empty($e->getCode()) && $this->writeLog($this->error , 'log_user.txt');

            return false;

        }

        return  $this->userData;

    }

    private function set(){

        $cookieString = $this->package();

        if($cookieString){

            setcookie($this->cookieName, $cookieString, time() + 60*60*24*365*10, PATH);

            return true;

        }

        throw new AuthExceptions('Ошибка формирования cookie' , 1);

    }

    private function package(){

        if(!empty($this->userData['id'])){

            $data['id'] = $this->userData['id'];

            $data['version'] = COOKIE_VERSION;

            $data['cookieTime'] = date('Y-m-d H:i:s');

            return Crypt::instance()->encrypt(json_encode($data));

        }

        throw new AuthExceptions('Неккоректный идентификатор пользователя - ' . $this->userData['id'] , 1);

    }

    private function unPackage(){

        if(empty($_COOKIE[$this->cookieName]))
            throw new AuthExceptions('Отсутствует cookie пользователя');

        $data = json_decode(Crypt::instance()->decrypt($_COOKIE[$this->cookieName]) , true);

        if(empty($data['id']) || empty($data['version']) || empty($data['cookieTime'])){

            $this->logout();

            throw new AuthExceptions('Неккоректные данные в cookie пользователя' , 1);

        }

        $this->validate($data);

        $this->userData = $this->get($this->userTable,[
            'where' => ['id' => $data['id']]
        ]);

        if(!$this->userData){

            $this->logout();
            throw new AuthExceptions('Не найдены данные в таблице ' . $this->userTable . ' по идентификатору ' . $data['id'] , 1 );

        }

        $this->userData = $this->userData[0];

        return true;

    }

    private function validate($data){

        if(!empty(COOKIE_VERSION)) {

            if ($data['version'] !== COOKIE_VERSION) {

                $this->logout();

                throw new AuthExceptions('Некорректная версия cookie');

            }


        }

        if(!empty(COOKIE_TIME)){

            if((new \DateTime()) > (new \DateTime($data['cookieTime']))->modify(COOKIE_TIME . ' minutes')){

                throw new AuthExceptions('Превышено время бездействия пользователя');

            }

        }

    }

    public function logout(){

        setcookie($this->cookieName, '' ,1 , PATH);

    }

}