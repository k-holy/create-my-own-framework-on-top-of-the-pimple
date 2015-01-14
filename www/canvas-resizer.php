<?php
/**
 * Create my own framework on top of the Pimple
 *
 * Volcanus_CanvasResizer.js + Volcanus_FileUploader
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

use Volcanus\FileUploader\Uploader;
use Volcanus\FileUploader\FileValidator;
use Volcanus\FileUploader\File\NativeFile;
use Volcanus\FileUploader\Exception\UploaderException;
use Volcanus\FileUploader\Exception\FilenameException;
use Volcanus\FileUploader\Exception\FilesizeException;
use Volcanus\FileUploader\Exception\ExtensionException;
use Volcanus\FileUploader\Exception\ImageWidthException;
use Volcanus\FileUploader\Exception\ImageHeightException;

$app->on('GET|POST', function($app, $method) {

	$form = [];
	$errors = [];

	$form['allowableType'] = 'jpg,gif,png';
	$form['maxWidth'] = '600';
	$form['maxHeight'] = '600';
	$form['maxFilesize'] = '2M';
	$form['maxFilesizeAsByte'] = $app->createValue('byte',
		sprintf('%sB', $form['maxFilesize'])
	)->getValue();

	$form['acceptType'] = implode(',', array_map(function($type) {
		switch($type) {
		case 'jpg':
		case 'jpeg':
			return image_type_to_mime_type(IMAGETYPE_JPEG);
		case 'gif':
			return image_type_to_mime_type(IMAGETYPE_GIF);
		case 'png':
			return image_type_to_mime_type(IMAGETYPE_PNG);
		}
	}, explode(',', $form['allowableType'])));

	$form['image_1'] = null;
	$form['image_2'] = null;

	if ($method === 'POST') {

		$uploader = new Uploader([
			'moveDirectory' => $app->config->app_root . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'files',
			'moveRetry'     => 1,
		]);

		$validator = new FileValidator([
			'filenameEncoding' => 'UTF-8',
			'allowableType' => $form['allowableType'],
			'maxWidth' => $form['maxWidth'],
			'maxHeight' => $form['maxHeight'],
			'maxFilesize' => $form['maxFilesize'],
		]);

		$labels = [
			'image_1' => '画像1',
			'image_2' => '画像2',
		];

		$validFiles = [];

		foreach ($labels as $key => $label) {
			if (isset($_FILES[$key])) {
				$uploadedFile = new NativeFile($_FILES[$key]);
				$form[$key]['filename'] = $uploadedFile->getClientFilename();
				$form[$key]['filesize'] = $app->createValue('byte', $uploadedFile->getSize())->format(1);
				$form[$key]['mimeType'] = $uploadedFile->getMimeType();
				if (false !== (list($width, $height, $type, $attr) = getimagesize($uploadedFile->getPath()))) {
					$form[$key]['width'] = $width;
					$form[$key]['height'] = $height;
				}
				try {
					$uploader->validate($uploadedFile, $validator);
					$validFiles[$key] = $uploadedFile;
				} catch (FilenameException $e) {
					$errors[$key] = sprintf('%sのファイル名が不正です。', $label);
				} catch (FilesizeException $e) {
					$errors[$key] = sprintf('%sのファイルサイズが%sバイトを超えています。', $label, $validator->config('maxFilesize'));
				} catch (ExtensionException $e) {
					$errors[$key] = sprintf('%sにアップロード可能なファイルは%sです。', $label, $validator->config('allowableType'));
				} catch (ImageWidthException $e) {
					$errors[$key] = sprintf('%sの横幅が%spxを超えています。', $label, $validator->config('maxWidth'));
				} catch (ImageHeightException $e) {
					$errors[$key] = sprintf('%sの高さが%spxを超えています。', $label, $validator->config('maxHeight'));
				} catch (UploaderException $e) {
					$errors[$key] = sprintf('%sのアップロードに失敗しました。', $label);
				} catch (\Exception $e) {
					$errors[$key] = sprintf('%sのアップロードに失敗しました。', $label);
				}
			}
		}

		if (empty($errors) && !empty($validFiles)) {
			foreach ($validFiles as $key => $uploadedFile) {
				$form[$key]['dataUri'] = $uploadedFile->getContentAsDataUri();
				$form[$key]['movedPath'] = $uploader->move($uploadedFile);
			}
		}

	}

	return $app->render('canvas-resizer.html',
		[
			'title' => 'CanvasResizer',
			'form' => $form,
			'errors' => $errors,
		]
	);

});

$app->run();
