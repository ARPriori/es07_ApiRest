<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CertificazioniController
{
  // INDEX
  public function index(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    $result = $mysqli_connection->query("SELECT * FROM certificazioni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  // SHOW
  public function show(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');

    $id = $args['id'];
    $result = $mysqli_connection->query("SELECT * FROM certificazioni WHERE id = $id");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  // CREATE
  public function create(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    
    $data = json_decode($request->getBody(), true);
    $alunno_id = $data['alunno_id'] ?? null; 
    $titolo = $data['titolo'] ?? null;
    $votazione = $data['votazione'] ?? null;
    $ente = $data['ente'] ?? null;

    // This is NOT vulnerable to SQL injection!
    if ($alunno_id && $titolo && $votazione >= 0 && $ente) {
      $stmt = $mysqli_connection->prepare("INSERT INTO certificazioni (alunno_id, titolo, votazione, ente) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("isis", $alunno_id, $titolo, $votazione, $ente);
      $stmt->execute();
      $stmt->close();
    }

    $result = $mysqli_connection->query("SELECT * FROM certificazioni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  // UPDATE
  public function update(Request $request, Response $response, $args) {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');

    $data = json_decode($request->getBody(), true); 
    $titolo = $data['titolo'] ?? null;
    $votazione = $data['votazione'] ?? null;
    $ente = $data['ente'] ?? null;

    $id = $args['id'];

    $out = ["message" => null, "data" => []];
    
    if ($titolo && $votazione >= 0 && $ente) {
      $stmt = $mysqli_connection->prepare("UPDATE certificazioni SET titolo = ?, votazione = ?, ente = ? WHERE id = ?");
      $stmt->bind_param("sisi", $titolo, $votazione, $ente, $id);
      $stmt->execute();

      $out["message"] = ($stmt->affected_rows > 0) ? "Record updated successfully" : "No records were updated.";
      
      $stmt->close();
    } else {
      $out["message"] = "Missing required fields";
    }

    $result = $mysqli_connection->query("SELECT * FROM certificazioni");
    $out["data"] = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    $response->getBody()->write(json_encode($out));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  // DESTROY
  public function destroy(Request $request, Response $response, $args){
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');

    $id = $args['id'];
    $result = $mysqli_connection->query("DELETE FROM certificazioni WHERE id = $id");

    $result = $mysqli_connection->query("SELECT * FROM certificazioni");
    $results = $result->fetch_all(MYSQLI_ASSOC);

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }
}
