<?php

namespace application\models;

use application\core\Model;

//use Imagick;

class Admin extends Model
{
	public $error;

	public function loginValidate($post)
	{
		$config = require_once 'application/config/admin.php';

		if ($config['login'] != $post['login'] || $config['password'] != $post['password']) {
			$this->error = 'Неверный логин и/или пароль';
			return false;
		}

		return true;
	}

	public function postValidate($post, $type)
	{
		$nameLen = mb_strlen($post['name']);
		$descriptionLen = mb_strlen($post['description']);
		$textLen = mb_strlen($post['text']);

		if ($nameLen < 3 || $nameLen > 100) {
			$this->error = 'Название должно содержать от 3 до 100 символов';
			return false;
		}
		elseif ($descriptionLen < 3 || $descriptionLen > 500) {
			$this->error = 'Описание должно содержать от 3 до 100 символов';
			return false;
		}
		
		elseif ($textLen < 10 || $textLen > 10000) {
			$this->error = 'Нажмите кнопку "добавить" еще раз';
			return false;
		}
		

		if (empty($_FILES['img']['tmp_name']) && $type == 'add') {
				$this->error = 'Изображение не выбрано';
				return false;
			}

		return true;
	}

	public function postAdd($post)
	{
		$params = [
			'id' => null,
			'name' => $post['name'],
			'description' => $post['description'],
			'text' => $post['text'],
		];

		$this->db->query('INSERT INTO posts VALUES (:id, :name, :description, :text)', $params);
		return $this->db->lastInsertId();
	}

	public function postEdit($post, $id)
	{
		$params = [
			'id' => $id,
			'name' => $post['name'],
			'description' => $post['description'],
			'text' => $post['text'],
		];

		$this->db->query('UPDATE posts SET name = :name, description = :description, text = :text WHERE id = :id', $params);
	}

	public function postUploadImage($path, $id)
	{
		/*
		$img = new Imagick($path);
		$img->cropThumbnailImage(1080, 600);
		$img->setImageCompressionQuality(80);
		$img->writeImage('public/materials/'.$id.'.jpg');
		*/
		move_uploaded_file($path, 'public/materials/'.$id.'.jpg');
	}

	public function isPostExists($id)
	{
		$params = [
			'id' => $id,
		];

		return $this->db->column('SELECT id FROM posts WHERE id = :id', $params);
	}

	public function postDelete($id)
	{
		$params = [
			'id' => $id,
		];

		$this->db->query('DELETE FROM posts WHERE id = :id', $params);
		unlink('public/materials/'.$id.'.jpg');
	}

	public function postData($id)
	{
		$params = [
			'id' => $id,
		];

		return $this->db->row('SELECT * FROM posts WHERE id = :id', $params);
	}
}