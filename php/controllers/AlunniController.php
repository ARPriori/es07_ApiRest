<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlunniController
{
  // INDEX
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM alunni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  // SHOW
  public function show(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');

    $id = $args['id'];
    $result = $mysqli_connection->query("SELECT * FROM alunni WHERE id = $id");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  // CREATE
  public function create(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    
    $data = json_decode($request->getBody(), true);
    $nome = $data['nome'] ?? null; 
    $cognome = $data['cognome'] ?? null;

    // This is NOT vulnerable to SQL injection!
    if ($nome && $cognome) {
      $stmt = $mysqli_connection->prepare("INSERT INTO alunni (nome, cognome) VALUES (?, ?)");
      $stmt->bind_param("ss", $nome, $cognome);
      $stmt->execute();
      $stmt->close();
    }

    $result = $mysqli_connection->query("SELECT * FROM alunni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  // UPDATE
  public function update(Request $request, Response $response, $args) {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');

    $data = json_decode($request->getBody(), true);
    $nome = $data['nome'] ?? null;
    $cognome = $data['cognome'] ?? null;

    $id = $args['id']; 
    
    if ($nome && $cognome && $id) {
      $stmt = $mysqli_connection->prepare("UPDATE alunni SET nome = ?, cognome = ? WHERE id = ?");
      $stmt->bind_param("ssi", $nome, $cognome, $id);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
          $response->getBody()->write(json_encode(["message" => "Record updated successfully"]));
      } else {
          $response->getBody()->write(json_encode(["message" => "No records were updated."]));
      }
      
      $stmt->close();
    } else {
      $response->getBody()->write(json_encode(["message" => "Missing required fields"]));
    }

    $result = $mysqli_connection->query("SELECT * FROM alunni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  // DESTROY
  public function destroy(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');

    $id = $args['id'];
    $result = $mysqli_connection->query("DELETE FROM alunni WHERE id = $id");

    $result = $mysqli_connection->query("SELECT * FROM alunni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }
}
