<?php
require "includes/index.php";
require_once "Slim/Slim.php";

$data = new Slim();


$data->get('/books/search',  function () use ($data) {
		$query = $data->request()->params('name');
		findByName($query);
});

$data->get('/books', 'getBooks');
$data->get('/books/:id', 'getBook');
//$data->get('/books/search:query', 'findByName');
$data->post('/books', 'addBook');
$data->put('/books/:id', 'updateBook');
$data->delete('/books/:id',	'deleteBook');


$data->run();

function getBooks() {
	$sql = "select * FROM books ORDER BY id DESC";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$books = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($books);
	
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getBook($id) {
	$query = "SELECT * FROM books WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($query);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$book = $stmt->fetchObject();  
		$db = null;
		echo json_encode($book); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addBook() {
	
	$request = Slim::getInstance()->request();
	$book = json_decode($request->getBody());
	$query = "INSERT INTO books (name, author, status) VALUES (:name, :author, :status)";
	try {
		$db = getConnection();
		$sql = $db->prepare($query);  
		$sql->bindParam("name", $book->name);
		$sql->bindParam("author", $book->author);
		$sql->bindParam("status", $book->status);
		$sql->execute();
		$book->id = $db->lastInsertId();
		$db = null;
		echo json_encode($book); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


function updateBook($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$book = json_decode($body);
	$query = "UPDATE books set name=:name, author=:author, status=:status WHERE id=:id";
	try {
		$db = getConnection();
		$sql = $db->prepare($query);  
		$sql->bindParam("name", $book->name);
		$sql->bindParam("author", $book->author);
		$sql->bindParam("status", $book->status);
		$sql->bindParam("id", $id);
		$sql->execute();
		$db = null;
		echo json_encode($book); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


function deleteBook($id) {
	$query = "DELETE FROM books WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($query);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function findByName($query) {
	$sql = "SELECT * FROM books WHERE UPPER(name) LIKE :query order by name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";  
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$books = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($books);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


?>