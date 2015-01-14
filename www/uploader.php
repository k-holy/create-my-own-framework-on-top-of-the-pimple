<?php
/**
 * Create my own framework on top of the Pimple
 *
 * 画像アップローダ
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

use Symfony\Component\HttpFoundation\JsonResponse;

use Volcanus\FileUploader\Exception\UploaderException;
use Volcanus\FileUploader\Exception\FilenameException;
use Volcanus\FileUploader\Exception\FilesizeException;
use Volcanus\FileUploader\Exception\ExtensionException;
use Volcanus\FileUploader\Exception\ImageWidthException;
use Volcanus\FileUploader\Exception\ImageHeightException;
use Volcanus\FileUploader\File\FileInterface;

$app->on('GET|POST', function($app, $method) {

    $status = null;
    $errors = [];

    $response['results'] = [];

    if ($method === 'POST') {

        do {

            // \Volcanus\FileUploader\File\FileInterface
            $uploadedFile = $app->findFile('upload_file');

            if (false === ($uploadedFile instanceof FileInterface)) {
                break;
            }

            try {

                $uploader = $app->createFileUploader();

                $validator = $app->createFileValidator([
                    'allowableType'    => 'gif,jpg,png',
                    'filenameEncoding' => 'UTF-8',
                    'maxFilesize' => '1M',
                    'maxWidth' => 1200,
                    'maxHeight' => 1200,
                ]);

                $uploader->validate($uploadedFile, $validator);

            } catch (\Exception $e) {
                if ($e instanceof FilenameException) {
                    $errors['filename'] = 'ファイル名が不正です。';
                } elseif ($e instanceof FilesizeException) {
                    $errors['filesize'] = sprintf('ファイルサイズが%sバイトを超えています。', $validator->config('maxFilesize'));
                } elseif ($e instanceof ExtensionException) {
                    $errors['extension'] = sprintf('アップロード可能なファイルは%sです。', $validator->config('allowableType'));
                } elseif ($e instanceof ImageTypeException) {
                    $errors['extension'] = sprintf('画像のファイルフォーマットが拡張子 %s と一致しません。', $uploadedFile->getClientExtension());
                } elseif ($e instanceof ImageWidthException) {
                    $errors['width'] = sprintf('画像の横幅が%spxを超えています。', $validator->config('maxWidth'));
                } elseif ($e instanceof ImageHeightException) {
                    $errors['width'] = sprintf('画像の高さが%spxを超えています。', $validator->config('maxHeight'));
                } else {
                    $errors['upload'] = 'ファイルのアップロードに失敗しました。';
                }
                $status = 400;
                break;
            }

            try {
                $response['results']['data']['dataUri'] = $uploadedFile->getContentAsDataUri();
                $response['results']['file']['name'] = $uploadedFile->getClientFilename();
                $response['results']['file']['mimeType'] = $uploadedFile->getMimeType();
                $response['results']['file']['path'] = $uploader->move($uploadedFile);
            } catch (\Exception $e) {
                $errors['upload'] = 'ファイルのアップロードに失敗しました。';
                $status = 500;
                break;
            }

        } while (false);

    }

    $response['results']['status'] = (isset($status)) ? $status : 200;

    if (!empty($errors)) {
        $response['results']['errors'] = $errors;
    }

$app->logger->addError(sprintf('results:%s', print_r($response['results'], true)));

    return new JsonResponse($response, $response['results']['status']);

});

$app->run();
