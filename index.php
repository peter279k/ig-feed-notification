<?php

date_default_timezone_set('Africa/Lagos');

require_once __DIR__  . '/./vendor/autoload.php';

use Instagram\Storage\CacheManager;
use Instagram\Api;
use Dotenv\Dotenv;
use IgFeedNotification\CheckPost;
use IgFeedNotification\SendMail;

@mkdir('./ig_cache');

$cache = new CacheManager('./ig_cache');

$userNamesInstruction = [
    'user_name1' => 'Give him 10k Likes',
    'user_name2' => 'Give 10 comments',
];

foreach ($userNamesInstruction as $userName => $instruction) {
    $api   = new Api($cache);

    $api->setUserName($userName);

    $dotenv = new Dotenv(__DIR__);
    $dotenv->load();

    $checkPost = new CheckPost();

    try {
        // First page

        /** @var \Instagram\Hydrator\Component\Feed $feed */
        $feed = $api->getFeed();

        echo '============================' . "<br/>";

        echo 'User Informations : ' . "<br/>";
        echo '============================' . "<br/><br/>";

        echo 'ID        : ' . $feed->getId() . "<br/>";
        echo 'Full Name : ' . $feed->getFullName() . "<br/>";
        echo 'UserName  : ' . $feed->getUserName() . "<br/>";
        echo 'Following : ' . $feed->getFollowing() . "<br/>";
        echo 'Followers : ' . $feed->getFollowers() . "<br/><br/>";

        echo '============================' . "<br/>";
        echo 'Medias first page : ' . "<br/>";
        echo '============================' . "<br/><br/>";

        $msg = '';

        /** @var \Instagram\Hydrator\Component\Media $media */
        foreach ($feed->getMedias() as $media) {
            $msg .= 'User Name : ' . $feed->getUserName() . "<br/>";
            $msg .= 'Caption   : ' . $media->getCaption() . "<br/>";
            $msg .= 'Link      : ' . $media->getLink() . "<br/>";
            $msg .= 'Instructions: ' . $instruction . "<br/>";
            $msg .= '============================' . "<br/>";
            echo $msg;

            $postId = explode('/', $media->getLink());

            if (!$checkPost->postIsExisted($postId[4])) {
                SendMail::sendMail($msg, $media, $feed->getUserName());
                $checkPost->insertPostId($postId[4]);
            }

            break;
        }

        $msg = wordwrap($msg, 70);

        // Second Page

        $api->setEndCursor($feed->getEndCursor());

        sleep(1); // avoir 429 Rate limit from Instagram

        $feed = $api->getFeed();

        echo "<br/><br/>";
        echo '============================' . "<br/>";
        echo 'Medias second page : ' . "<br/>";
        echo '============================' . "<br/><br/>";

        /** @var \Instagram\Hydrator\Component\Media $media */
        foreach ($feed->getMedias() as $media) {
            echo 'ID        : ' . $media->getId() . "<br/>";
            echo 'Caption   : ' . $media->getCaption() . "<br/>";
            echo 'Link      : ' . $media->getLink() . "<br/>";
            echo 'Instructions: ' . $instruction . "<br/>";
            echo '============================' . "<br/>";
        }

        // And etc...

    } catch (\Instagram\Exception\InstagramException $exception) {
        var_dump($exception->getMessage());
    }
}
