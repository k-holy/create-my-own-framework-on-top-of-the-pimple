<?php
/**
 * Create my own framework on top of the Pimple
 *
 * 投稿フォーム
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

use Volcanus\FileUploader\Exception\UploaderException;
use Volcanus\FileUploader\Exception\FilenameException;
use Volcanus\FileUploader\Exception\FilesizeException;
use Volcanus\FileUploader\Exception\ExtensionException;
use Volcanus\FileUploader\Exception\ImageWidthException ;
use Volcanus\FileUploader\Exception\ImageHeightException;

$app->on('GET|POST', function($app, $method) {

    $form = $app->createForm('commentForm', [
        'author'             => $app->findVar('P', 'author'),
        'comment'            => $app->findVar('P', 'comment'),
        'image_file_json'    => $app->findVar('P', 'image_file_json'),
        'image_file_name'    => $app->findVar('P', 'image_file_name'),
        'image_file_size'    => null,
        'image_file_path'    => null,
        'image_encoded_data' => null,
        'image_data_uri'     => null,
        'image_mime_type'    => null,
        'image_width'        => null,
        'image_height'       => null,
    ]);

    if ($method === 'POST') {

        // CSRFトークンの検証
        if (!$app->csrfVerify('P')) {
            $app->abort(403, 'リクエストは無効です。');
        }

        $fileInfo = null;

        // 投稿フォーム処理
        if (!$form->image_file_json->isEmpty()) {
            $upload_file = json_decode($form->image_file_json->value());
            if (!is_null($upload_file)) {
                $form->image_file_name = $upload_file->name;
                $form->image_file_path = $upload_file->path;
                // mimeType および Base64エンコードデータがあればdataURIをフォームにセットする
                if (file_exists($upload_file->path)) {
                    $fileInfo = new \SplFileinfo($upload_file->path);
                    $getMimeTypeFrom = new \finfo(FILEINFO_MIME_TYPE);
                    $content = file_get_contents($upload_file->path);
                    $form->image_file_size = $fileInfo->getSize();
                    $form->image_encoded_data = base64_encode($content);
                    $form->image_data_uri = sprintf('data:%s;base64,%s', $upload_file->mimeType, $form->image_encoded_data->value());
                    $form->image_mime_type = $getMimeTypeFrom->file($fileInfo->getRealPath());
                    if (false !== (list($width, $height, $type, $attr) = getimagesize($upload_file->path))) {
                        $form->image_width  = $width;
                        $form->image_height = $height;
                    }
                }
                $fileValidator = $app->createFileValidator([
                    'maxFilesize'      => '2M',
                    'allowableType'    => 'gif,jpg,png',
                    'filenameEncoding' => 'UTF-8',
                    'maxWidth'         => 400,
                    'maxHeight'        => 400,
                ]);
                try {
                    $fileValidator->validateFilesize($fileInfo->getSize());
                    $fileValidator->validateExtension($fileInfo->getExtension());
                    $fileValidator->validateImageType($fileInfo->getRealPath(), $fileInfo->getExtension());
                    $fileValidator->validateImageSize($fileInfo->getRealPath());
                } catch (FilesizeException $e) {
                    $form->image_file_path->error(sprintf('画像のファイルサイズが %s バイトを超えています。', $fileValidator->config('maxFilesize')));
                } catch (ExtensionException $e) {
                    $form->image_file_path->error(sprintf('画像のファイルフォーマットが %s 以外です。', $fileValidator->config('allowableType')));
                } catch (ImageTypeException $e) {
                    $form->image_file_path->error(sprintf('画像のファイルフォーマットが拡張子 %s と一致しません。', $fileInfo->getExtension()));
                } catch (ImageWidthException $e) {
                    $form->image_file_path->error(sprintf('画像の横幅が %spx を超えています。', $fileValidator->config('maxWidth')));
                } catch (ImageHeightException $e) {
                    $form->image_file_path->error(sprintf('画像の高さが %spx を超えています。', $fileValidator->config('maxHeight')));
                }
            }
        }

        if ($form->author->isEmpty()) {
            $form->author->error('名前を入力してください。');
        } elseif (mb_strlen($form->author->value()) > 20) {
            $form->author->error('名前は20文字以内で入力してください。');
        }

        if ($form->comment->isEmpty()) {
            $form->comment->error('コメントを入力してください。');
        } elseif (mb_strlen($form->comment->value()) > 50) {
            $form->comment->error('コメントは50文字以内で入力してください。');
        }

        if (!$form->hasError()) {

            $app->transaction->begin();

            try {

                // 画像を登録
                if (isset($fileInfo)) {

                    $row = [
                        'fileName'    => $form->image_file_name->value(),
                        'fileSize'    => $form->image_file_size->value(),
                        'encodedData' => $form->image_encoded_data->value(),
                        'mimeType'    => $form->image_mime_type->value(),
                        'width'       => $form->image_width->value(),
                        'height'      => $form->image_height->value(),
                        'createdAt'   => $app->clock,
                    ];

                    $statement = $app->db->prepare(<<<'SQL'
INSERT INTO images (
    file_name
   ,file_size
   ,encoded_data
   ,mime_type
   ,width
   ,height
   ,created_at
) VALUES (
    :fileName
   ,:fileSize
   ,:encodedData
   ,:mimeType
   ,:width
   ,:height
   ,:createdAt
)
SQL
                    );

                    $statement->execute($row);

                    $row['id'] = $app->db->lastInsertId();

                    $image = $app->createData('image', $row);

                }

                // コメントを登録
                $row = [
                    'author'   => $form->author->value(),
                    'comment'  => $form->comment->value(),
                    'imageId'  => (isset($image)) ? $image->id : null,
                    'postedAt' => $app->clock,
                ];

                $statement = $app->db->prepare(<<<'SQL'
INSERT INTO comments (
    author
   ,comment
   ,image_id
   ,posted_at
) VALUES (
    :author
   ,:comment
   ,:imageId
   ,:postedAt
)
SQL
                );

                $statement->execute($row);

                $row['id'] = $app->db->lastInsertId();

                if (isset($image)) {
                    $row['image'] = $image;
                }

                $comment = $app->createData('comment', $row);

                $app->transaction->commit();

            } catch (\Exception $e) {
                $app->transaction->rollback();
                throw $e;
            }

            $app->flash->addSuccess('投稿を受け付けました');

            return $app->redirect('/');
        }

    }

    return $app->render('comment.html', [
        'title'  => '投稿フォーム',
        'form'   => $form,
    ]);
});

$app->run();
