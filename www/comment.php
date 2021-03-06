<?php
/**
 * Create my own framework on top of the Pimple
 *
 * 投稿フォーム
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

use Volcanus\FileUploader\File\SplFile;
use Volcanus\FileUploader\Exception\UploaderException;
use Volcanus\FileUploader\Exception\FilenameException;
use Volcanus\FileUploader\Exception\FilesizeException;
use Volcanus\FileUploader\Exception\ExtensionException;
use Volcanus\FileUploader\Exception\ImageWidthException ;
use Volcanus\FileUploader\Exception\ImageHeightException;

$app->on('GET|POST', function($app, $method) {

    $uploaderConfig = [
        'maxWidth'  => '600',
        'maxHeight' => '600',
        'maxFilesize' => '1M',
    ];

    $uploaderConfig['maxFilesizeAsByte'] = $app->createValue('byte',
        sprintf('%sB', $uploaderConfig['maxFilesize'])
    )->getValue();

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

        $uploadedFile = null;

        // 投稿フォーム処理
        if (!$form->image_file_json->isEmpty()) {
            $upload_file_info = json_decode($form->image_file_json->value());
            if (!is_null($upload_file_info)) {
                $form->image_file_name = $upload_file_info->name;
                $form->image_file_path = $upload_file_info->path;
                // mimeType および Base64エンコードデータがあればdataURIをフォームにセットする
                if (file_exists($upload_file_info->path)) {
                    $uploadedFile = new SplFile(new \SplFileInfo($upload_file_info->path));
                    $form->image_file_size = $uploadedFile->getSize();
                    $form->image_encoded_data = base64_encode($uploadedFile->getContent());
                    $form->image_data_uri = $uploadedFile->getContentAsDataUri();
                    $form->image_mime_type = $uploadedFile->getMimeType();
                    if (false !== (list($width, $height, $type, $attr) = getimagesize($upload_file_info->path))) {
                        $form->image_width  = $width;
                        $form->image_height = $height;
                    }
                }
                $fileValidator = $app->createFileValidator([
                    'allowableType' => 'gif,jpg,png',
                    'filenameEncoding' => 'UTF-8',
                    'maxFilesize' => $uploaderConfig['maxFilesize'],
                    'maxWidth' => $uploaderConfig['maxWidth'],
                    'maxHeight' => $uploaderConfig['maxHeight'],
                ]);
                try {
                    $fileValidator->validateFilesize($uploadedFile);
                    $fileValidator->validateExtension($uploadedFile);
                    $fileValidator->validateImageType($uploadedFile);
                    $fileValidator->validateImageSize($uploadedFile);
                } catch (FilesizeException $e) {
                    $form->image_file_path->error(sprintf('画像のファイルサイズが %s バイトを超えています。', $uploaderConfig['maxFilesize']));
                } catch (ExtensionException $e) {
                    $form->image_file_path->error(sprintf('画像のファイルフォーマットが %s 以外です。', $fileValidator->config('allowableType')));
                } catch (ImageTypeException $e) {
                    $form->image_file_path->error(sprintf('画像のファイルフォーマットが拡張子 %s と一致しません。', $uploadedFile->getClientExtension()));
                } catch (ImageWidthException $e) {
                    $form->image_file_path->error(sprintf('画像の横幅が %spx を超えています。', $uploaderConfig['maxWidth']));
                } catch (ImageHeightException $e) {
                    $form->image_file_path->error(sprintf('画像の高さが %spx を超えています。', $uploaderConfig['maxHeight']));
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

                // コメントを登録
                $row = [
                    'author'   => $form->author->value(),
                    'comment'  => $form->comment->value(),
                    'postedAt' => $app->clock,
                ];

                $statement = $app->db->prepare(<<<'SQL'
INSERT INTO comments (
    author
   ,comment
   ,posted_at
) VALUES (
    :author
   ,:comment
   ,:postedAt
)
SQL
                );

                $statement->execute($row);

                $commentId = $app->db->lastInsertId();

                // 画像を登録
                if (isset($uploadedFile)) {

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

                    $imageId = $app->db->lastInsertId();

                    $row = [
                        'commentId' => $commentId,
                        'imageId' => $imageId,
                    ];

                    $statement = $app->db->prepare(<<<'SQL'
INSERT INTO comment_images (
    comment_id
   ,image_id
) VALUES (
    :commentId
   ,:imageId
)
SQL
                    );

                    $statement->execute($row);

                }

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
        'title' => '投稿フォーム',
        'form' => $form,
        'uploaderConfig' => $uploaderConfig,
    ]);
});

$app->run();
