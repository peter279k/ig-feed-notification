<?php

namespace IgFeedNotification;

use Illuminate\Database\Capsule\Manager;

class CheckPost
{
    private $capsule;

    private $postRecordTable;

    public function __construct()
    {
        $capsule = new Manager;
        $capsule->addConnection([
            'driver' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'database' => getenv('DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $this->capsule = $capsule;
        $this->postIdTable = $this->capsule->table('post_record');
    }

    public function insertPostId($postId)
    {
        $this->postRecordTable->insert([
            'post_id' => $postId,
        ]);
    }

    public function postIsExisted($postId)
    {
        $result = $this->postRecordTable->select('post_id')
            ->where('post_id', '=', $postId)
            ->get()
            ->toJson();
        $result = json_decode($result, false);

        return count($result) !== 0;
    }
}
