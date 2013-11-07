<?php
/**
 * Create my own framework on top of the Pimple
 *
 * 画像アップローダ
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

use Symfony\Component\HttpFoundation\JsonResponse;

use Volcanus\FileUploader\Exception\UploaderException;
use Volcanus\FileUploader\Exception\FilenameException;
use Volcanus\FileUploader\Exception\FilesizeException;
use Volcanus\FileUploader\Exception\ExtensionException;

$app->on('GET|POST', function($app, $method) {

    $status = null;
    $errors = [];

    $response['data'] = [];
    $response['file'] = [];

    if ($method === 'POST') {

        do {

            // \Symfony\Component\HttpFoundation\File\UploadedFile
            $uploadedFile = $app->findFile('upload_file');

            try {
                $uploader = $app->createFileUploader($uploadedFile);
                $uploader->validate($app->createFileValidator([
                    'maxFilesize'      => '2M',
                    'allowableType'    => 'gif,jpg,png',
                    'filenameEncoding' => 'UTF-8',
                ]));
            } catch (\Exception $e) {
                if ($e instanceof FilenameException) {
                    $errors['filename'] = 'ファイル名が不正です。';
                } elseif ($e instanceof FilesizeException) {
                    $errors['filesize'] = sprintf('ファイルサイズが%sバイトを超えています。', $uploader->config('maxFilesize'));
                } elseif ($e instanceof ExtensionException) {
                    $errors['extension'] = sprintf('アップロード可能なファイルは%sのみです。', $uploader->config('allowableType'));
                } else {
                    $errors['upload'] = 'ファイルのアップロードに失敗しました。';
                }
                $status = 400;
                break;
            }

            try {
                $mimeType = $uploadedFile->getMimeType();
                $client_filename = $uploader->getClientFilename();
                $moved_path = $uploader->move();
                $content = file_get_contents($moved_path);
                $response['file']['path'] = $moved_path;
                $response['file']['name'] = $client_filename;
                $response['file']['mimeType'] = $mimeType;
                $response['data']['dataUri'] = sprintf('data:%s;base64,%s', $mimeType, base64_encode($content));
            } catch (\Exception $e) {
                $errors['upload'] = 'ファイルのアップロードに失敗しました。';
                $status = 500;
                break;
            }

        } while (false);

    }

    $response['status'] = (isset($status)) ? $status : 200;
    $response['errors'] = (!empty($errors)) ? $errors : null;

    return new JsonResponse($response, $response['status'], [
        'Content-type' => 'text/html; charset=UTF-8', // jQuery.uploadの仕様
    ]);

});

$app->run();
