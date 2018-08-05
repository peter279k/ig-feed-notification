<?php

date_default_timezone_set('Africa/Lagos');

require_once __DIR__  . '/./vendor/autoload.php';
require_once __DIR__ . '/./src/CheckIgPost.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Instagram\Storage\CacheManager;
use Instagram\Api;
use Dotenv\Dotenv;
use IgFeedNotification\CheckPost;

@mkdir('./ig_cache');

$cache = new CacheManager('./ig_cache');
$api   = new Api($cache);
$api->setUserName('esocialpanel');

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

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
        $msg .= '============================' . "<br/>";
        echo $msg;
        break;
    }

    $msg = wordwrap($msg,70);

    $checkPost = new CheckPost();

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
        echo '============================' . "<br/>";

        if (!$checkPost->postIsExisted($media->getId())) {
            // send email
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0; // Set the SMTP Debug mode it can set 0, 1, 2
            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST');  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;        // Enable SMTP authentication
            $mail->Username = getenv('MAIL_USERNAME');  // SMTP username
            $mail->Password = getenv('MAIL_PASSWORD'); // SMTP password
            $mail->SMTPSecure = 'tls';        // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('admin@igclerks.com', 'Mailer');
            $mail->addAddress('dsamsondeen@gmail.com', 'dsamsondeen');

            //Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $media->getCaption();
            $mail->Body    = $msg;
            $mail->AltBody = $media->getLink();

            $mail->send();

            $checkPost->insertPostId($media->getId());
        }
    }

    // And etc...

} catch (\Instagram\Exception\InstagramException $exception) {
    var_dump($exception->getMessage());
}

// Second page
