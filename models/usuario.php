<?php
class Usuario
{

  private $conn;
  private $tabela = "usuario";

  public $id;
  public $nome;
  public $email;
  public $senha;
  public $foto;

  public function __construct($database)
  {
    $this->conn = $database;
  }

  /********************************************************/

  function buscar_por_id()
  {
    $query = "
      select a.id, a.nome, a.email, a.foto
      from $this->tabela a
      where a.id =  $this->id
      order by a.nome
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    return $stmt;
  }

  public function listar($page, $perPage, $nome, $email)
  {
    $where = " where 1=1 ";

    if ($nome != null) $where .=  " and a.nome like :nome ";
    if ($email != null) $where .=  " and a.email like :email ";

    $query = "
      select a.id, a.nome, a.email, a.foto
      from " . $this->tabela . " a
      $where
      order by a.nome
      limit :page, :perPage
    ";

    $stmt = $this->conn->prepare($query);

    if ($nome != null) {
      $nome = "%" . htmlspecialchars(strip_tags($nome)) . "%";
      $stmt->bindParam(":nome", $nome, PDO::PARAM_STR);
    }
    if ($email != null) {
      $email = "%" . htmlspecialchars(strip_tags($email)) . "%";
      $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    }

    $stmt->bindParam(":page", $page, PDO::PARAM_INT);
    $stmt->bindParam(":perPage", $perPage, PDO::PARAM_INT);

    $stmt->execute();

    return $stmt;
  }

  function cadastrar()
  {
    $query = "
      insert into $this->tabela set
        nome=:nome,
        email=:email,
        senha=:senha
    ";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":nome", $this->nome);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":senha", $this->senha);

    return $stmt->execute();
  }

  function editar()
  {
    $query = "
      update $this->tabela set
        nome=:nome,
        email=:email
      where id = :id
    ";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":nome", $this->nome);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":id", $this->id);

    return $stmt->execute();
  }

  function editar_senha()
  {
    $query = "
      update $this->tabela set
        senha=:senha
      where id = :id
    ";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":senha", $this->senha);
    $stmt->bindParam(":id", $this->id);

    return $stmt->execute();
  }

  function editar_foto()
  {
    $query = "
      update $this->tabela set
        foto=:foto
      where id=:id
    ";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":foto", $this->foto);
    $stmt->bindParam(":id", $this->id);

    return $stmt->execute();
  }

  function excluir($id)
  {
    $query = "delete from $this->tabela where id = ?";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $id);

    return $stmt->execute();
  }

  public function total_linhas()
  {
    $query = "select count(*) as total_linhas from $this->tabela";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $linhas = $stmt->fetch(PDO::FETCH_ASSOC);

    return $linhas['total_linhas'];
  }

  function email_existe($email)
  {
    $query = "select 1 as existe from usuario where email = ? limit 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $email);

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $existe = $row['existe'];

    return !empty($existe) ? true : false;
  }
}
