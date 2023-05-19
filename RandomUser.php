<?php

class RandomUser
{
    private $apiUrl = 'https://randomuser.me/api/?results=10';

    public function getUsers()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        $users = $data['results'];

        $this->saveUsersToDatabase($users);

        return $users;
    }

    private function saveUsersToDatabase($users)
    {
        $dbHost = 'localhost';
        $dbName = 'random_users';
        $dbUser = 'root';
        $dbPass = '';

        $connection = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);

        foreach ($users as $user) {
            $firstName = $user['name']['first'];
            $lastName = $user['name']['last'];
            $email = $user['email'];
            $phone = $user['phone'];
            $picture = $user['picture']['medium'];

            $query = "INSERT INTO users (first_name, last_name, email, phone, picture) 
                      VALUES (:first_name, :last_name, :email, :phone, :picture)";
            $statement = $connection->prepare($query);
            $statement->bindParam(':first_name', $firstName);
            $statement->bindParam(':last_name', $lastName);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':phone', $phone);
            $statement->bindParam(':picture', $picture);
            $statement->execute();
        }
    }
}
?>